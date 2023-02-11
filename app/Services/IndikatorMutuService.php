<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class IndikatorMutuService
{
    private $range,
        $sheet,
        $documentId,
        $subIndikator,
        $typeIndikator,
        $indikatorList,
        $year,
        $month,
        $unit,
        $nameFile;

    public $file;

    public function __construct($indikator, $year)
    {
        $this->year = $year;
        $this->typeIndikator = $indikator;
        $this->sheet = new GoogleSheetService();
        $this->documentId = config('sheets.spreadsheet_id.' . $this->typeIndikator . '.' . $this->year);
        $this->file = config('sheets.file.' . $this->typeIndikator);
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
        $subIndikator = config('sheets.sub-indikator.' . $this->typeIndikator);
        $explode = explode("!", $subIndikator);
        $sheetName = $explode[0];
        $this->range = $sheetName . "!E3:AI3";

        return $this;
    }

    public function indikatorsList()
    {
        $fileService = new FileService($this->fileName());
        $fileService->setType($this->getNameFile());
        if (!is_null($this->year)) {
            $fileService->setYear($this->year);
        }
        if (!is_null($this->month)) {
            $fileService->setMonth($this->month);
        }
        $this->indikatorList = $fileService->read();

        return $this;
    }

    public function subIndikatorsList(): Collection
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
        return $this->typeIndikator;
    }

    public function getType()
    {
        return $this->typeIndikator;
    }

    /**
     * Set the value of indikator
     *
     * @return  self
     */
    public function setSubIndikator($subIndikator)
    {
        $this->subIndikator = $subIndikator;

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
        $indikator = $this->subIndikator;
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
     * Get the value of nameFile
     */
    public function getNameFile()
    {
        $explode = explode("-", $this->getType());

        return count($explode) > 1 ? $explode[0] . $explode[1] : $explode[0];
    }
}
