@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <h4 class="h4">
                            {{ __('You are logged in!') }}
                        </h4>
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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-2">Insiden Rate <strong>PLEBITIS</strong> Tahun
                                    <strong>{{ now()->format('Y') }}</strong>
                                </h4>
                                <div id="spline_data" data-spline="{{ $infeksiSplineChart }}"></div>
                                <div id="chart_spline"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-2">Insiden Rate <strong>PLEBITIS</strong> Tiap Unit Pada
                                    <strong>{{ now()->format('F - Y') }}</strong>
                                </h4>
                                <div id="plebitis_column" data-column="{{ $infeksiColumn }}"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript">
        $(function() {
            hideLoader()
        });
    </script>
    <script type="text/javascript">
        let splineData = $('#spline_data').attr('data-spline');
        let splineDataParse = JSON.parse(splineData);
        let splineDataSeries = [];
        let splineSeries = Object.entries(splineDataParse).forEach((item, key) => {
            splineDataSeries.push({
                name: item[0],
                data: Object.values(item[1])
            })
        })

        var options = {
            series: splineDataSeries,
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
                    console.log(opts)
                    return val;
                }
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'text',
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agus', 'Sept', 'Okt', 'Nov', 'Des'],
            },
            yaxis: {
                title: {
                    text: 'presentase per mille',
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

        let columnData = $('#plebitis_column').attr('data-column');
        let columnDataParse = JSON.parse(columnData);
        let columnDataSeries = [];
        let columnSeries = Object.entries(columnDataParse.infeksi).forEach((item, key) => {
            columnDataSeries.push({
                name: item[0],
                data: [
                    item[1]
                ]
            })
        })
        let columnCategories = [columnDataParse.bulan]

        var plebitis_column_options = {
            series: columnDataSeries,
            chart: {
                type: 'bar',
                height: 350,
                fontSize: '14px',
                fontFamily: 'Inter',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
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
                categories: columnCategories,
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
