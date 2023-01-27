@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('admin/dist/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="h3">List Insiden</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 table-responsive">
                                <div id="url" data-url="{{ route('insiden.data') }}"></div>
                                <table id="insiden-table" class="table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
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
@endsection
@push('javascript')
    <script src="{{ asset('admin/dist/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/dist/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/dist/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/dist/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript">
        let table;
        let ajaxUrl = $('#url').attr('data-url');
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
                    processing: '<i class="fas fa-circle-notch fa-spin fa-fw"></i>'
                },
                ajax: ajaxCallback(),
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal'
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
                "fnInitComplete": function(oSettings, json) {
                    console.log('xhr completed!')
                },
                orders: [
                    []
                ]
                "responsive": true,
                "autoWidth": false,
            });
        }
    </script>
@endpush
