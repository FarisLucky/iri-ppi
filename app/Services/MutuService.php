<?php

namespace App\Services;

use Illuminate\Support\Arr;

class MutuService
{
    public static function getIndikator($jenis)
    {
        $initSheet = new INMMutuService();
        $initSheet->setRange(config('sheets.sub-indikator.INM'));
        return $initSheet->read();
    }

    public static function indikatorWithUnit($jenis)
    {
        $getIndikators = self::getIndikator($jenis);
        $indikators = collect($getIndikators)->filter(function ($item) {
            return count($item) > 0 ? $item : null;
        })->map(function ($item) {
            return collect($item);
        }); // 2x loop

        $head = 0;
        $ind = $indikators;
        foreach ($indikators as $key => $indikator) { //3x loop
            if ($indikator->get(0) == '') {
                $unit = $ind->get($key)[1];
                $ind->get($head)->add($unit);
                $ind->forget($key);
            }
            if ($indikator->get(0) != '') {
                $head = $key;
            }
        }
        $jenis = $ind->map(function ($item) {
            return $item->get(0);
        });
        $ind->transform(function ($item) {
            return $item->forget(0);
        });
        $subJenis = $jenis->map(function ($item, $key) use ($ind) {
            $result = [];
            $result['header'] = $item;
            // $ind->forget($key);
            $result['sub'] = $ind->get($key);
            return $result;
        });
        $result = $subJenis->forget(0);
        $result = $result->values();

        return $result;
    }
}
