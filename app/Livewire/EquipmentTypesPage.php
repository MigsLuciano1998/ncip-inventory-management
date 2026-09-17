<?php

namespace App\Livewire;

use App\Models\EquipmentCategory;
use App\Models\EquipmentType;
use Livewire\Component;

class EquipmentTypesPage extends Component
{
    public $name;

    public $equipment_category_id;

    public $status = true;

    public $equipmentTypeId;

    public $isEdit = false;

    public $showModal = false;

    public function openAdd()
    {
        $this->resetForm();
        $this->equipment_category_id = EquipmentCategory::query()
            ->where('short_name', 'ICT')
            ->value('id');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'name' => 'required|string|max:255',
        ]);

        EquipmentType::updateOrCreate(
            ['id' => $this->equipmentTypeId],
            [
                'equipment_category_id' => $this->equipment_category_id,
                'name' => $this->name,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('message', 'Equipment type saved successfully.');
    }

    public function edit($id)
    {
        $equipmentType = EquipmentType::findOrFail($id);

        $this->equipmentTypeId = $equipmentType->id;
        $this->equipment_category_id = $equipmentType->equipment_category_id;
        $this->name = $equipmentType->name;
        $this->status = $equipmentType->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $equipmentType = EquipmentType::findOrFail($id);

        if ($equipmentType->equipment()->exists()) {
            session()->flash('error', 'Cannot delete a type that is assigned to equipment.');

            return;
        }

        $equipmentType->delete();

        session()->flash('message', 'Equipment type deleted.');
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'equipment_category_id',
            'equipmentTypeId',
            'isEdit',
        ]);

        $this->status = true;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.equipment-types-page', [
            'equipmentTypes' => EquipmentType::with('equipmentCategory')
                ->orderBy('name')
                ->get(),
            'equipmentCategories' => EquipmentCategory::where('status', 1)
                ->orderBy('title')
                ->get(),
        ]);
    }
}
