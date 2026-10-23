<?php

namespace App\Http\Controllers;

use App\Models\Equipment;

class EquipmentController extends Controller
{
    public function index()
    {
        return view('equipment.index');
    }

    public function show(Equipment $equipment)
    {
        return view('equipment.show', compact('equipment'));
    }

    public function iirupp(Equipment $equipment)
    {
        if ($equipment->depreciation()->exists()) {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'An IIRUPP record already exists for this equipment.');
        }

        return view('equipment.iirupp', compact('equipment'));
    }
}
