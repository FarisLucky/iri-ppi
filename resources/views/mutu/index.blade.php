@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="accordion" id="accordionFilter">
                    <div class="accordion-item">
                        <h2 class="accordion-header d-flex" id="headingOne">
                            <button class="accordion-button d-inline-flex" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"
                                style="width: 95%">
                                Dashboard Indikator Mutu KMKP RS Graha Sehat
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
                                <form action="{{ route('mutu.dashboard') }}" method="POST">
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
                                                        {{ $y == optional($params)['filter_year'] ? 'selected' : '' }}>
                                                        {{ $y }}</option>
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
                                        @php
                                            $indikators = config('sheets.spreadsheet_id');
                                        @endphp
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_indikator">Jenis Indikator</label>
                                            <div id="jenis_url" data-url="{{ route('mutu.indikator.subIndikator') }}">
                                            </div>
                                            <select name="filter_indikator" id="filter_indikator" class="form-control">
                                                <option value="">Pilih Jenis Indikator</option>
                                                @foreach ($indikators as $key => $d)
                                                    <option value="{{ $key }}"> {{ $key }}</option>
                                                @endforeach
                                            </select>
                                            @error('filter_indikator')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_sub_indikator">Sub Indikator</label>
                                            <div id="sub_indikator_url"
                                                data-url="{{ route('mutu.indikator.subIndikator.unit') }}">
                                            </div>
                                            <select name="filter_sub_indikator" id="filter_sub_indikator"
                                                class="form-control">
                                                <option value="">Pilih Sub Indikator</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_unit">Unit</label>
                                            <select name="filter_unit" id="filter_unit" class="form-control">
                                                <option value="">Pilih Unit</option>
                                            </select>
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
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="border-bottom pb-2"><strong>{{ json_decode($chart, true)['title'] }}</strong> Tahun
                            <strong>{{ now()->format('Y') }}</strong>
                        </h4>
                        <div id="chart_data" data-chart="{{ $chart }}"></div>
                        <div id="chart"></div>
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
            $('#filter_indikator').on('change', function(e) {
                e.preventDefault();
                showLoader()
                let indikatorUrl = $('#jenis_url').attr('data-url')
                let dataIndikator = {
                    indikator: $(this).val()
                }
                if (dataIndikator.indikator == '') {
                    $('#filter_sub_indikator')
                        .empty()
                        .append(new Option('Pilih Sub Indikator', ''))

                    hideLoader()
                    return;
                }
                $.ajax({
                        type: "GET",
                        url: indikatorUrl,
                        data: dataIndikator,
                        dataType: "JSON"
                    })
                    .done(resp => {
                        let options = resp.data
                        $('#filter_sub_indikator')

                            .empty()
                            .append(new Option('Pilih Sub Indikator', ''))

                        options.forEach(sub => {
                            $('#filter_sub_indikator').append(new Option(sub, sub))
                        });
                        hideLoader()
                    })
                    .fail(err => {
                        hideLoader()
                        console.log(err)
                    });
            });

            $('#filter_sub_indikator').on('change', function(e) {
                e.preventDefault();
                showLoader()
                let indikatorUrl = $('#sub_indikator_url').attr('data-url')
                let dataIndikator = {
                    subIndikator: $(this).val()
                }
                if (dataIndikator.indikator == '') {
                    $('#filter_unit')
                        .empty()
                        .append(new Option('Pilih Units', ''))

                    hideLoader()
                    return;
                }
                $.ajax({
                        type: "GET",
                        url: indikatorUrl,
                        data: dataIndikator,
                        dataType: "JSON"
                    })
                    .done(resp => {
                        hideLoader()
                        let options = resp.data

                        $('#filter_unit')
                            .empty()
                            .append(new Option('Pilih Units', ''))

                        options.forEach(sub => {
                            $('#filter_unit').append(new Option(sub, sub))
                        });
                        hideLoader()
                    })
                    .fail(err => {
                        hideLoader()
                        console.log(err)
                    });
            });
        });
    </script>
    <script>
        const chartData = $('#chart_data').attr('data-chart');
        let data = JSON.parse(chartData);
        let title = data.title
        let label = data.label
        let seriesData = data.val

        var options = {
            series: [{
                name: title,
                data: seriesData
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: true
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => {
                    return val + " %";
                }
            },
            stroke: {
                curve: 'straight'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: label,
            },
            yaxis: {
                title: {
                    text: 'Presentase',
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " %"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endpush
