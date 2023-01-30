<?php

namespace App\Http\Controllers;

use App\Models\Insiden;
use Illuminate\Http\Request;
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
        return view('home', compact('infeksiPieChart'));
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
        // dd($result);

        return $result->toJson();
    }
}
