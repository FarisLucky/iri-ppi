<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public $name, $data, $year, $month, $type;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getFile()
    {
        return Storage::disk('public')->get($this->name . $this->ext());
    }

    public function read()
    {
        $data = $this->decode();
        if (!is_null($this->year) && !is_null($this->month)) {
            $data = $data->filters();
        }
        return collect($data->data);
    }

    public function filters()
    {
        $month = $this->month;
        $year = $this->year;

        $data = $this->data;

        $filteredData = [];
        $months = config('sheets.bulan');
        foreach ($data as $key => $subIndikator) {
            $unit = array_values($subIndikator)[0];
            $indikatorKey = array_keys($subIndikator)[0];
            $unitCollapse = Arr::collapse($unit);
            if (is_array($unitCollapse)) {
                $units = [];
                foreach ($unitCollapse as $unitKey => $value) {
                    $explode = explode(" ", $value);
                    $firstName = $explode[0];
                    $time = mktime(0, 0, 0, intval($month), 1, $year);
                    $monthName = Carbon::parse($time)->locale('id')->isoFormat('MMMM');
                    if ($this->type == "IMP-RS") {
                        $lastName = explode("!", $explode[0]);
                        // dd($lastName);
                        $sheetRange = $lastName[1];
                        $replaceYear = $year . '!' . $sheetRange;
                        $fullName = $replaceYear; // ex. 2023!B3:AI3
                    } else {
                        $lastName = explode("!", $explode[2]);
                        $replaceYear = $year . '!' . $lastName[1];
                        $fullName = $firstName . " " . $monthName . " " . $replaceYear; // ex. INM JANUARI 2023!B3:AI3
                    }
                    array_push($units, [
                        $unitKey => $fullName
                    ]);
                }
                array_push($filteredData, [
                    $indikatorKey => $units
                ]);
            } else {
                array_push($filteredData, [
                    $indikatorKey => $unit
                ]);
            }
        }

        $this->data = $filteredData;

        return $this;
    }

    public function decode()
    {
        $this->data = json_decode($this->getFile(), true);

        return $this;
    }

    public function ext()
    {
        return '.json';
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Set the value of month
     *
     * @return  self
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
