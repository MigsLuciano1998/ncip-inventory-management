<?php

namespace App\Livewire\Equipment;

use App\Models\Equipment;
use App\Models\EquipmentDepreciation;
use Livewire\Component;

class IiruppFormPage extends Component
{
    public Equipment $equipment;

    public $accumulated_depreciation_beginning;

    public $accumulated_depreciation_end;

    public $book_value;

    public $nature_of_repair;

    public $repair_cost;

    public $authority;

    public $use_of_depreciation_date;

    public $salvage_value;

    public $start_date_of_depreciation;

    public $status_of_depreciation;

    public function mount(Equipment $equipment)
    {
        $this->equipment = $equipment->load(['equipmentType', 'office', 'depreciation']);

        if ($this->equipment->depreciation) {
            $this->redirect(route('equipment.show', $this->equipment), navigate: false);
        }
    }

    public function save()
    {
        if ($this->equipment->depreciation()->exists()) {
            session()->flash('error', 'An IIRUPP record already exists for this equipment.');

            return $this->redirect(route('equipment.show', $this->equipment), navigate: false);
        }

        $validated = $this->validate([
            'accumulated_depreciation_beginning' => 'nullable|numeric|min:0',
            'accumulated_depreciation_end' => 'nullable|numeric|min:0',
            'book_value' => 'nullable|numeric|min:0',
            'nature_of_repair' => 'nullable|string|max:2000',
            'repair_cost' => 'nullable|numeric|min:0',
            'authority' => 'nullable|string|max:255',
            'use_of_depreciation_date' => 'nullable|date',
            'salvage_value' => 'nullable|numeric|min:0',
            'start_date_of_depreciation' => 'nullable|date',
            'status_of_depreciation' => 'nullable|string|max:255',
        ]);

        EquipmentDepreciation::create([
            'equipment_id' => $this->equipment->id,
            ...collect($validated)->map(fn ($value) => $value === '' ? null : $value)->all(),
        ]);

        session()->flash('message', 'IIRUPP record saved for '.$this->equipment->property_no.'.');

        return $this->redirect(route('equipment.show', $this->equipment), navigate: false);
    }

    public function render()
    {
        return view('livewire.equipment.iirupp-form-page');
    }
}
