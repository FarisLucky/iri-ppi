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
                                Dashboard Insiden Infeksi PPI RS Graha Sehat
                            </button>
                            <a href="{{ route('home') }}" class="cs-btn-accordion text-secondary">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                        </h2>
                        @php
                            $showChart = isset($infeksiSplineChart) && $infeksiSplineChart != '';
                        @endphp
                        <div id="collapseOne" class="accordion-collapse collapse {{ $showChart ? '' : 'show' }}"
                            aria-labelledby="headingOne" data-bs-parent="#accordionFilter">
                            <div class="accordion-body">
                                <form action="{{ route('insiden.dashboard') }}" method="POST">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_year">Tahun</label>
                                            <select name="filter_year" id="filter_year" class="form-control">
                                                <option value="">Pilih Tahun</option>
                                                @php
                                                    $year = date('Y');
                                                    $min = $year - 60;
                                                    $max = $year;
                                                @endphp
                                                @for ($y = $max; $y >= $min; $y--)
                                                    <option value="{{ $y }}"
                                                        {{ in_array($y, [optional($params)['filter_year'], old('filter_year')]) ? 'selected' : '' }}>
                                                        {{ $y }}</option>
                                                    @if ($y == '2023')
                                                    @break
                                                @endif
                                            @endfor
                                        </select>
                                        @error('filter_year')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-1 pr-0">
                                        <label for="filter_month">Bulan</label>
                                        <select name="filter_month" id="filter_month" class="form-control">
                                            <option value="">Pilih Bulan</option>
                                            @php
                                                $maxMonth = 12;
                                            @endphp
                                            @for ($y = 1; $y <= $maxMonth; $y++)
                                                <option value="{{ $y }}"
                                                    {{ in_array($y, [optional($params)['filter_month'], old('filter_month')]) ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $y, 1)) }}</option>
                                            @endfor
                                        </select>
                                        @error('filter_month')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-1 pr-0">
                                        <label for="filter_infeksi">Jenis Infeksi</label>
                                        <select name="filter_infeksi" id="filter_infeksi" class="form-control">
                                            <option value="">Pilih Jenis Infeksi</option>
                                            @php
                                                $jenisInfeksi = ['IDO', 'PLEBITIS', 'ISK'];
                                            @endphp
                                            @foreach ($jenisInfeksi as $jenis)
                                                <option value="{{ $jenis }}"
                                                    {{ in_array($jenis, [optional($params)['filter_infeksi'], old('filter_infeksi')]) ? 'selected' : '' }}>
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
        <div class="col-md-12">
            <div class="row">
                {{-- <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-2">Insiden Infeksi Bulan
                                    <strong>{{ date('F - Y', strtotime(now())) }}</strong>
                                </h4>
                                <div id="pie_data" data-pie="{{ $infeksiPieChart }}"></div>
                                <div id="chart_pie"></div>
                            </div>
                        </div>
                    </div> --}}
                <div class="col-md-12 {{ $showChart ? '' : 'd-none' }}">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="border-bottom pb-1">Insiden Rate
                                <strong>{{ optional($params)['filter_infeksi'] }}</strong> Tahun
                                <strong>{{ optional($params)['filter_year'] }}</strong>
                            </h4>
                            <div id="spline_data" data-spline="{{ optional($infeksiSplineChart)['dataSeries'] }}"
                                data-spline-label="{{ optional($infeksiSplineChart)['labelSeries'] }}"
                                data-spline-type="{{ optional($infeksiSplineChart)['type'] }}"></div>
                            <div id="chart_spline"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 {{ $showChart ? '' : 'd-none' }}">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="border-bottom pb-1">Insiden Rate
                                <strong>{{ optional($params)['filter_infeksi'] }}</strong> Tiap Unit Pada
                                <strong>{{ (!is_null(optional($params)['filter_month']) ? date('F', mktime(0, 0, 0, $params['filter_month'], 1)) : '') . ' - ' . optional($params)['filter_year'] }}</strong>
                            </h4>
                            <div id="column_data" data-column="{{ optional($infeksiColumn)['dataSeries'] }}"
                                data-column-label="{{ optional($infeksiColumn)['labelSeries'] }}"></div>
                            <div id="plebitis_column">
                            </div>
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
    let splineData = $('#spline_data').attr('data-spline');
    let splineLabel = $('#spline_data').attr('data-spline-label');
    let splineType = $('#spline_data').attr('data-spline-type');
    let dataSpline = JSON.parse(splineData);
    let labelSpline = JSON.parse(splineLabel);

    var options = {
        series: [{
            name: splineType,
            data: dataSpline
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
            categories: labelSpline,
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

    // let pieData = $('#pie_data').attr('data-pie');
    // let pieDataParse = JSON.parse(pieData);
    // let pieSeries = Object.values(pieDataParse);
    // let pieLabels = Object.entries(pieDataParse).map((item, key) => {
    //     return [item[0], item[1]]
    // });

    // var pie_options = {
    //     series: pieSeries,
    //     chart: {
    //         width: 460,
    //         type: 'pie',
    //         fontSize: '14px',
    //         fontFamily: 'Inter',
    //     },
    //     labels: pieLabels,
    //     legend: {
    //         onItemHover: {
    //             highlightDataSeries: true
    //         },
    //         formatter: (item, key) => {
    //             let result = item[0] + ' = ' + item[1];
    //             if (item[0] == 'IDO-YA') {
    //                 return result + ' %';
    //             } else {
    //                 return result + ' &#8240;';
    //             }
    //         }
    //     },
    //     responsive: [{
    //         breakpoint: 480,
    //         options: {
    //             chart: {
    //                 width: 200
    //             },
    //             legend: {
    //                 position: 'bottom'
    //             }
    //         }
    //     }]
    // };

    // var chart = new ApexCharts(document.querySelector("#chart_pie"), pie_options);
    // chart.render();

    let columnData = $('#column_data').attr('data-column');
    let columnLabel = $('#column_data').attr('data-column-label');
    let columnDataParse = JSON.parse(columnData);
    let columnLabelParse = JSON.parse(columnLabel);
    let columnDataSeries = [];
    let dataToArray = Object.entries(columnDataParse)
    let columnSeries = dataToArray.map((item, key) => {
        return {
            name: item[0],
            data: Object
                .entries(item[1])
                .map((val, key) => val[1]),
        }
    })
    console.log(dataToArray)
    let columnLabelSeries = Object
        .entries(dataToArray[0][1])
        .map((val, key) => val[0])
    console.log(columnLabelSeries)
    // throw new Error('tset');

    var plebitis_column_options = {
        series: columnSeries,
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
            categories: columnLabelSeries,
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
