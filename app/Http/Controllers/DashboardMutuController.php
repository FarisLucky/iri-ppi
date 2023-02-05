<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardMutuRequest;
use App\Http\Resources\ApiResource;
use App\Services\DashboardMutuService;
use App\Services\FileService;
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
        $params = '';

        $inm = new INMMutuService();

        $labels = $inm->label()->readCollection()->flatten();
        $values = $inm->setIndikator("Kepatuhan Identifikasi Pasien")
            ->setUnit("Maternal")
            ->val()
            ->readCollection()
            ->flatten();

        $chart = (new DashboardMutuService())
            ->setTitle($inm->getTitle())
            ->setLabel($labels)
            ->setVal($values)
            ->result()
            ->toJson();

        return view('mutu.index', compact('params', 'chart'));
    }

    public function showChart(DashboardMutuRequest $request)
    {
        $params = [];

        if ($request->has('filter_year')) {
            $params['filter_year'] = $request->filter_year;
        }

        if ($request->has('filter_month')) {
            $params['filter_month'] = $request->filter_month;
        }

        if ($request->has('filter_infeksi')) {
            $params['filter_infeksi'] = $request->filter_infeksi;
        }

        try {
            $listIndikator = config('sheets.file');
            $fileName = Arr::get($listIndikator, $request->filter_sub_indikator);
            $indikators = (new FileService($fileName))->read();
            $subIndikator = $indikators->map(function ($item) {
                return array_keys($item)[0];
            });
        } catch (\Throwable $th) {
            return redirect()->route('home')->with(['error' => $th->getMessage()])->withInput();
        }

        return view('home', compact('infeksiColumn', 'infeksiSplineChart', 'params'));
    }

    public function indikator($jenis)
    {
        return response()->json([
            'success' => Response::HTTP_OK,
            'data' => MutuService::indikatorWithUnit($jenis),
        ]);
    }

    public function baca()
    {
        $inm = new INMMutuService();
        $readInm = $inm->setIndikator(request()->get('indikator'))
            ->setUnit(request()->get('unit'))
            ->readCollection();
        dd($readInm);
    }

    public function getSubIndikator()
    {
        try {

            $inm = new INMMutuService();
            $subIndikator = $inm->indikatorsList()
                ->subIndikatorsList();

            return new ApiResource([
                'status' => Response::HTTP_OK,
                'messages' => 'Berhasil dimuat',
                'data' => $subIndikator
            ]);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function getUnit()
    {
        try {

            $subIndikator = request()->get('subIndikator');
            $inm = new INMMutuService();
            $units = $inm->indikatorsList()
                ->units($subIndikator);

            return new ApiResource([
                'status' => Response::HTTP_OK,
                'messages' => 'Berhasil dimuat',
                'data' => $units
            ]);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
}
