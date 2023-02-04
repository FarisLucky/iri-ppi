<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MutuService
{
    public static function getIndikator()
    {
        $initSheet = new INMMutuService();
        $initSheet->setRange(config('sheets.sub-indikator.INM'));
        return $initSheet->read();
    }

    public static function indikator()
    {
        return Cache::remember('indikator-rs', 3600, function () {
            return self::getIndikator();
        });
    }

    public static function indikatorWithUnit()
    {
        $getIndikators = self::indikator();

        $indikators = collect($getIndikators)->filter(function ($item) {
            return count($item) > 0 ? $item : null;
        })->map(function ($item) {
            return collect($item);
        }); // 2x loop

        $indikators->shift(); // remove first element
        // $indikators->dd();
        $units = $indikators->filter(function ($item) {
            return $item->get(0) == '';
        });
        $units = collect([]);
        $head = 0;
        foreach ($indikators as $key => $indikator) {
            if ($indikator->get(0) == '') {
                $indikators->get($head)
                    ->add($indikator->get(1));
                $indikators->forget($key);
                continue;
            }
            $head = $key;
        }
        $indikators->transform(function ($item) {
            $data = collect();
            $key = $item->get(0);
            $item->shift();
            $data->put(
                $key,
                $item->map(function ($item) {
                    return [$item => 'INM JANUARI 2023!'];
                })
            );
            return $data;
        });

        return $indikators->values();
    }

    public static function saveToDisk()
    {
        return Storage::disk('public')->put('inm.json', self::indikatorWithUnit()->toJson(JSON_PRETTY_PRINT));
    }

    public static function perhitunganCell()
    {
        $initSheet = new INMMutuService();
        $initSheet->setRange('INM JANUARI 2023!E12:AI12');
        return $initSheet->read();
    }
}
