<?php

namespace App\Livewire\Reports;

use App\Models\Equipment;
use App\Models\PpeFoundAtStation;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class PpeInStationsPage extends Component
{
    use WithPagination;

    public $search = '';

    public bool $showFound = true;

    public bool $showMissing = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingShowFound(): void
    {
        $this->resetPage();
    }

    public function updatingShowMissing(): void
    {
        $this->resetPage();
    }

    public function markStation(int $equipmentId, string $status): void
    {
        if (! in_array($status, [PpeFoundAtStation::STATUS_FOUND, PpeFoundAtStation::STATUS_MISSING], true)) {
            return;
        }

        $equipment = $this->ppeBaseQuery()->whereKey($equipmentId)->first();

        if (! $equipment) {
            return;
        }

        PpeFoundAtStation::updateOrCreate(
            ['equipment_id' => $equipment->id],
            [
                'status' => $status,
                'dates' => now()->toDateString(),
            ]
        );
    }

    public function render()
    {
        $query = $this->filteredPpeQuery();

        return view('livewire.reports.ppe-in-stations-page', [
            'equipments' => (clone $query)
                ->orderBy('property_no')
                ->paginate(10),
            'printItems' => (clone $query)
                ->orderBy('property_no')
                ->get(),
        ]);
    }

    protected function ppeBaseQuery(): Builder
    {
        return Equipment::query()
            ->with([
                'equipmentType',
                'ppeCategory',
                'equipmentCategory',
                'activeAssignment.employee',
                'stationRecord',
            ])
            ->where(function (Builder $q) {
                $q->where('classification', Equipment::CLASSIFICATION_PPE)
                    ->orWhereRaw("CAST(REPLACE(REPLACE(IFNULL(cost, '0'), ',', ''), ' ', '') AS REAL) > 49999");
            });
    }

    protected function filteredPpeQuery(): Builder
    {
        return $this->ppeBaseQuery()
            ->when(! $this->showFound && ! $this->showMissing, function (Builder $q) {
                $q->whereRaw('0 = 1');
            })
            ->when($this->showFound && ! $this->showMissing, function (Builder $q) {
                $q->whereHas('stationRecord', function (Builder $station) {
                    $station->where('status', PpeFoundAtStation::STATUS_FOUND);
                });
            })
            ->when($this->showMissing && ! $this->showFound, function (Builder $q) {
                $q->where(function (Builder $inner) {
                    $inner->whereDoesntHave('stationRecord')
                        ->orWhereHas('stationRecord', function (Builder $station) {
                            $station->where('status', PpeFoundAtStation::STATUS_MISSING);
                        });
                });
            })
            ->when(trim($this->search) !== '', function (Builder $q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function (Builder $query) use ($term) {
                    $query->where('property_no', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('brand', 'like', $term)
                        ->orWhere('model', 'like', $term)
                        ->orWhere('item_id', 'like', $term)
                        ->orWhere('remarks', 'like', $term)
                        ->orWhereHas('equipmentType', fn (Builder $type) => $type->where('name', 'like', $term))
                        ->orWhereHas('ppeCategory', fn (Builder $category) => $category->where('title', 'like', $term))
                        ->orWhereHas('activeAssignment.employee', function (Builder $employee) use ($term) {
                            $employee->where('first_name', 'like', $term)
                                ->orWhere('middle_name', 'like', $term)
                                ->orWhere('last_name', 'like', $term);
                        });
                });
            });
    }
}
