<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use App\Services\K3Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardK3Controller extends Controller
{
    public function index()
    {
        // $date = mktime(0, 0, 0, 12, 1, 2022);
        // dd(Carbon::createFromTime($date)->format('d-m-Y'));
        // $params = $this->getDataByDate();

        return view('k3.index', compact("params", "chart"));
    }

    public function showChart(Request $request)
    {
        $date = Carbon::createFromFormat("Y-m-d", '2022-12-01');
        $k3SheetService = new K3Service(
            $date->year
        );
        $k3SheetService->setMonth(strtoupper($date->monthName));

        // Ambil data label
        $labelK3 = $k3SheetService->label()
            ->read()[0];

        // Ambil data K3
        $dataK3 = $k3SheetService->val()
            ->read()[0];

        // dd($labelK3);
        $dataK3 = collect($dataK3)->map(function ($k3) {
            return intval($k3);
        })->toArray();

        $params = "";
        $chart = collect([
            "title" => "K3 Kejadian Tertusuk Jarum",
            "label" => $labelK3,
            "data" => $dataK3,
        ])->toJson();
        $params = "";
        $chart = "";
        return view('k3.index', compact("params", "chart"));
    }

    public function getDataByDate($date)
    {
        $date = Carbon::createFromFormat("Y-m-d", $date);
        $k3SheetService = new K3Service(
            $date->year
        );
        $k3SheetService->setMonth(strtoupper($date->monthName));

        // Ambil data label
        $labelK3 = $k3SheetService->label()
            ->read()[0];

        // Ambil data K3
        $dataK3 = $k3SheetService->val()
            ->read()[0];

        // dd($labelK3);
        $dataK3 = collect($dataK3)->map(function ($k3) {
            return intval($k3);
        })->toArray();

        $params = "";
        $chart = collect([
            "title" => "K3 Kejadian Tertusuk Jarum",
            "label" => $labelK3,
            "data" => $dataK3,
        ])->toJson();

        return compact('params', 'chart');
    }
}
