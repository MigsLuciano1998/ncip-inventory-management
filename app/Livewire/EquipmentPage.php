<?php

namespace App\Livewire;

use App\Livewire\Equipment\EquipmentModal;
use App\Models\Equipment;
use App\Models\EquipmentMovementLog;
use App\Models\Office;
use App\Models\Province;
use App\Models\Region;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class EquipmentPage extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedRegion = '';

    public $selectedProvince = '';

    public $selectedOffice = '';

    public $selectedClassification = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedOffice()
    {
        $this->resetPage();
    }

    public function updatingSelectedClassification()
    {
        $this->resetPage();
    }

    public function updatedSelectedRegion()
    {
        $this->selectedProvince = '';
        $this->selectedOffice = '';
        $this->resetPage();
    }

    public function updatedSelectedProvince()
    {
        $this->selectedOffice = '';
        $this->resetPage();
    }

    public function openAddEquipment()
    {
        $this->dispatch('openAddEquipment')->to(EquipmentModal::class);
    }

    public function openEditEquipment($equipmentId)
    {
        $this->dispatch('openEditEquipment', equipmentId: $equipmentId)->to(EquipmentModal::class);
    }

    #[On('equipmentSaved')]
    public function refreshEquipment(?string $message = null)
    {
        if ($message) {
            session()->flash('message', $message);
        }

        $this->resetPage();
    }

    public function delete($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);

        if ($equipment->activeAssignment) {
            session()->flash('error', 'Cannot delete equipment that is currently assigned to an employee. Return it first.');

            return;
        }

        EquipmentMovementLog::record([
            'equipment_id' => $equipment->id,
            'action' => EquipmentMovementLog::ACTION_DELETED,
            'office_id' => $equipment->office_id,
            'remarks' => 'Equipment removed from inventory.',
        ]);

        $equipment->delete();

        session()->flash('message', 'Equipment deleted successfully.');
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

        $equipments = Equipment::with([
            'equipmentCategory',
            'equipmentType',
            'office.province',
            'activeAssignment',
        ])
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
            })
            ->orderBy('property_no')
            ->paginate(10);

        return view('livewire.equipment-page', compact(
            'regions',
            'provinces',
            'offices',
            'equipments'
        ));
    }
}
