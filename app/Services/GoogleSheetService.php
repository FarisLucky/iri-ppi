<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetService
{
    public $client, $service, $documentId, $range;

    public function __construct()
    {
        $this->client = $this->getClient();
        $this->service = new Sheets($this->client);
        $this->range = 'A:Z'; // format Sheet1!A:Z
    }

    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName('Dashboard KMKP');
        $client->setRedirectUri('http://localhost/starter/ppi/mutu/dashboard');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setAccessType('offline');

        return $client;
    }

    public function readSheet()
    {
        $doc = $this->service->spreadsheets_values->get($this->documentId, $this->range);

        return $doc->getValues();
    }
}
