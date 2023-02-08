<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public $name, $data, $year, $month;

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
                    $center = array_filter($months, function ($key) use ($month) {
                        return $key == $month;
                    }, ARRAY_FILTER_USE_KEY);
                    $lastName = explode("!", $explode[2]);
                    $replaceYear = $year . '!' . $lastName[1];
                    $fullName = $firstName . " " . array_shift($center) . " " . $replaceYear;
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
}
