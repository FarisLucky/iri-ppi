<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardMutuRequest;
use App\Http\Resources\ApiResource;
use App\Services\DashboardMutuService;
use App\Services\FileService;
use App\Services\GoogleSheetService;
use App\Services\InmMutuService;
use App\Services\MutuService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Revolution\Google\Sheets\Facades\Sheets;

class DashboardMutuController extends Controller
{
    public function index()
    {
        $params = "";
        $chart = "";

        return view('mutu.index', compact('params', 'chart'));
    }

    public function showChart(DashboardMutuRequest $request)
    {
        try {
            $params = $request->validated();

            $inm = new InmMutuService();
            $labels = $inm->label()->readCollection()->flatten();
            $values = $inm->setIndikator($params["filter_sub_indikator"])
                ->setUnit($params["filter_unit"])
                ->val()
                ->readCollection()
                ->flatten();

            $title = $inm->getTitle() . " Unit " . $inm->getUnit();
            $chart = (new DashboardMutuService())
                ->setTitle($title)
                ->setLabel($labels)
                ->setVal($values)
                ->result()
                ->toJson();

            return view('mutu.index', compact('params', 'chart'))->with($request->all());
        } catch (\Throwable $th) {

            return redirect()
                ->route('mutu.dashboard')
                ->with(['error' => $th->getMessage()])
                ->withInput();
        }
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
        $inm = new InmMutuService();
        $readInm = $inm->setIndikator(request()->get('indikator'))
            ->setUnit(request()->get('unit'))
            ->readCollection();
        dd($readInm);
    }

    public function getSubIndikator()
    {
        try {

            $inm = new InmMutuService();
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
            $inm = new InmMutuService();
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
