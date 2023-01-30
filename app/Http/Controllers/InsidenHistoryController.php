<?php

namespace App\Http\Controllers;

use App\Models\Insiden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class InsidenHistoryController extends Controller
{
    public function data()
    {
        $insidens = Insiden::selectInsiden()
            ->joinPasien()
            ->where('verified', Insiden::VERIFIED);

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
        return view('history.index', compact('ruangans'));
    }
}
