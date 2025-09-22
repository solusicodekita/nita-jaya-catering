@extends('layouts.adm.base')
@section('title', 'Bahan')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Form Import Bahan</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('items.index') }}" class="btn btn-outline-primary"><i
                                            class="fas fa-arrow-left"></i> Kembali</a>
                                    <a href="{{ asset('template/Template Barang.xlsx') }}" class="btn btn-outline-success"
                                        download><i class="fas fa-download"></i> Template Import</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="card border-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">Perhatian!</h5>
                                    <ol class="mb-0" style="font-weight: bold;">
                                        <li> Pastikan Menggunakan Template Yang Sudah Disediakan</li>
                                        <li> Nama Kategori Harus Sesuai Dengan Data Yang Sudah Ada Pada Fitur Master -> Kategori</li>
                                        <li> Tidak Boleh Kosong Pada Kolom Nama Kategori, Nama Barang, Unit, Harga</li>
                                        <li> Untuk Pengisian Harga Tidak Perlu Menggunakan Titik (.) atau Koma (,) Contoh : 100000</li>
                                        <li> Pastikan Data Sudah Sesuai Sebelum Melakukan Proses Import</li>
                                        <li> Dilarang Merubah Format Yang Sudah Disediakan</li>
                                    </ol>
                                </div>
                            </div>
                            <form id="formItemImport" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="file">File</label>
                                    <input type="file" class="form-control" name="file" id="file"
                                        placeholder="Ketikkan File" autocomplete="off" accept=".xlsx,.xls">
                                </div>
                                <hr>
                                <button class="btn btn-outline-success" onclick="importData(event)">
                                    <i class="fa fa-save"></i> Import
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
       function importData(event) {
        event.preventDefault();
        let myForm = document.getElementById('formItemImport');
        let formData = new FormData(myForm);
        let fileInput = document.getElementById('file');

        if (!fileInput.files.length) {
            Swal.fire({
                text: "File wajib diisi",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
            return;
        }

        Swal.fire({
            title: 'Sedang diproses',
            html: 'Mohon ditunggu sampai selesai',
            allowOutsideClick: false,
            allowEscapeKey: false,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading()
            },
        })

        $.ajax({
            url: "{{ route('items.importData') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status == 200) {
                    Swal.fire({
                        text: "Data sukses diimport",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Selesai",
                        customClass: {
                            confirmButton: "btn btn-success"
                        }
                    }).then(function(result) {
                        location.href = "{{ route('items.index') }}";
                    });
                } else if (data.status == 400) {
                    Swal.fire({
                        text: data.msg,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Selesai",
                        customClass: {
                            confirmButton: "btn btn-success"
                        }
                    })
                }
            },
            error: function(request, status, error) {
                console.log(request.responseText);
                if (request.status == 500) {
                    let response = JSON.parse(request.responseText);
                    let errorMessages = response.message.split('\n');
                    let messages = '';
                    errorMessages.forEach(function(msg) {
                        messages += '&bull; ' + msg + '<br>';
                    });
    
                    Swal.fire({
                        title: "Error",
                        html: messages,
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            }
        });
    }
    </script>
@endpush
