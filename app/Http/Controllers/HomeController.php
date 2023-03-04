<?php

namespace App\Http\Controllers;

use App\Models\Insiden;
use App\Services\DashboardService;
use App\Services\PerhitunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Gate::allows('ppi') || Gate::allows('supersu')) {
            return redirect()->route('insiden.dashboard.index');
        } else if (Gate::allows('mutu')) {
            return redirect()->route('mutu.dashboard');
        }
    }

    public function showChart(Request $request)
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

        return view('home', compact('infeksiColumn', 'infeksiSplineChart'));
    }
}
