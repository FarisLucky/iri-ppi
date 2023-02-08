<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MutuService
{
    private $object, $sheet, $indikator, $range, $jenisIndikator;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function getData()
    {
        $initSheet = $this->object;
        $initSheet->setRange($this->range);
        $this->sheet = $initSheet->read();

        return $this;
    }

    public function indikatorWithUnit()
    {
        $getIndikators = $this->sheet;

        $indikators = collect($getIndikators)->filter(function ($item) {
            return count($item) > 0 ? $item : null;
        })->map(function ($item) {
            return collect($item);
        }); // 2x loop

        $indikators->shift(); // remove first element

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

        $sheetStart = 3;
        $indikators->transform(function ($item) use (&$sheetStart) {
            $data = collect();
            $key = $item->get(0);
            $item->shift();
            $data->put(
                $key,
                $item->map(function ($item) use (&$sheetStart) {
                    $sheetStart += 3;
                    $result = [$item => $this->jenisIndikator . " JANUARI 2023!E" . $sheetStart . ":AI" . $sheetStart];
                    return $result;
                })
            );
            return $data;
        });

        $this->indikator = $indikators->values();

        return $this;
    }

    public function saveToDisk()
    {
        return Storage::disk('public')->put($this->object->fileName() . '.json', $this->getIndikator()->toJson(JSON_PRETTY_PRINT));
    }

    /**
     * Get the value of range
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Set the value of range
     *
     * @return  self
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * Get the value of indikator
     */
    public function getIndikator()
    {
        return collect($this->indikator);
    }

    /**
     * Get the value of sheet
     */
    public function getSheet()
    {
        return collect($this->sheet);
    }

    /**
     * Set the value of jenisIndikator
     *
     * @return  self
     */
    public function setJenisIndikator($jenisIndikator)
    {
        $this->jenisIndikator = $jenisIndikator;

        return $this;
    }
}
