<?php

namespace App\Http\Controllers;

use App\Models\Insiden;
use App\Services\PerhitunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $infeksiColumn = $this->column();
        // $infeksiPieChart = $this->pie();
        $infeksiSplineChart = $this->spline();
        return view('home', compact('infeksiColumn', 'infeksiSplineChart'));
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
        ];

        $lmInfusInMonth = Insiden::select(DB::raw('SUM(LMINFUS) as ttl_lminfus'))
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->pluck('ttl_lminfus')
            ->first();

        $jmlPasienOperasi = Insiden::select('ID')
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->where('IDO', 'YA')
            ->count();

        $result = collect();
        foreach ($infeksiusType as $infeksi) {
            $explodeKey = explode('-', $infeksi); // => [0]PLEBISTIS, [1]YA
            $keyWhere = $explodeKey[0];
            $valWhere = $explodeKey[1];

            $getInfeksi = Insiden::select('ID')
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->where($keyWhere, $valWhere)
                ->count();

            $functionName = strtolower($keyWhere);
            $resultInfeksi = PerhitunganService::$functionName($getInfeksi, $lmInfusInMonth);

            $result->put($infeksi, round($resultInfeksi, PHP_ROUND_HALF_UP));
        }

        return $result->toJson();
    }

    public function spline()
    {
        $startDate = date('Y-01-01');
        $endDate = date('Y-12-t');

        $tipeInfeksi = 'PLEBITIS';

        $sumLmInfus = Insiden::select(
            DB::raw('SUM(LMINFUS) as sum_lminfus'),
            DB::raw('RUANGAN as ruangan'),
            DB::raw('MONTH(TANGGAL) as bulan')
        )
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->where($tipeInfeksi, 'YA')
            ->groupBy('ruangan', 'bulan')
            ->get();

        $getInfeksi = Insiden::select(
            DB::raw('COUNT(ID) as jumlah_infeksi'),
            DB::raw('RUANGAN as ruangan'),
            DB::raw('MONTH(TANGGAL) as bulan')
        )
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->where($tipeInfeksi, 'YA')
            ->groupBy('ruangan', 'bulan')
            ->get();

        $groupByRuangan = $getInfeksi->groupBy('ruangan');
        $groupByRuanganAndBulan = $groupByRuangan->map->groupBy('bulan');
        $ruanganAndInsiden = $groupByRuanganAndBulan->map->transform(function ($item) use ($sumLmInfus) {
            $lmInfus = $sumLmInfus->where('ruangan', $item[0]['ruangan'])->first()->sum_lminfus;
            $hitung = PerhitunganService::plebitis($item[0]['jumlah_infeksi'], $lmInfus);
            return round($hitung, PHP_ROUND_HALF_UP);
        });

        return $ruanganAndInsiden->toJson();
    }

    public function column()
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        $tipeInfeksi = 'PLEBITIS';

        $infeksiPerRuangan = Insiden::select(
            DB::raw('SUM(LMINFUS) as sum_lminfus'),
            DB::raw('RUANGAN as ruangan'),
            DB::raw('MONTH(TANGGAL) as bulan')
        )
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->where($tipeInfeksi, 'YA')
            ->groupBy('ruangan')
            ->get();

        $getInfeksi = Insiden::select(
            DB::raw('COUNT(id) as jumlah_infeksi'),
            DB::raw('RUANGAN as ruangan'),
            DB::raw('MONTH(TANGGAL) as bulan')
        )
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->where($tipeInfeksi, 'YA')
            ->groupBy('ruangan')
            ->get();

        $infeksi = $getInfeksi->transform(function ($item) use ($infeksiPerRuangan) {
            $lmInfus = $infeksiPerRuangan->where('ruangan', $item->ruangan)->first()->sum_lminfus;
            $hitung = PerhitunganService::plebitis($item->jumlah_infeksi, $lmInfus);
            $item->ruangan = $item->ruangan;
            $item->result = round($hitung, PHP_ROUND_HALF_UP);
            $item->sum_lminfus = $lmInfus;
            return $item;
        })->pluck('result', 'ruangan');

        $dataInfeksi = [
            'infeksi' => $infeksi,
            'bulan' => date('F', strtotime($startDate)),
            'tahun' => date('Y', strtotime($startDate)),
        ];

        return collect($dataInfeksi)->toJson();
    }
}
