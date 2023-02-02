<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardInsidenRequest;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardInsidenController extends Controller
{
    public function index()
    {
        $params = '';
        $infeksiColumn = '';
        $infeksiSplineChart = '';

        return view('home', compact('infeksiColumn', 'infeksiSplineChart', 'params'));
    }

    public function showChart(DashboardInsidenRequest $request)
    {
        $params = [];

        if ($request->has('filter_year')) {
            $params['filter_year'] = $request->filter_year;
        }

        if ($request->has('filter_infeksi')) {
            $params['filter_infeksi'] = $request->filter_infeksi;
        }

        $dashboard = new DashboardService();
        $infeksiColumn = $dashboard->column($params);
        $infeksiSplineChart = $dashboard->spline($params);

        return view('home', compact('infeksiColumn', 'infeksiSplineChart', 'params'));
    }
}
