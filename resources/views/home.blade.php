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
                                <h4 class="border-bottom pb-2">Insiden Infeksi Bulan
                                    <strong>{{ date('F - Y', strtotime(now())) }}</strong>
                                </h4>
                                <div id="pie_data" data-pie="{{ $infeksiPieChart }}"></div>
                                <div id="chart_pie"></div>
                            </div>
                        </div>
                    </div>
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
                                <div id="plebitis_column" data-spline="{{ $infeksiSplineChart }}"></div>
                                <div id="plebitis_column"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="border-bottom pb-2">Infeksius Tiap Bulan</h4>
                                <div id="line_data" data-line=""></div>
                                <div id="chart_line"></div>
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
                type: 'line'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return val + ' inf';
                }
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'text',
                categories: ['Okt', 'Nov', 'Des', 'Jan']
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
            return [item[0], item[1]]
        });

        var pie_options = {
            series: pieSeries,
            chart: {
                width: 460,
                type: 'pie',
            },
            labels: pieLabels,
            legend: {
                onItemHover: {
                    highlightDataSeries: true
                },
                formatter: (item, key) => {
                    let result = item[0] + ' = ' + item[1];
                    if (item[0] == 'IDO-YA') {
                        return result + ' %';
                    } else {
                        return result + ' &#8240;';
                    }
                }
            },
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

        var plebitis_column_options = {
            series: [{
                name: 'Net Profit',
                data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
            }, {
                name: 'Revenue',
                data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
            }, {
                name: 'Free Cash Flow',
                data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            },
            yaxis: {
                title: {
                    text: '$ (thousands)'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "$ " + val + " thousands"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#plebitis_column"), plebitis_column_options);
        chart.render();
    </script>
@endpush
