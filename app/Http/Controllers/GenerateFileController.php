<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use App\Services\MutuService;
use Illuminate\Http\Request;

class GenerateFileController extends Controller
{

    public function index()
    {
        return view('generate.index');
    }
    public function generate(Request $request)
    {
        try {
            $type = $request->filter_indikator;

            $fileService = new MutuService(
                DashboardMutuController::filters($type)
            );

            $fileService->setRange(config('sheets.sub-indikator.' . $type));

            $fileService->getData()
                ->indikatorWithUnit()
                ->saveToDisk(strtolower($type)); // Generate file json

            return \redirect()->route('mutu.generate.index')->with(['success' => 'Berhasil digenerate']);
        } catch (\Throwable $th) {
            return \redirect()->back()->with(['error' => $th->getMessage()]);
        }
    }
}
