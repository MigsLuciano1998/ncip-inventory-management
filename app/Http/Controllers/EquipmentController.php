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
}
