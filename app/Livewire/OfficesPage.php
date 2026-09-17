<?php

namespace App\Livewire;

use App\Models\Office;
use App\Models\Province;
use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;

class OfficesPage extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedRegion = '';

    public $selectedProvince = '';

    public $form_region_id;

    public $form_province_id;

    public $office_name;

    public $office_location;

    public $status = true;

    public $officeId;

    public $isEdit = false;

    public $showModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedRegion()
    {
        $this->selectedProvince = '';
        $this->resetPage();
    }

    public function updatedSelectedProvince()
    {
        $this->resetPage();
    }

    public function updatedFormRegionId()
    {
        $this->form_province_id = '';
    }

    public function openAdd()
    {
        $this->resetForm();
        $this->form_region_id = $this->selectedRegion ?: '';
        $this->form_province_id = $this->selectedProvince ?: '';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'form_region_id' => 'required|exists:regions,id',
            'form_province_id' => 'required|exists:provinces,id',
            'office_name' => 'required|string|max:255|unique:offices,office_name,' . $this->officeId . ',id,province_id,' . $this->form_province_id,
            'office_location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        Office::updateOrCreate(
            ['id' => $this->officeId],
            [
                'province_id' => $this->form_province_id,
                'office_name' => $this->office_name,
                'office_location' => $this->office_location ?: null,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('message', 'Office saved successfully.');
    }

    public function edit($id)
    {
        $office = Office::with('province')->findOrFail($id);

        $this->officeId = $office->id;
        $this->form_region_id = $office->province?->region_id;
        $this->form_province_id = $office->province_id;
        $this->office_name = $office->office_name;
        $this->office_location = $office->office_location;
        $this->status = $office->status;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $office = Office::findOrFail($id);

        if ($office->equipment()->exists() || $office->employees()->exists()) {
            session()->flash('error', 'Cannot delete an office that has equipment or employees assigned.');

            return;
        }

        $office->delete();

        session()->flash('message', 'Office deleted.');
    }

    public function resetForm()
    {
        $this->reset([
            'form_region_id',
            'form_province_id',
            'office_name',
            'office_location',
            'officeId',
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
        $regions = Region::where('status', 1)
            ->orderBy('region_short_name')
            ->get();

        $filterProvinces = Province::when($this->selectedRegion, fn ($q) => $q->where('region_id', $this->selectedRegion))
            ->where('status', 1)
            ->orderBy('province_name')
            ->get();

        $formProvinces = $this->form_region_id
            ? Province::where('region_id', $this->form_region_id)
                ->where('status', 1)
                ->orderBy('province_name')
                ->get()
            : collect();

        $offices = Office::with('province.region')
            ->when($this->selectedRegion, function ($q) {
                $q->whereHas('province', fn ($province) => $province->where('region_id', $this->selectedRegion));
            })
            ->when($this->selectedProvince, fn ($q) => $q->where('province_id', $this->selectedProvince))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('office_name', 'like', "%{$this->search}%")
                        ->orWhere('office_location', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('office_name')
            ->paginate(10);

        return view('livewire.offices-page', compact(
            'regions',
            'filterProvinces',
            'formProvinces',
            'offices'
        ));
    }
}
