<div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Insiden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('insiden.update') }}" method="POST" id="InsidenUpdateForm">
                    @method('PUT')
                    <input type="hidden" name="id" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-1 row">
                                <label class="col-sm-3 col-form-label">MR</label>
                                <div class="col-sm-8">
                                    <input type="text" name="mr" readonly class="form-control" value="mr">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-8">
                                    <input type="text" name="nama" readonly class="form-control" value="nama">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="col-sm-3 col-form-label">Ruangan</label>
                                <div class="col-sm-8">
                                    <input type="text" name="ruangan" readonly class="form-control" value="ruangan">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1 row">
                                <label class="col-sm-5 col-form-label">Lama Rawat</label>
                                <div class="col-sm-7">
                                    <input type="text" name="lmrawat" class="form-control">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="col-sm-5 col-form-label">Lama Infus</label>
                                <div class="col-sm-7">
                                    <input type="text" name="lminfus" class="form-control">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="col-sm-5 col-form-label">Lama KTT</label>
                                <div class="col-sm-7">
                                    <input type="text" name="lmktt" class="form-control">
                                </div>
                            </div>
                            <hr class="m-0 my-2">
                            <div class="mb-1 row">
                                <label class="col-sm-5 col-form-label">Plebitis</label>
                                <div class="col-sm-7">
                                    <select type="text" name="plebitis" class="form-control">
                                        <option value="">Pilih</option>
                                        @php
                                            $yesOrNo = ['YA', 'TIDAK'];
                                        @endphp
                                        @foreach ($yesOrNo as $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="col-sm-5 col-form-label">ISK</label>
                                <div class="col-sm-7">
                                    <select type="text" name="isk" class="form-control">
                                        <option value="">Pilih</option>
                                        @foreach ($yesOrNo as $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="col-sm-5 col-form-label">IDO</label>
                                <div class="col-sm-7">
                                    <select type="text" name="ido" class="form-control">
                                        @php
                                            array_push($yesOrNo, 'NON OPERASI');
                                        @endphp
                                        <option value="">Pilih</option>
                                        @foreach ($yesOrNo as $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="verified" class="form-check form-check-inline">
                                <input type="checkbox" name="verified" id="verified" class="form-check-input"
                                    value="1">
                                <span class="form-check-label">Verifikasi</span>
                            </label>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-1 row justify-content-end">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('javascript')
    <script>
        $('#InsidenUpdateForm').on('submit', function(e) {
            e.preventDefault();

            let data = $(this).serialize()
            let urlAttr = $(this).attr('data-url')
            let typeAttr = "PUT"

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                type: typeAttr,
                url: urlAttr,
                data: data,
                dataType: "JSON"
            }).done((response) => {
                toastrSuccess('Berhasil diperbarui')
                myModal.hide()

                table
                    .row(this)
                    .remove()
                    .draw(false);

            }).fail((errors) => {
                toastrError(errors.responseText)
            });

        });
        myModal.addEventListener('hidden.bs.modal', (event) => {
            resetForm();
        })

        function resetForm() {
            $('#InsidenUpdateForm')[0].reset()
        }
    </script>
@endpush
