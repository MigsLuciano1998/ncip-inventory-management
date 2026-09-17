<?php

namespace App\Livewire;

use App\Models\PpeCategory;
use Livewire\Component;

class PpeCategoriesPage extends Component
{
    public $number;

    public $title;

    public $uacs_object_code;

    public $ppe_sub_major_account_group;

    public $general_ledger_account;

    public $status = true;

    public $date;

    public $ppeCategoryId;

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
            'number' => 'required|string|max:10|unique:ppe_categories,number,' . $this->ppeCategoryId,
            'title' => 'required|string|max:255',
            'uacs_object_code' => 'required|string|max:50',
            'ppe_sub_major_account_group' => 'required|string|max:10',
            'general_ledger_account' => 'required|string|max:10',
            'status' => 'required|boolean',
            'date' => 'required|date',
        ]);

        $wasEdit = $this->isEdit;

        PpeCategory::updateOrCreate(
            ['id' => $this->ppeCategoryId],
            [
                'number' => strtoupper(trim($this->number)),
                'title' => trim($this->title),
                'uacs_object_code' => trim($this->uacs_object_code),
                'ppe_sub_major_account_group' => trim($this->ppe_sub_major_account_group),
                'general_ledger_account' => trim($this->general_ledger_account),
                'status' => $this->status,
                'date' => $this->date,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('message', $wasEdit ? 'PPE category updated successfully.' : 'PPE category saved successfully.');
    }

    public function edit($id)
    {
        $category = PpeCategory::findOrFail($id);

        $this->ppeCategoryId = $category->id;
        $this->number = $category->number;
        $this->title = $category->title;
        $this->uacs_object_code = $category->uacs_object_code;
        $this->ppe_sub_major_account_group = $category->ppe_sub_major_account_group;
        $this->general_ledger_account = $category->general_ledger_account;
        $this->status = $category->status;
        $this->date = $category->date?->format('Y-m-d');

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        PpeCategory::findOrFail($id)->delete();

        session()->flash('message', 'PPE category deleted.');
    }

    public function resetForm()
    {
        $this->reset([
            'number',
            'title',
            'uacs_object_code',
            'ppe_sub_major_account_group',
            'general_ledger_account',
            'ppeCategoryId',
            'isEdit',
        ]);

        $this->status = true;
        $this->date = now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.ppe-categories-page', [
            'ppeCategories' => PpeCategory::query()
                ->orderByRaw('CAST(number AS INTEGER), number')
                ->get(),
        ]);
    }
}
