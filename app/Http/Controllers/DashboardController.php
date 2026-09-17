<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\PreventiveMaintenance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalEquipment' => Equipment::count(),
            'pmThisMonth' => PreventiveMaintenance::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'unserviceable' => Equipment::where('status', false)->count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
