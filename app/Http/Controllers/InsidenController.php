<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInsidenRequest;
use App\Models\Insiden;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InsidenController extends Controller
{

    public function data()
    {
        $insidens = Insiden::selectInsiden()
            ->joinPasien()
            ->where('verified', 0);

        if (
            request()->has('filter_ruangan') &&
            !is_null(request()->input('filter_ruangan'))
        ) {
            $insidens->where('ppi.RUANGAN', request()->input('filter_ruangan'));
        }

        if (
            request()->has('filter_year') &&
            !is_null(request()->input('filter_year'))
        ) {
            $insidens->whereYear('ppi.TANGGAL', request()->input('filter_year'));
        }

        return DataTables::of($insidens)
            ->addIndexColumn()
            ->editColumn('tanggal', function ($insiden) {
                return $insiden->tanggal->isoFormat('L');
            })
            ->addColumn('mr', function ($insiden) {
                return $insiden->mr;
            })
            ->addColumn('aksi', function ($insiden) {
                return '<div class="table-action">
                    <a href="' . route('insiden.edit', $insiden->id) . '" class="act-edit">
                        <i class="fas fa-edit text-info"></i>
                    </a>
                    <a href="' . route('insiden.destroy', $insiden->id) . '" class="act-delete">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </div>';
            })
            ->rawColumns(['aksi', 'select_id'])
            ->make(true);
    }

    public function index()
    {
        $ruangans = Cache::remember('ruangans', 3600, function () {
            return Insiden::select('ruangan')
                ->groupBy('ruangan')
                ->get();
        });
        return view('insiden.index', compact('ruangans'));
    }

    public function edit($id)
    {
        try {

            DB::beginTransaction();
            $insiden = Insiden::where('id', $id)
                ->selectInsiden()
                ->joinPasien()
                ->first();
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_OK,
                'messages' => 'BERHASIL',
                'data' => $insiden
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'messages' => $th->getMessage()
            ]);
        }
    }

    public function update(UpdateInsidenRequest $request, $id = null)
    {
        try {

            $input = array_change_key_case($request->validated(), CASE_UPPER);
            $input += [
                'VERIFIED' => !is_null($request->verified) ? Insiden::VERIFIED : 0,
                'UPDATED_AT' => now()->format('Y-m-d H:i:s')
            ];
            DB::beginTransaction();
            $insiden = Insiden::where('id', $request->id);
            $insiden->update($input);
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_OK,
                'messages' => 'BERHASIL',
                'data' => $input
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'messages' => $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {

            DB::beginTransaction();
            $insiden = Insiden::where('id', $id);
            $insiden->delete();
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_NO_CONTENT,
                'messages' => 'BERHASIL',
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'messages' => $th->getMessage()
            ]);
        }
    }

    public function verif(Request $request)
    {
        $insidenList = $request->insidenList;
        try {

            DB::beginTransaction();
            $insidens = Insiden::whereIn('id', $insidenList)
                ->update([
                    'verified' => Insiden::VERIFIED
                ]);
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_OK,
                'messages' => 'BERHASIL DIVERIFIKASI',
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'messages' => $th->getMessage()
            ]);
        }
    }
}
