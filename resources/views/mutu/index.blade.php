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
                                                        {{ $y == optional($params)['filter_year'] ? 'selected' : '' }}>
                                                        {{ $y }}</option>
                                                @endfor
                                            </select>
                                            @error('filter_year')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @php
                                            $indikators = config('sheets.spreadsheet_id');
                                        @endphp
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_infeksi">Jenis Indikator</label>
                                            <select name="filter_infeksi" id="filter_infeksi" class="form-control">
                                                <option value="">Pilih Jenis Indikator</option>
                                                @foreach ($indikators as $key => $d)
                                                    <option value="{{ $d }}"> {{ $key }}</option>
                                                @endforeach
                                            </select>
                                            @error('filter_infeksi')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_infeksi">Sub Indikator</label>
                                            <select name="filter_infeksi" id="filter_infeksi" class="form-control">
                                                <option value="">Pilih Sub Indikator</option>
                                                @foreach ($subIndikator as $d)
                                                    <option value="{{ $d['header'] }}"> {{ $d['header'] }}</option>
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
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="border-bottom pb-2">Indikator Nasional <strong>Mutu</strong> Tahun
                            <strong>{{ now()->format('Y') }}</strong>
                        </h4>
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
            $('#filter_infeksi').on('change', function(e) {
                e.preventDefault();
                alert('test')
            });
        });
    </script>
    <script>
        var options = {
            series: [{
                name: "Desktops",
                data: [10, 41, 35, 51, 49, 62, 69, 91, 148, 45, 120, 80]
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
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
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endpush
