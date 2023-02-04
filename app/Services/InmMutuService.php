<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class INMMutuService
{
    private $data,
        $sheet,
        $documentId,
        $indikator,
        $indikatorList,
        $unit,
        $file;

    public function __construct()
    {
        $this->sheet = new GoogleSheetService();
        $this->documentId = config('sheets.spreadsheet_id.INM');
        $this->file = config('sheets.file.INM');
    }

    public function getData()
    {
        $sheet = $this->sheet;
        $sheet->range = $this->range();
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

    public function range(): string
    {
        $this->indikatorsList();

        $list = $this->indikatorList;
        $indikator = $this->indikator;
        $unit = $this->unit;

        $filterByIndikator = $list->filter(function ($item) use ($indikator) {
            return array_keys($item)[0] == $indikator;
        })->first();

        $units = array_values($filterByIndikator)[0];
        $filterByUnit = Arr::collapse($units);
        $range = Arr::get($filterByUnit, $unit);

        return $range;
    }

    public function indikatorsList()
    {
        $this->indikatorList = (new FileService($this->fileName()))->read();

        return $this;
    }

    public function subIndikatorsList(): Collection
    {
        $indikators = $this->indikatorList;

        return $indikators->map(function ($item) {
            return array_keys($item)[0];
        });
    }

    /**
     * Set the value of documentId
     *
     * @return  self
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
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
}
