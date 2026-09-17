<?php

namespace App\Http\Controllers;

class EquipmentCategoryController extends Controller
{
    public function index()
    {
        return view('equipment-categories.index');
    }
}
