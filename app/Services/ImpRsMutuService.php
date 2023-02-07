<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ImpRsMutuService
{
    private $range,
        $sheet,
        $documentId,
        $indikator,
        $indikatorList,
        $unit,
        $file;

    public $tahun,
        $bulan;

    public function __construct()
    {
        $this->sheet = new GoogleSheetService();
        $this->documentId = config('sheets.spreadsheet_id.IMP-RS');
        $this->file = config('sheets.file.IMP-RS');
    }

    public function getData()
    {
        $sheet = $this->sheet;
        $sheet->range = $this->range;
        $sheet->documentId = $this->documentId;

        return $sheet;
    }

    public function read()
    {
        return $this->getData()->readSheet();
    }

    public function readCollection(): Collection
    {
        return collect($this->read());
    }

    public function fileName(): string
    {
        return strtolower($this->file);
    }

    public function val()
    {
        $unit = $this->unit;
        $this->range = $unit;

        return $this;
    }

    public function label()
    {
        $this->range = "2023!E3:AI3";

        return $this;
    }

    public function indikatorsList()
    {
        $this->indikatorList = (new FileService($this->fileName()))->read();

        return $this;
    }

    public function subIndikatorsList($params): Collection
    {
        $indikators = $this->indikatorList;

        $subIndikator = $indikators->transform(function ($item) {
            return array_keys($item)[0];
        });

        return $subIndikator;
    }

    public function units($subIndikator)
    {
        $indikators = $this->indikatorList;

        $units = $indikators->filter(function ($item) use ($subIndikator) {
            if (array_keys($item)[0] == $subIndikator) {
                return array_values($item)[0];
            }
        })->first();

        $units = Arr::collapse($units);

        return $units;
    }

    public function getTitle()
    {
        return "IMP-RS";
    }

    /**
     * Set the value of indikator
     *
     * @return  self
     */
    public function setIndikator($indikator)
    {
        $this->indikator = $indikator;

        return $this;
    }

    /**
     * Set the value of unit
     *
     * @return  self
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
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
     * Get the value of unit
     */
    public function getUnit()
    {
        $this->indikatorsList();

        $list = $this->indikatorList;
        $indikator = $this->indikator;
        $unit = $this->unit;
        $this->range = $unit;

        $filterByIndikator = $list->filter(function ($item) use ($indikator) {
            return array_keys($item)[0] == $indikator;
        })->first();

        $units = array_values($filterByIndikator)[0];
        $filterByUnit = Arr::collapse($units);

        return array_search($unit, $filterByUnit);
    }

    /**
     * Set the value of tahun
     *
     * @return  self
     */
    public function setTahun($tahun)
    {
        $this->tahun = $tahun;

        return $this;
    }

    /**
     * Set the value of bulan
     *
     * @return  self
     */
    public function setBulan($bulan)
    {
        $this->bulan = $bulan;

        return $this;
    }
}
