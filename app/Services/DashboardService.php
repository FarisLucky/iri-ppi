<?php

namespace App\Services;

use App\Models\Insiden;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{

    public function __construct()
    {
        DB::statement("SET SQL_MODE=''");
    }

    public function pie(Request $request)
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

    public function spline($params)
    {
        try {
            $inputYear = $params['filter_year'];
            $inputInfeksi = $params['filter_infeksi'];

            $startDate = date('Y-01-01', strtotime($inputYear));
            $endDate = date('Y-12-t', strtotime($inputYear));

            $sumLmInfus = Insiden::select(
                DB::raw('SUM(LMINFUS) as sum_lminfus'),
                DB::raw('RUANGAN as ruangan'),
                DB::raw('MONTH(TANGGAL) as bulan')
            )
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->where($inputInfeksi, 'YA')
                ->groupBy('ruangan', 'bulan')
                ->get();

            $getInfeksi = Insiden::select(
                DB::raw('COUNT(ID) as jumlah_infeksi'),
                DB::raw('RUANGAN as ruangan'),
                DB::raw('MONTH(TANGGAL) as bulan')
            )
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->where($inputInfeksi, 'YA')
                ->groupBy('ruangan', 'bulan')
                ->get();

            $groupByRuangan = $getInfeksi->groupBy('ruangan');
            $groupByRuanganAndBulan = $groupByRuangan->map->groupBy('bulan');
            $ruanganAndInsiden = $groupByRuanganAndBulan->map->transform(function ($item) use ($sumLmInfus) {
                $lmInfus = $sumLmInfus->where('ruangan', $item[0]['ruangan'])->first()->sum_lminfus;
                $hitung = PerhitunganService::plebitis($item[0]['jumlah_infeksi'], $lmInfus);
                return round($hitung, PHP_ROUND_HALF_UP);
            });

            $maxMonth = 12;
            $labelSeries = [];
            for ($i = 1; $i <= $maxMonth; $i++) {
                array_push($labelSeries, [
                    $i => date('F', mktime(0, 0, 0, $i, 1)) // ['1' => 'January']
                ]);
            }

            $result = [
                'dataSeries' => $ruanganAndInsiden->toJson(),
                'labelSeries' => collect($labelSeries)->toJson(),
            ];

            return $result;
        } catch (\Throwable $th) {

            dd($th->getMessage());
        }
    }

    public function column($params)
    {
        try {
            $now = date('Y-m', strtotime($params['filter_year'])); // Bulan ini
            $tipeInfeksi = $params['filter_infeksi'];

            $startDate = date('Y-m-01', strtotime("-2 month", strtotime($now))); // 3 bulan sebelumnya
            $endDate = date('Y-m-t', strtotime($now)); // bulan ini

            $sumLMInfus = Insiden::select(
                DB::raw('SUM(LMINFUS) as sum_lminfus'),
                DB::raw('RUANGAN as ruangan'),
                DB::raw('MONTH(TANGGAL) as bulan'),
            )
                ->when($tipeInfeksi == 'IDO', function ($query) {
                    $query->where(function ($query) {
                        $query->where('IDO', 'YA')
                            ->OrWhere('IDO', 'TIDAK');
                    });
                })
                ->when(in_array($tipeInfeksi, ['ISK', 'PLEBITIS']), function ($query) use ($tipeInfeksi) {
                    $query->where($tipeInfeksi, 'YA');
                })
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->groupBy('ruangan', 'bulan')
                ->get();

            $getInfeksi = Insiden::select(
                DB::raw('COUNT(id) as jumlah_infeksi'),
                DB::raw('RUANGAN as ruangan'),
                DB::raw('MONTH(TANGGAL) as bulan'),
            )
                ->when($tipeInfeksi == 'IDO', function ($query) {
                    $query->where(function ($query) {
                        $query->where('IDO', 'YA')
                            ->OrWhere('IDO', 'TIDAK');
                    });
                })
                ->when(in_array($tipeInfeksi, ['ISK', 'PLEBITIS']), function ($query) use ($tipeInfeksi) {
                    $query->where($tipeInfeksi, 'YA');
                })
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->groupBy('ruangan', 'bulan')
                ->get();

            $subMonth = Carbon::createFromFormat('Y-m', $now)->diffInMonths($startDate);
            $maxMonth = $now;
            $labelSeries = [];
            for ($i = $subMonth; $i >= 0; $i--) {
                $month = Carbon::createFromFormat('Y-m', $now)->subMonths($i);
                $labelSeries += [
                    $month->month => date('F', mktime(0, 0, 0, $month->month, 1))
                ]; // ['1' => 'January']
            }
            $groupByRuangan = $getInfeksi->groupBy('ruangan');

            $groupByRuanganAndBulan = $groupByRuangan->map->groupBy('bulan');

            $function = strtolower($tipeInfeksi);

            $infeksi = $groupByRuanganAndBulan->transform(function ($item) use ($sumLMInfus, $labelSeries, $function) {
                $result = [];
                foreach ($labelSeries as $keyLabel => $value) {
                    if (!$item->has($keyLabel)) {
                        $result[$value] = 0;
                    } else {

                        $firstItem = $item->get($keyLabel)->first();
                        $lmInfus = $sumLMInfus->where('ruangan', $firstItem['ruangan'])->first()->sum_lminfus;
                        $hitung = PerhitunganService::$function($firstItem['jumlah_infeksi'], $lmInfus);

                        $result[$value] = round($hitung, PHP_ROUND_HALF_UP);
                    }
                }

                return $result;
            });

            $result = [
                'dataSeries' => $infeksi->toJson(),
                'labelSeries' => collect($labelSeries)->toJson(),
            ];

            return $result;
        } catch (\Throwable $th) {

            dd($th->getMessage());
        }
    }
}
