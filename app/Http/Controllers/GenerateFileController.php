<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use App\Services\IndikatorMutuService;
use App\Services\MutuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GenerateFileController extends Controller
{

    public function index()
    {
        if (Gate::allows('supersu')) {
            return view('generate.index');
        }
    }
    public function generate(Request $request)
    {
        if (Gate::allows('supersu')) {
            try {
                $type = $request->filter_indikator;

                $fileService = new MutuService(
                    new IndikatorMutuService($request->filter_indikator, $request->filter_year)
                );

                $fileService
                    ->setRange(config('sheets.sub-indikator.' . $type))
                    ->setJenisIndikator($type);

                $fileService->getData()
                    ->indikatorWithUnit()
                    ->saveToDisk(strtolower($type)); // Generate file json

                return redirect()
                    ->route('mutu.generate.index')
                    ->with(['success' => 'Berhasil digenerate']);
            } catch (\Throwable $th) {
                return redirect()
                    ->back()
                    ->with(['error' => $th->getMessage()]);
            }
        }
    }
}
