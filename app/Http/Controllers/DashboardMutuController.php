<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardMutuRequest;
use App\Http\Resources\ApiResource;
use App\Services\DashboardMutuService;
use App\Services\FileService;
use App\Services\GoogleSheetService;
use App\Services\ImpRsMutuService;
use App\Services\ImpUnitMutuService;
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

            $inm = self::filters($params['filter_indikator']);

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

    public function getSubIndikator()
    {
        try {

            $object = self::filters(request()->get('indikator'));
            $subIndikator = $object->indikatorsList()
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
            $object = self::filters(request()->get('indikator'));
            $units = $object->indikatorsList()
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

    public static function filters($indikator)
    {
        $object = null;
        switch ($indikator) {

            case 'IMP-RS':
                $object = new ImpRsMutuService();
                break;

            case 'IMP-UNIT':
                $object = new ImpUnitMutuService();
                break;

            default:
                $object = new InmMutuService();
                break;
        }

        return $object;
    }
}
