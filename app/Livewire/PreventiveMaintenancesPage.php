<?php

namespace App\Livewire;

use App\Models\PreventiveMaintenance;
use Livewire\Component;
use Livewire\WithPagination;

class PreventiveMaintenancesPage extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $selectedPmId;

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedPmId = null;
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
        $this->selectedPmId = null;
    }

    public function viewPm($pmId): void
    {
        $this->selectedPmId = (int) $pmId;
    }

    public function closePmDetail(): void
    {
        $this->selectedPmId = null;
    }

    public function delete($pmId)
    {
        PreventiveMaintenance::findOrFail($pmId)->delete();

        session()->flash('message', 'PM log deleted successfully.');
    }

    public function render()
    {
        $pmLogs = PreventiveMaintenance::with(['equipment.equipmentType', 'equipment.office'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('equipment', function ($equipment) {
                        $equipment->where('property_no', 'like', "%{$this->search}%")
                            ->orWhere('serial_no', 'like', "%{$this->search}%")
                            ->orWhere('brand', 'like', "%{$this->search}%");
                    });
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('date_inspected')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.preventive-maintenances-page', [
            'pmLogs' => $pmLogs,
            'selectedPm' => $this->selectedPmId
                ? PreventiveMaintenance::with(['equipment.equipmentType', 'equipment.office'])->find($this->selectedPmId)
                : null,
        ]);
    }
}
