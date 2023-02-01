<?php

namespace App\Services;

class INMMutuService
{
    public $data, $sheet, $range, $documentId;

    public function __construct()
    {
        $this->sheet = new GoogleSheetService();
        $this->documentId = config('sheets.spreadsheet_id.INM');
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

    public function toCollection()
    {
        return collect($this->read());
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
     * Set the value of documentId
     *
     * @return  self
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
    }
}
