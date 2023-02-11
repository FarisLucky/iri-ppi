@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('admin/dist/datatables/select.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="h4">List Insiden</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 table-responsive">
                                <div id="url" data-url="{{ route('insiden.data') }}"></div>
                                <div class="mb-2">
                                    <strong>Filter</strong>
                                    <div class="row">
                                        <div class="col-2 pr-1">
                                            <select name="filter_year" id="filter_year" class="form-select">
                                                <option value="">- Pilih Tahun -</option>
                                                @php
                                                    $years = date('Y');
                                                    $min = $years - 60;
                                                    $max = $years;
                                                    $filterByYear = isset($year) ? $year : '';
                                                @endphp
                                                @for ($y = $max; $y >= $min; $y--)
                                                    <option value="{{ $y }}"
                                                        {{ $y == $filterByYear ? 'selected' : '' }}>
                                                        {{ $y }}</option>
                                                    @if ($y == '2023')
                                                    @break
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-2 pr-1">
                                        <select name="filter_ruangan" id="filter_ruangan" class="form-select">
                                            <option value="">- Pilih Ruangan -</option>
                                            @foreach ($ruangans as $ruangan)
                                                @if ($ruangan->ruangan !== '')
                                                    <option value="{{ $ruangan->ruangan }}">{{ $ruangan->ruangan }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" id="btn_cari" class="btn btn-primary">
                                            <i class="fas fa-search mr-1"></i>
                                            Cari
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <div class="d-inline-block">
                                        <strong class="d-inline-block mb-1">Tindakan</strong>
                                        <div class="form-group mb-2">
                                            <button type="button" id="btn_verif"
                                                data-url="{{ route('insiden.verif') }}" class="btn btn-sm btn-success"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Verifikasi">
                                                <i class="fas fa-check mr-1"></i>
                                                Verifikasi
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-inline-block">
                                        <button type="button" id="btn_reset" class="btn btn-outline-secondary">
                                            <i class="fas fa-undo-alt mr-1"></i>
                                            Reset
                                        </button>
                                    </div>
                                </div>
                                <table id="insiden-table" class="table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>
                                                <input type="checkbox" class="select-checkbox" id="select_all">
                                            </th>
                                            <th>Tanggal</th>
                                            <th>MR</th>
                                            <th>Nama</th>
                                            <th>Ruangan</th>
                                            <th>LM Rawat</th>
                                            <th>LM Infus</th>
                                            <th>LM KTT</th>
                                            <th>PLEBITIS</th>
                                            <th>ISK</th>
                                            <th>IDO</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    @include('insiden.modal_edit')
@endsection
@prepend('javascript')
    <script src="{{ asset('admin/dist/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/dist/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/dist/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('admin/dist/datatables/dataTables.select.min.js') }}"></script>
    <script type="text/javascript">
        let table;
        const ajaxUrl = $('#url').attr('data-url');
        var myModal = new bootstrap.Modal(document.getElementById('modal-edit'))

        $(function() {
            hideLoader()
            table = initTable('#insiden-table', () => {
                return {
                    url: ajaxUrl
                }
            })
        });

        function initTable(table, ajaxCallback) {
            return $(table).DataTable({
                processing: true,
                serverSide: true,
                language: {
                    processing: '<i class="fas fa-circle-notch fa-spin fa-fw text-primary"></i>'
                },
                lengthMenu: [10, 25, 50, 100, 250, 500],
                ajax: ajaxCallback(),
                deferRender: false,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        orderable: false,
                        searchable: false,
                        defaultContent: "",
                        className: 'select-checkbox',
                        targets: 1
                    },
                    {
                        data: 'tanggal',
                        name: 'ppi.TANGGAL'
                    },
                    {
                        data: 'mr',
                        name: 'ppi.MR'
                    },
                    {
                        data: 'nama',
                        name: 'm_pasien.NAMA'
                    },
                    {
                        data: 'ruangan',
                        name: 'ppi.RUANGAN'
                    },
                    {
                        data: 'lmrawat',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lminfus',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lmktt',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'plebitis',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'isk',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ido',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false
                    },
                ],
                fnInitComplete: function(oSettings, json) {
                    console.log('xhr completed!')
                },
                order: [
                    [2, 'desc']
                ],
                select: {
                    style: 'multi',
                },
                responsive: true,
                autoWidth: false,
                drawCallback: function(settings) {
                    setCheckbox(false)
                }
            });
        }

        // Select Checkbox in TH TAble
        $('#insiden-table').on('click', 'th .select-checkbox', function() {
            // Get all rows with search applied
            if ($(this).prop('checked')) {
                table.rows().select()
                setCheckbox(true)
            } else {
                table.rows().deselect()
                setCheckbox(false)
            }
        });

        // BTN Verif Action
        $('#btn_verif').on('click', function(e) {
            e.preventDefault();
            if (confirm('Apakah ingin diverifikasi ?')) {

                let data = table.rows({
                    selected: true
                }).data();

                if (data.count() > 0) {
                    let dataAttr = [];
                    data.each((item, idx) => {
                        dataAttr.push(item.id)
                    })
                    let urlAttr = $(this).attr('data-url')
                    let typeAttr = "POST"
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        type: typeAttr,
                        url: urlAttr,
                        data: {
                            "insidenList": dataAttr
                        },
                        dataType: "JSON"
                    }).done((response) => {
                        table
                            .row(this)
                            .remove()
                            .draw(false);
                        toastrSuccess('Berhasil diverifikasi')
                    }).fail(errors => {
                        toastrError(errors.responseText)
                    });
                }
            }
        });

        // BTN Cari ACtion
        $('#btn_cari').on('click', function(e) {
            e.preventDefault();

            let year = $('select[name=filter_year]').val();
            let ruangan = $('select[name=filter_ruangan]').val();

            if (year == '' && ruangan == '') {
                toastrError('Tahun atau Ruangan Tidak Boleh Kosong')
                return;
            }

            table.clear().destroy()

            table = initTable('#insiden-table', () => {
                return {
                    url: ajaxUrl,
                    data: {
                        filter_year: year,
                        filter_ruangan: ruangan,
                    }
                }
            })
        });

        // BTN Reset Action
        $('#btn_reset').on('click', function(e) {
            e.preventDefault();

            table.clear().destroy()

            $('select[name=filter_year]').val('').trigger('change');
            $('select[name=filter_ruangan]').val('').trigger('change');

            table = initTable('#insiden-table', () => {
                return {
                    url: ajaxUrl
                }
            })
        });

        // DataTable Edit ACtion
        $('#insiden-table').on('click', '.act-edit', function(e) {
            e.preventDefault();
            showLoader()

            table.rows().deselect()
            setCheckbox(false)

            let urlAttr = $(this).attr('href');
            let typeAttr = "GET"

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                type: typeAttr,
                url: urlAttr,
                dataType: "JSON"
            }).done((response) => {
                $('#InsidenUpdateForm input[name=id]').val(response.data.id)
                $('#InsidenUpdateForm input[name=mr]').val(response.data.mr)
                $('#InsidenUpdateForm input[name=nama]').val(response.data.nama)
                $('#InsidenUpdateForm input[name=ruangan]').val(response.data.ruangan)
                $('#InsidenUpdateForm input[name=lmrawat]').val(response.data.lmrawat)
                $('#InsidenUpdateForm input[name=lminfus]').val(response.data.lminfus)
                $('#InsidenUpdateForm input[name=lmktt]').val(response.data.lmktt)
                $('#InsidenUpdateForm select[name=plebitis]').val(response.data.plebitis).trigger('change')
                $('#InsidenUpdateForm select[name=isk]').val(response.data.isk).trigger('change')
                $('#InsidenUpdateForm select[name=ido]').val(response.data.ido).trigger('change')
                $('#InsidenUpdateForm input[name=verified]').val(response.data.verified).prop('checked')
                hideLoader()
                myModal.show()
            });
        });

        // Datatable delete Action
        $('#insiden-table').on('click', '.act-delete', function(e) {
            e.preventDefault();
            if (confirm('Apakah ingin dihapus ?')) {
                let urlAttr = $(this).attr('href')
                let typeAttr = "DELETE"
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    type: typeAttr,
                    url: urlAttr,
                    dataType: "JSON"
                }).done((response) => {
                    table
                        .row(this)
                        .remove()
                        .draw(false);
                    toastrSuccess('Berhasil dihapus')
                }).fail(errors => {
                    toastrError(errors.responseText)
                });
            }
        });

        function setCheckbox(params) {
            $('.select-checkbox').prop('checked', params);
        }
    </script>
@endprepend
