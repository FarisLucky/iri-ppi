<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use App\Services\INMMutuService;
use App\Services\MutuService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Revolution\Google\Sheets\Facades\Sheets;

class DashboardMutuController extends Controller
{
    public function index()
    {
        $initSheet = new INMMutuService();
        $initSheet->setRange(config('sheets.sub-indikator.INM'));
        $sheet = $initSheet->read();
        // $subIndikator = Arr::collapse($sheet);
        $params = '';

        $subIndikator = MutuService::indikatorWithUnit('INM');
        return view('mutu.index', compact('params', 'subIndikator'));
    }

    public function indikator($jenis)
    {
        return response()->json([
            'success' => Response::HTTP_OK,
            'data' => MutuService::indikatorWithUnit($jenis),
        ]);
    }
}
