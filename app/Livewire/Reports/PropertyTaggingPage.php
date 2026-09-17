<?php

namespace App\Livewire\Reports;

use App\Models\Equipment;
use App\Models\Office;
use App\Models\Province;
use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyTaggingPage extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedRegion = '';

    public $selectedProvince = '';

    public $selectedOffice = '';

    public $selectedClassification = '';

    public array $selectedIds = [];

    public array $printIds = [];

    public $printError = '';

    public $checkAll = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->printIds = [];
    }

    public function updatingSelectedOffice(): void
    {
        $this->resetPage();
        $this->printIds = [];
    }

    public function updatingSelectedClassification(): void
    {
        $this->resetPage();
        $this->printIds = [];
    }

    public function updatedSelectedRegion(): void
    {
        $this->selectedProvince = '';
        $this->selectedOffice = '';
        $this->resetPage();
        $this->printIds = [];
        $this->refreshCheckAllSelection();
    }

    public function updatedSelectedProvince(): void
    {
        $this->selectedOffice = '';
        $this->resetPage();
        $this->printIds = [];
        $this->refreshCheckAllSelection();
    }

    public function updatedSearch(): void
    {
        $this->refreshCheckAllSelection();
    }

    public function updatedSelectedOffice(): void
    {
        $this->refreshCheckAllSelection();
    }

    public function updatedSelectedClassification(): void
    {
        $this->refreshCheckAllSelection();
    }

    public function updatedCheckAll($value): void
    {
        $this->selectedIds = $value ? $this->filteredIds() : [];
        $this->printError = '';
    }

    public function updatedSelectedIds(): void
    {
        $selected = collect($this->selectedIds)->map(fn ($id) => (string) $id)->unique()->sort()->values();
        $all = collect($this->filteredIds())->sort()->values();

        $this->checkAll = $all->isNotEmpty() && $selected->values()->all() === $all->all();
        $this->printError = '';
    }

    public function printSelected(): void
    {
        $this->printError = '';

        $ids = collect($this->selectedIds)
            ->map(fn ($id) => is_numeric($id) ? (int) $id : null)
            ->filter(fn ($id) => $id !== null)
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            $this->printIds = [];
            $this->printError = 'Select at least one item to print.';

            return;
        }

        $this->printIds = $ids;
        $this->js('setTimeout(() => window.print(), 150)');
    }

    protected function refreshCheckAllSelection(): void
    {
        if ($this->checkAll) {
            $this->selectedIds = $this->filteredIds();
        }
    }

    protected function filteredIds(): array
    {
        return $this->filteredQuery()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    protected function filteredQuery()
    {
        return Equipment::query()
            ->when($this->selectedOffice, function ($q) {
                $q->where('office_id', $this->selectedOffice);
            })
            ->when($this->selectedClassification, function ($q) {
                $q->where('classification', $this->selectedClassification);
            })
            ->when($this->selectedProvince, function ($q) {
                $q->whereHas('office', function ($office) {
                    $office->where('province_id', $this->selectedProvince);
                });
            })
            ->when($this->selectedRegion, function ($q) {
                $q->whereHas('office.province', function ($province) {
                    $province->where('region_id', $this->selectedRegion);
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('property_no', 'like', "%{$this->search}%")
                        ->orWhere('serial_no', 'like', "%{$this->search}%")
                        ->orWhere('brand', 'like', "%{$this->search}%")
                        ->orWhere('model', 'like', "%{$this->search}%")
                        ->orWhere('tag', 'like', "%{$this->search}%")
                        ->orWhere('uacs_object_code', 'like', "%{$this->search}%")
                        ->orWhere('item_id', 'like', "%{$this->search}%");
                });
            });
    }

    public function render()
    {
        $regions = Region::orderBy('region_short_name')->get();

        $provinces = Province::when($this->selectedRegion, function ($q) {
            $q->where('region_id', $this->selectedRegion);
        })->orderBy('province_name')->get();

        $offices = Office::when($this->selectedProvince, function ($q) {
            $q->where('province_id', $this->selectedProvince);
        })->orderBy('office_name')->get();

        $equipments = $this->equipmentQuery()
            ->orderBy('property_no')
            ->paginate(10);

        $printEquipment = $this->printIds !== []
            ? Equipment::with([
                'equipmentType',
                'office.province',
                'activeAssignment.employee',
            ])
                ->whereIn('id', $this->printIds)
                ->orderBy('property_no')
                ->get()
            : collect();

        return view('livewire.reports.property-tagging-page', [
            'regions' => $regions,
            'provinces' => $provinces,
            'offices' => $offices,
            'equipments' => $equipments,
            'printEquipment' => $printEquipment,
        ]);
    }

    protected function equipmentQuery()
    {
        return $this->filteredQuery()->with([
            'equipmentCategory',
            'equipmentType',
            'office.province',
            'activeAssignment.employee',
        ]);
    }
}
