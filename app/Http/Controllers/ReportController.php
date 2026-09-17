<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function par()
    {
        return view('reports.par');
    }

    public function propertyTransfer()
    {
        return view('reports.property-transfer');
    }

    public function returnedProperty()
    {
        return view('reports.returned-property');
    }

    public function ppeInStations()
    {
        return view('reports.ppe-in-stations');
    }

    public function lostStolenDamaged()
    {
        return view('reports.lost-stolen-damaged');
    }

    public function propertyTagging()
    {
        return view('reports.property-tagging');
    }
}
