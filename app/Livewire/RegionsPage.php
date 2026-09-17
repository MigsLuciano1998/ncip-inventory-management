<?php

namespace App\Livewire;

use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;

class RegionsPage extends Component
{
    use WithPagination;

    public $search = '';

    public $region_short_name;

    public $region_complete_name;

    public $status = true;

    public $regionId;

    public $isEdit = false;

    public $showModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAdd()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'region_short_name' => 'required|string|max:20|unique:regions,region_short_name,' . $this->regionId,
            'region_complete_name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $wasEdit = $this->isEdit;

        Region::updateOrCreate(
            ['id' => $this->regionId],
            [
                'region_short_name' => trim($this->region_short_name),
                'region_complete_name' => trim($this->region_complete_name),
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('message', $wasEdit ? 'Region updated successfully.' : 'Region saved successfully.');
    }

    public function edit($id)
    {
        $region = Region::findOrFail($id);

        $this->regionId = $region->id;
        $this->region_short_name = $region->region_short_name;
        $this->region_complete_name = $region->region_complete_name;
        $this->status = $region->status;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $region = Region::findOrFail($id);

        if ($region->provinces()->exists()) {
            session()->flash('error', 'Cannot delete a region that has provinces assigned.');

            return;
        }

        $region->delete();

        session()->flash('message', 'Region deleted.');
    }

    public function resetForm()
    {
        $this->reset([
            'region_short_name',
            'region_complete_name',
            'regionId',
            'isEdit',
        ]);

        $this->status = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render()
    {
        $regions = Region::query()
            ->when($this->search, function ($q) {
                $search = trim($this->search);
                $q->where(function ($query) use ($search) {
                    $query->where('region_short_name', 'like', "%{$search}%")
                        ->orWhere('region_complete_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('region_short_name')
            ->paginate(10);

        return view('livewire.regions-page', compact('regions'));
    }
}
