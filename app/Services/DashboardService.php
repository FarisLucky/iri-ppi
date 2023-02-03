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

            $sumLminfus = Insiden::select(
                DB::raw('MONTH(TANGGAL) as bulan')
            )
                ->when($inputInfeksi == 'IDO', function ($query) {
                    $query->addSelect(
                        DB::raw('COUNT(ID) as ttl')
                    )->where(function ($query) {
                        $query->where('IDO', 'YA')
                            ->OrWhere('IDO', 'TIDAK');
                    });
                })
                ->when(in_array($inputInfeksi, ['ISK', 'PLEBITIS']), function ($query) use ($inputInfeksi) {
                    $query->addSelect(
                        DB::raw('SUM(LMINFUS) as ttl')
                    );
                })
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->groupBy('bulan')
                ->get();
            $getInfeksi = Insiden::select(
                DB::raw('COUNT(ID) as ttl'),
                DB::raw('MONTH(TANGGAL) as bulan')
            )
                ->where($inputInfeksi, 'YA')
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->groupBy('bulan')
                ->get();

            $functionName = strtolower($inputInfeksi);
            $result = $getInfeksi
                ->groupBy('bulan')
                ->map(function ($item, $key) use ($sumLminfus, $functionName) {
                    $data = $item->pluck('ttl')->first();
                    $lmInfus = $sumLminfus->where('bulan', $key)->first()->ttl;
                    $plebitis = PerhitunganService::$functionName($data, $lmInfus);
                    return [
                        date('F', mktime(0, 0, 0, $key, 1)) => round($plebitis, PHP_ROUND_HALF_UP)
                    ];
                })
                ->values();
            $result = Arr::collapse($result);

            $result = [
                'dataSeries' => json_encode(array_values($result)),
                'labelSeries' => json_encode(array_keys($result)),
                'type' => json_encode(strtoupper($inputInfeksi))
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
                DB::raw('RUANGAN as ruangan'),
                DB::raw('MONTH(TANGGAL) as bulan'),
            )
                ->when($tipeInfeksi == 'IDO', function ($query) {
                    $query->addSelect(
                        DB::raw('COUNT(ID) as ttl') // jumlah pasien ido 1 bulan operasi
                    )->where(function ($query) {
                        $query->where('IDO', 'YA')
                            ->OrWhere('IDO', 'TIDAK');
                    });
                })
                ->when(in_array($tipeInfeksi, ['ISK', 'PLEBITIS']), function ($query) use ($tipeInfeksi) {
                    $query->addSelect(
                        DB::raw('SUM(LMINFUS) as ttl') // jumlah lminfus 1 bulan
                    );
                })
                ->whereBetween('TANGGAL', [$startDate, $endDate])
                ->groupBy('ruangan', 'bulan')
                ->get();

            $getInfeksi = Insiden::select(
                DB::raw('RUANGAN as ruangan'),
                DB::raw('MONTH(TANGGAL) as bulan'),
            )
                ->when($tipeInfeksi == 'IDO', function ($query) {
                    $query->addSelect(
                        DB::raw('COUNT(ID) as ttl')
                    )->where(function ($query) {
                        $query->where('IDO', 'YA')
                            ->OrWhere('IDO', 'TIDAK');
                    });
                })
                ->when(in_array($tipeInfeksi, ['ISK', 'PLEBITIS']), function ($query) use ($tipeInfeksi) {
                    $query->addSelect(
                        DB::raw('SUM(LMINFUS) as ttl')
                    )->where($tipeInfeksi, 'YA');
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
                        $lmInfus = $sumLMInfus->where('ruangan', $firstItem['ruangan'])->first()->ttl;
                        $hitung = PerhitunganService::$function($firstItem['ttl'], $lmInfus);

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
