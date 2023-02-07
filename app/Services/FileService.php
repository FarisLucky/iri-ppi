<?php

namespace App\Services;

use Illuminate\Support\Arr;
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
        $data = $this->decode()->data;
        $this->filterByYear();

        return collect($data);
    }

    public function filterByYear()
    {
        $year = $this->year;

        $data = $this->decode()->data;

        $filteredData = [];
        foreach ($data as $key => $subIndikator) {
            $unit = array_values($subIndikator)[0];
            $unitCollapse = Arr::collapse($unit);
            if (is_array($unitCollapse)) {
                $units = [];
                foreach ($unitCollapse as $value) {
                    $explode = explode(" ", $value);
                    dd($explode);
                    array_push($units, $explode);
                }
            }
            array_push($filteredData, Arr::collapse($unit));
        }
        dd($filteredData);

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
