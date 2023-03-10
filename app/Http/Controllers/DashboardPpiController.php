<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardPpiController extends Controller
{
    public function index()
    {
        $bulan = "januari";
        $search = "IDO";
        $data = $this->data();
        $infeksi = [
            "tahunan_label" => [],
            "tahunan_value" => [],
            "bulanan_label" => [],
            "bulanan_value" => [
                "data" => [],
            ],
        ];
        $params = '';
        // foreach ($data as $i) {
        //     $m = ucfirst($i["bulan"]);
        //     foreach ($i["data_tahunan"] as $v) {
        //         if ($v["type"] == $search) {
        //             array_push($infeksi["tahunan_label"], $m);
        //             array_push($infeksi["tahunan_value"], $v["val"]);
        //         }
        //     }
        //     if ($i["bulan"] == $bulan) {
        //         $infeksi["bulanan_name"] = $m;
        //         foreach ($i["data_bulanan"] as $k => $v) {
        //             foreach ($v as $key => $value) {
        //                 if ($value["type"] == $search) {
        //                     array_push($infeksi["bulanan_label"], $k);
        //                     array_push($infeksi["bulanan_value"]["data"], $value["val"]);
        //                 }
        //             }
        //         }
        //     }
        // }

        return view('ppi.index', compact('infeksi', 'params'));
    }
    public function showChart()
    {
        $tahun = request()->input('filter_year');
        $bulan = request()->input('filter_month');
        $search = request()->input('filter_infeksi');
        $data = $this->data();
        $infeksi = [
            "tahunan_label" => [],
            "tahunan_value" => [],
            "bulanan_label" => [],
            "bulanan_value" => [
                "data" => [],
            ],
        ];
        $params = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'infeksi' => $search
        ];
        foreach ($data as $i) {
            $m = ucfirst($i["bulan"]);
            foreach ($i["data_tahunan"] as $v) {
                if ($v["type"] == $search) {
                    array_push($infeksi["tahunan_label"], $m);
                    array_push($infeksi["tahunan_value"], $v["val"]);
                }
            }
            if ($i["bulan"] == $bulan) {
                $infeksi["bulanan_name"] = $m;
                foreach ($i["data_bulanan"] as $k => $v) {
                    foreach ($v as $key => $value) {
                        if ($value["type"] == $search) {
                            array_push($infeksi["bulanan_label"], $k);
                            array_push($infeksi["bulanan_value"]["data"], $value["val"]);
                        }
                    }
                }
            }
        }

        return view('ppi.index', compact('infeksi', 'params'));
    }

    public function data()
    {
        return [
            [
                "tahun" => "2023",
                "bulan" => "januari",
                "data_bulanan" => [
                    "R.ANAK" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.VK" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.GENERAL" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 9.9,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.NEO" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.MATERNAL" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 1.7,
                        ],
                    ],
                    "R.PAVILIUN" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                ],
                "data_tahunan" => [
                    [
                        "type" => "PLEBITIS",
                        "val" => 7.7,
                    ],
                    [
                        "type" => "ISK",
                        "val" => 0,
                    ],
                    [
                        "type" => "IDO",
                        "val" => 0.9,
                    ],
                ],
            ],
            [
                "tahun" => "2023",
                "bulan" => "februari",
                "data_bulanan" => [
                    "R.ANAK" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.VK" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.GENERAL" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 8.3,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.NEO" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 17.5,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                    "R.MATERNAL" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0.85,
                        ],
                    ],
                    "R.PAVILIUN" => [
                        [
                            "type" => "PLEBITIS",
                            "val" => 0,
                        ],
                        [
                            "type" => "ISK",
                            "val" => 0,
                        ],
                        [
                            "type" => "IDO",
                            "val" => 0,
                        ],
                    ],
                ],
                "data_tahunan" => [
                    [
                        "type" => "PLEBITIS",
                        "val" => 4.6,
                    ],
                    [
                        "type" => "ISK",
                        "val" => 0,
                    ],
                    [
                        "type" => "IDO",
                        "val" => 0.5,
                    ],
                ],
            ],
        ];
    }
}
