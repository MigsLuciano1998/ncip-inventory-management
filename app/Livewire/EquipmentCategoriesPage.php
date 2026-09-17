<?php

namespace App\Livewire;

use App\Models\EquipmentCategory;
use Livewire\Component;

class EquipmentCategoriesPage extends Component
{
    public $title;

    public $short_name;

    public $status = true;

    public $date;

    public $equipmentCategoryId;

    public $isEdit = false;

    public $showModal = false;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function openAdd()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:150|unique:equipment_categories,title,' . $this->equipmentCategoryId,
            'short_name' => 'required|string|max:20',
            'status' => 'required|boolean',
            'date' => 'required|date',
        ]);

        EquipmentCategory::updateOrCreate(
            ['id' => $this->equipmentCategoryId],
            [
                'title' => $this->title,
                'short_name' => strtoupper($this->short_name),
                'status' => $this->status,
                'date' => $this->date,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('message', 'Equipment category saved successfully.');
    }

    public function edit($id)
    {
        $category = EquipmentCategory::findOrFail($id);

        $this->equipmentCategoryId = $category->id;
        $this->title = $category->title;
        $this->short_name = $category->short_name;
        $this->status = $category->status;
        $this->date = $category->date?->format('Y-m-d');

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $category = EquipmentCategory::findOrFail($id);

        if ($category->equipmentTypes()->exists() || $category->equipment()->exists()) {
            session()->flash('error', 'Cannot delete a category that is assigned to equipment or types.');

            return;
        }

        $category->delete();

        session()->flash('message', 'Equipment category deleted.');
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'short_name',
            'equipmentCategoryId',
            'isEdit',
        ]);

        $this->status = true;
        $this->date = now()->format('Y-m-d');
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.equipment-categories-page', [
            'equipmentCategories' => EquipmentCategory::orderBy('title')->get(),
        ]);
    }
}
