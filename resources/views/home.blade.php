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
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-2">Infeksius Tiap Bulan</h4>
                                <div id="spline_data" data-spline="{{ $infeksiPieChart }}"></div>
                                <div id="chart_spline"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-2">Infeksius Tiap Bulan</h4>
                                <div id="line_data" data-line="{{ $infeksiPieChart }}"></div>
                                <div id="chart_line"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-2">Infeksius Tiap Bulan</h4>
                                <div id="pie_data" data-pie="{{ $infeksiPieChart }}"></div>
                                <div id="chart_pie"></div>
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
        var options = {
            series: [{
                name: 'series1',
                data: [31, 40, 28, 51, 42, 109, 100]
            }, {
                name: 'series2',
                data: [11, 32, 45, 32, 34, 52, 41]
            }],
            colors: ['#9b5de5', '#f15bb5'],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return val + ' insiden'
                }
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z",
                    "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                    "2018-09-19T06:30:00.000Z"
                ]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart_spline"), options);
        chart.render();

        var options = {
            series: [{
                    name: "High - 2013",
                    data: [28, 29, 33, 36, 32, 32, 33]
                },
                {
                    name: "Low - 2013",
                    data: [12, 11, 14, 18, 17, 13, 13]
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#1982c4', '#ffca3a'],
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return val + ' insiden'
                }
            },
            stroke: {
                curve: 'smooth'
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            markers: {
                size: 1
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                title: {
                    text: 'Month'
                }
            },
            yaxis: {
                title: {
                    text: 'Temperature'
                },
                min: 5,
                max: 40
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart_line"), options);
        chart.render();


        let pieData = $('#pie_data').attr('data-pie');
        let pieDataParse = JSON.parse(pieData);
        let pieSeries = Object.values(pieDataParse);
        let pieLabels = Object.entries(pieDataParse).map((item, key) => {
            return [item[0] + ' = ' + item[1]]
        });

        var pie_options = {
            series: pieSeries,
            chart: {
                width: 560,
                type: 'pie',
            },
            labels: pieLabels,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#chart_pie"), pie_options);
        chart.render();
    </script>
@endpush
