@extends('layouts.app')
{{-- {{ Illuminate\Support\Carbon::now()->locale('id')->isoFormat('MMMM') }} --}}
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 mb-2">
                <div class="accordion" id="accordionFilter">
                    <div class="accordion-item">
                        <h2 class="accordion-header d-flex" id="headingOne">
                            <button class="accordion-button d-inline-flex" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Dashboard PPI RS Graha Sehat
                            </button>
                            <a href="{{ route('ppi.dashboard') }}" class="cs-btn-accordion text-secondary">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                        </h2>
                        @php
                            $showChart = isset($infeksiSplineChart) && $infeksiSplineChart != '';
                        @endphp
                        <div id="collapseOne" class="accordion-collapse collapse {{ $showChart ? '' : 'show' }}"
                            aria-labelledby="headingOne" data-bs-parent="#accordionFilter">
                            <div class="accordion-body">
                                <form action="{{ route('ppi.filter.dashboard') }}" method="POST">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_year">Tahun</label>
                                            <select name="filter_year" id="filter_year" class="form-control" required>
                                                <option value="">Pilih Data</option>
                                                <option value="2023"
                                                    {{ optional($params)['tahun'] == '2023' ? 'selected' : '' }}>2023
                                                </option>
                                            </select>
                                            @error('filter_year')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_month">Bulan</label>
                                            <select name="filter_month" id="filter_month" class="form-control" required>
                                                <option value="">Pilih Data</option>
                                                <option value="januari"
                                                    {{ optional($params)['bulan'] == 'januari' ? 'selected' : '' }}>Januari
                                                </option>
                                                <option value="februari"
                                                    {{ optional($params)['bulan'] == 'februari' ? 'selected' : '' }}>
                                                    Februari
                                                </option>
                                            </select>
                                            @error('filter_month')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_infeksi">Jenis Infeksi</label>
                                            <select name="filter_infeksi" id="filter_infeksi" class="form-control" required>
                                                <option value="">Pilih Data</option>
                                                @php
                                                    $jenisInfeksi = ['IDO', 'PLEBITIS', 'ISK'];
                                                @endphp
                                                @foreach ($jenisInfeksi as $jenis)
                                                    <option value="{{ $jenis }}"
                                                        {{ in_array($jenis, [optional($params)['infeksi']]) ? 'selected' : '' }}>
                                                        {{ $jenis }}</option>
                                                @endforeach
                                            </select>
                                            @error('filter_infeksi')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mt-1">
                                            <button type="submit" class="btn btn-primary">Terapkan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 {{ $params == '' ? 'hide' : '' }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-1">Insiden Rate
                                    <strong>{{ optional($params)['filter_infeksi'] }}</strong> Tahun
                                    <strong>{{ optional($params)['filter_year'] }}</strong>
                                </h4>
                                <div id="spline_data" data-infeksi="{{ json_encode($infeksi) }}"></div>
                                <div id="chart_spline"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-1">Insiden Rate
                                    <strong>{{ optional($params)['filter_infeksi'] }}</strong> Tiap Unit Pada
                                    <strong>{{ (!is_null(optional($params)['filter_month']) ? date('F', mktime(0, 0, 0, $params['filter_month'], 1)) : '') . ' - ' . optional($params)['filter_year'] }}</strong>
                                </h4>
                                <div id="plebitis_column"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('javascript')
    <script src="{{ asset('admin/dist/apexcharts/apexcharts.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            hideLoader()
        });
    </script>
    <script type="text/javascript">
        let infeksi = $('#spline_data').attr('data-infeksi');
        let parseInfeksi = JSON.parse(infeksi);
        let tahunan_lbl = parseInfeksi.tahunan_label;
        let tahunan_val = parseInfeksi.tahunan_value;
        let bulanan_lbl = parseInfeksi.bulanan_label;
        let bulanan_val = parseInfeksi.bulanan_value;
        let bulanan_name = parseInfeksi.bulanan_name;

        var options = {
            series: [{
                data: tahunan_val
            }],
            colors: ['#9b5de5', '#2a9d8f', '#ff6c0e', '#9ef01a', '#f15bb5', '#ef233c', '#f4acb7', '#84a59d', '#7678ed'],
            chart: {
                height: 350,
                type: 'line',
                fontSize: '14px',
                fontFamily: 'Inter',
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return val;
                }
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'text',
                categories: tahunan_lbl,
            },
            yaxis: {
                title: {
                    text: 'presentase per mille',
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " &permil;"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart_spline"), options);
        chart.render();

        let seriesBulanan = Object.values(bulanan_lbl).map((val, key) => {
            return {
                name: val.toString(),
                data: [bulanan_val.data[key].toString()]
            }
        });

        var plebitis_column_options = {
            series: seriesBulanan,
            chart: {
                type: 'bar',
                height: 350,
                fontSize: '12px',
                fontFamily: 'Inter',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '80%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [bulanan_name],
            },
            yaxis: {
                title: {
                    text: 'presentase per mille',
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " &permil;"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#plebitis_column"), plebitis_column_options);
        chart.render();
    </script>
@endpush
