<?php

namespace App\Services;

use App\Models\Insiden;
use Exception;
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

    public function spline($params)
    {
        $inputYear = $params['filter_year'];
        $inputInfeksi = $params['filter_infeksi'];

        $startDate = date('Y-01-01', mktime(0, 0, 0, 1, 1, $inputYear));
        $endDate = date('Y-12-t', strtotime($startDate));

        $sumLminfus = Insiden::select(
            DB::raw('MONTH(TANGGAL) as bulan')
        )
            ->when($inputInfeksi == 'IDO', function ($query) {
                $query->byIdo(); // scopeModel
            })
            ->when(in_array($inputInfeksi, ['ISK', 'PLEBITIS']), function ($query) use ($inputInfeksi) {
                $query->byNonIdo(); // scopeModel
            })
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->verified()
            ->groupBy('bulan')
            ->get();

        $getInfeksi = Insiden::select(
            DB::raw('COUNT(ID) as ttl'),
            DB::raw('MONTH(TANGGAL) as bulan')
        )
            ->where($inputInfeksi, 'YA')
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->verified()
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
    }

    public function column($params)
    {
        $now = date('Y-m', mktime(0, 0, 0, $params['filter_month'], 1, $params['filter_year'])); // Bulan ini
        $tipeInfeksi = $params['filter_infeksi'];

        $startDate = date('Y-m-01', strtotime("-2 month", strtotime($now))); // 3 bulan sebelumnya
        $endDate = date('Y-m-t', strtotime($now)); // bulan ini

        $sumLMInfus = Insiden::select(
            DB::raw('RUANGAN as ruangan'),
            DB::raw('MONTH(TANGGAL) as bulan'),
        )
            ->when($tipeInfeksi == 'IDO', function ($query) {
                $query->byIdo(); // scopeModel
            })
            ->when(in_array($tipeInfeksi, ['ISK', 'PLEBITIS']), function ($query) use ($tipeInfeksi) {
                $query->byNonIdo(); // scopeModel
            })
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->verified()
            ->groupBy('ruangan', 'bulan')
            ->get();

        $getInfeksi = Insiden::select(
            DB::raw('COUNT(ID) as ttl'),
            DB::raw('RUANGAN as ruangan'),
            DB::raw('MONTH(TANGGAL) as bulan'),
        )
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->where($tipeInfeksi, 'YA')
            ->verified()
            ->groupBy('ruangan', 'bulan')
            ->get();

        // dd($getInfeksi);
        // Create Label
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

        // $groupByRuanganAndBulan->dd();

        $function = strtolower($tipeInfeksi);

        $groupByRuanganAndBulan->transform(function ($item) use ($sumLMInfus, $labelSeries, $function) {
            $result = [];
            foreach ($labelSeries as $keyLabel => $value) {
                if (!$item->has($keyLabel)) {
                    $result[$value] = 0;
                } else {

                    $firstItem = $item->get($keyLabel)->first();
                    $lmInfus = optional($sumLMInfus->where('ruangan', $firstItem['ruangan'])->first())->ttl ?? 0;
                    $hitung = PerhitunganService::$function($firstItem['ttl'], $lmInfus);

                    $result[$value] = round($hitung, PHP_ROUND_HALF_UP);
                }
            }

            return $result;
        });

        $result = [
            'dataSeries' => $groupByRuanganAndBulan->toJson(),
            'labelSeries' => collect($labelSeries)->toJson(),
        ];

        return $result;
    }
}
