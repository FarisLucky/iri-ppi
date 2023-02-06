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
                                Generate File
                            </button>
                            <a href="{{ route('home') }}" class="cs-btn-accordion text-secondary">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionFilter">
                            <div class="accordion-body">
                                <form action="{{ route('mutu.generate.file') }}" method="POST">
                                    @csrf
                                    <div class="row align-items-end">
                                        @php
                                            $indikators = config('sheets.spreadsheet_id');
                                        @endphp
                                        <div class="col-md-2 mb-1 pr-0">
                                            <label for="filter_indikator">Jenis Indikator</label>
                                            <select name="filter_indikator" id="filter_indikator" class="form-control"
                                                required>
                                                <option value="">Pilih Jenis Indikator</option>
                                                @foreach ($indikators as $key => $d)
                                                    <option value="{{ $key }}">
                                                        {{ $key }}</option>
                                                @endforeach
                                            </select>
                                            @error('filter_indikator')
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
        </div>
    </div>
@endsection
@push('javascript')
    <script>
        $(function() {
            hideLoader()
        });
    </script>
@endpush
