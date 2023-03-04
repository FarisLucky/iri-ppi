@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="accordion" id="accordionFilter">
                    <div class="accordion-item">
                        <h2 class="accordion-header d-flex" id="headingOne">
                            <button class="accordion-button d-inline-flex" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Dashboard Indikator Mutu KMKP RS Graha Sehat
                            </button>
                            <a href="{{ route('mutu.dashboard') }}" class="cs-btn-accordion text-secondary">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                        </h2>
                        @php
                            $showChart = isset($infeksiSplineChart) && $infeksiSplineChart != '';

                            function selected($name, array $options)
                            {
                                return in_array($name, $options) ? 'selected' : '';
                            }
                        @endphp
                        <div id="collapseOne" class="accordion-collapse collapse {{ $showChart ? '' : 'show' }}"
                            aria-labelledby="headingOne" data-bs-parent="#accordionFilter">
                            <div class="accordion-body">
                                <form action="{{ route('mutu.filter.dashboard') }}" method="POST">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_year">Tahun</label>
                                            <select name="filter_year" id="filter_year" class="form-control" required>
                                                <option value="">Pilih Data</option>
                                                @php
                                                    $year = date('Y');
                                                    $min = $year - 30;
                                                    $max = $year;
                                                @endphp
                                                @for ($y = $max; $y >= $min; $y--)
                                                    <option value="{{ $y }}"
                                                        {{ selected($y, [optional($params)['filter_year'], old('filter_year')]) }}>
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
                                        <select name="filter_month" id="filter_month" class="form-control" required>
                                            <option value="">Pilih Data</option>
                                            @php
                                                $maxMonth = config('sheets.bulan');
                                            @endphp
                                            @foreach ($maxMonth as $key => $m)
                                                <option value="{{ $key }}"
                                                    {{ selected($key, [optional($params)['filter_month'], old('filter_month')]) }}>
                                                    {{ $m }}</option>
                                            @endforeach
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
                                        <select name="filter_indikator" id="filter_indikator" class="form-control"
                                            required>
                                            <option value="">Pilih Data</option>
                                            @foreach ($indikators as $key => $d)
                                                <option value="{{ $key }}"
                                                    {{ selected($y, [optional($params)['filter_indikator'], old('filter_indikator')]) }}>
                                                    {{ $key }}</option>
                                            @endforeach
                                        </select>
                                        @error('filter_indikator')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-1 pr-0">
                                        <label for="filter_sub_indikator">Sub Indikator</label>
                                        <div id="data_sub_indikator"
                                            data-sub="{{ optional($params)['filter_indikator'] }}"></div>
                                        <div id="sub_indikator_url"
                                            data-url="{{ route('mutu.indikator.subIndikator.unit') }}">
                                        </div>
                                        <select name="filter_sub_indikator" id="filter_sub_indikator"
                                            class="form-control" required>
                                            <option value="">Pilih Data</option>
                                        </select>
                                        @error('filter_sub_indikator')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-1 pr-0">
                                        <label for="filter_unit">Unit</label>
                                        <input type="hidden" name="unit_hide" id="unit_hide">
                                        <select name="filter_unit" id="filter_unit" class="form-control" required>
                                            <option value="">Pilih Data</option>
                                        </select>
                                        @error('filter_unit')
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
        {{-- {{ dd($params) }} --}}
        <div class="col-md-12 mt-2 {{ $params !== '' ? 'show' : 'hide' }}">
            <div class="card">
                <div class="card-body">
                    <h4 class="border-bottom pb-2">
                        <strong>{{ optional(json_decode($chart, true))['title'] . ' (' . optional($params)['filter_sub_indikator'] . ' )' }}</strong>
                        Tahun
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
<script src="{{ asset('admin/dist/apexcharts/apexcharts.min.js') }}"></script>
<script type="text/javascript">
    let dataLabels = {};
    $(function() {
        hideLoader()
        $('#filter_indikator').on('change', function(e) {
            e.preventDefault();
            showLoader()
            let indikatorUrl = $('#jenis_url').attr('data-url')
            let dataIndikator = {
                filter_indikator: $(this).val(),
                filter_year: $("#filter_year").val(),
                filter_month: $("#filter_month").val()
            }
            if (dataIndikator.indikator == '') {
                ["#filter_sub_indikator", "#filter_unit"].forEach(selectEl => {
                    $(selectEl)
                        .empty()
                        .append(new Option('Pilih Data', ''))
                })

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
                    let options = resp.data;
                    ["#filter_sub_indikator", "#filter_unit"].forEach(selectEl => {
                        $(selectEl)
                            .empty()
                            .append(new Option('Pilih Data', ''))
                    })

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
                filter_sub_indikator: $(this).val(),
                filter_indikator: $("#filter_indikator").val(),
                filter_year: $("#filter_year").val(),
                filter_month: $("#filter_month").val()
            }
            if (dataIndikator.indikator == '') {
                $('#filter_unit')
                    .empty()
                    .append(new Option('Pilih Data', ''))

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
                        .append(new Option('Pilih Data', ''))

                    options.forEach(sub => {
                        $('#filter_unit').append(
                            new Option(
                                Object.keys(sub)[0],
                                Object.values(sub)[0]
                            )
                        )
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
    let showLabel = dataLabels

    var options = {
        series: [{
            name: title,
            // type: 'column',
            data: seriesData
        }],
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: true
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '90%',
                endingShape: 'rounded'
            },
        },
        colors: ["#118ab2"],
        dataLabels: showLabel,
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
            title: {
                text: 'Hari ke - n dalam bulan ' + $("#filter_month option:selected").text(),
            }
        },
        yaxis: {
            title: {
                text: 'Presentase ' + $("#data_sub_indikator").attr("data-sub"),
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
