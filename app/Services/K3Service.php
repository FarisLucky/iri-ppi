<?php

namespace App\Services;

use Illuminate\Support\Collection;

class K3Service
{
    private $range,
        $sheet,
        $documentId,
        $year,
        $month;

    public $file;

    public function __construct($year)
    {
        $this->year = $year;
        $this->sheet = new GoogleSheetService();
        $this->documentId = config('sheets.k3rs.' . $this->year);
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
        $sheetName = "IMUK " . $this->month;
        $this->range = $sheetName . "!E39:AI39";

        return $this;
    }

    public function label()
    {
        $sheetName = "IMUK " . $this->month;
        $this->range = $sheetName . "!E3:AI3";

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
     * Get the value of month
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Get the value of range
     */
    public function getRange()
    {
        return $this->range;
    }
}
