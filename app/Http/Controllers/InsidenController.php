<?php

namespace App\Http\Controllers;

use App\Models\Insiden;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InsidenController extends Controller
{

    public function data()
    {
        $insidens = Insiden::selectInsiden()->pasien();

        return DataTables::of($insidens)
            ->addIndexColumn()
            ->addColumn('aksi', function ($insiden) {
                return 'Aksi';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function index()
    {
        return view('insiden.index');
    }
}
