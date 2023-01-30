<?php

namespace App\Http\Controllers;

use App\Models\Insiden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        DB::statement("SET SQL_MODE=''");
        $infeksiPieChart = $this->pie();
        $infeksiSplineChart = $this->spline();
        return view('home', compact('infeksiPieChart', 'infeksiSplineChart'));
    }

    public function pie()
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        if (request()->has('filter_by_month') != null || request()->has('filter_by_year') != null) {
            $month = request()->input('filter_by_month');
            $year = request()->input('filter_by_year');
            $startDate = date('Y-m-01', strtotime("$year-$month"));
            $endDate = date('Y-m-t', strtotime("$year-$month"));
        }

        $infeksiusType = [
            'PLEBITIS-YA',
            'ISK-YA',
            'IDO-YA',
            'IDO-NON OPERASI',
        ];

        $result = collect();
        foreach ($infeksiusType as $infeksi) {
            $explodeKey = explode('-', $infeksi); // => [0]PLEBISTIS, [1]YA
            $keyWhere = $explodeKey[0];
            $valWhere = $explodeKey[1];

            $getInfeksi = Insiden::select('id')
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->where($keyWhere, $valWhere)
                ->count();

            $result->put($infeksi, $getInfeksi);
        }

        return $result->toJson();
    }

    public function spline()
    {
        $year = date('Y');

        if (request()->has('filter_by_year') != null) {
            $year = request()->input('filter_by_year');
        }

        $tipeInfeksi = 'PLEBITIS';

        $infeksiusType = [
            'PLEBITIS-YA',
            'ISK-YA',
            'IDO-YA',
            'IDO-NON OPERASI',
        ];

        $getInfeksi = Insiden::select(
            DB::raw('COUNT(id) as jumlah_infeksi'),
            DB::raw('RUANGAN as ruangan'),
            DB::raw('MONTH(TANGGAL) as bulan')
        )
            ->whereBetween('TANGGAL', ['2022-10-1', '2023-01-30'])
            ->where($tipeInfeksi, 'YA')
            ->groupBy('ruangan', 'bulan')
            ->get();

        $groupByRuangan = $getInfeksi->groupBy('ruangan');
        $groupByRuanganAndBulan = $groupByRuangan->map->groupBy('bulan');
        $ruanganAndInsiden = $groupByRuanganAndBulan->map->transform(function ($item) {
            return $item[0]['jumlah_infeksi'] ?? 0;
        });

        return $ruanganAndInsiden->toJson();
    }
}
