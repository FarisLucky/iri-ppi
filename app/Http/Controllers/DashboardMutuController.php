<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardMutuRequest;
use App\Http\Resources\ApiResource;
use App\Services\DashboardMutuService;
use App\Services\FileService;
use App\Services\GoogleSheetService;
use App\Services\ImpRsMutuService;
use App\Services\ImpUnitMutuService;
use App\Services\IndikatorMutuService;
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

            $inm = new IndikatorMutuService($params['filter_indikator'], $params['filter_year']);

            $labels = $inm->label()->readCollection()->flatten();
            $values = $inm->setSubIndikator($params["filter_sub_indikator"])
                ->setUnit($params["filter_unit"])
                ->val()
                ->readCollection()
                ->flatten();
            $values->transform(function ($item) {
                return intval($item);
            });

            $title = $inm->getTitle() . " " . $inm->getUnit();
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

    public function getSubIndikator()
    {
        try {

            $indikator = (new IndikatorMutuService(
                request()->get('filter_indikator'),
                request()->get('filter_year')
            ))
                ->setMonth(request()->get('filter_month'))
                ->indikatorsList();

            $subIndikator = $indikator->subIndikatorsList();

            return new ApiResource([
                'status' => Response::HTTP_OK,
                'messages' => 'Berhasil dimuat',
                'data' => $subIndikator
            ]);
        } catch (\Throwable $th) {
            return dd($th->getMessage());
        }
    }

    public function getUnit()
    {
        try {
            $subIndikator = request()->get('filter_sub_indikator');

            $indikator = (new IndikatorMutuService(
                request()->get('filter_indikator'),
                request()->get('filter_year')
            ))
                ->setMonth(request()->get('filter_month'))
                ->setYear(request()->get('filter_year'))
                ->indikatorsList();

            $units = $indikator->units($subIndikator);

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
