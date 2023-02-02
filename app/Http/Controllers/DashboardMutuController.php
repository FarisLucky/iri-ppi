<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use App\Services\INMMutuService;
use Illuminate\Http\Request;
use Revolution\Google\Sheets\Facades\Sheets;

class DashboardMutuController extends Controller
{
    public function index()
    {
        // $initSheet = new INMMutuService();
        // $initSheet->setRange('2023!A:Z');
        // $sheet = $initSheet->read();

        // return response()->json($sheet);
        $params = '';

        return view('mutu.index', compact('params'));
    }
}
