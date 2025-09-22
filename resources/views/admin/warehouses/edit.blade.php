@extends('layouts.adm.base')
@section('title', 'Lokasi')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Form Edit Lokasi</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline-primary"><i
                                            class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="formLokasi" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="name">Nama Lokasi</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Ketikkan Nama Lokasi" autocomplete="off" value="{{ $lokasi->name }}">
                                    <small id="nameHelp" class="form-text text-danger" style="display: none;">
                                        Nama lokasi harus minimal 2 kata
                                    </small>
                                    <input type="text" class="form-control" name="id" id="id" hidden
                                        autocomplete="off" value="{{ $lokasi->id }}">
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="code">Kode Lokasi</label>
                                    <input type="text" class="form-control" name="code" id="code" maxlength="2"
                                        placeholder="Kode akan terisi otomatis" autocomplete="off" readonly value="{{ $lokasi->code }}">
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-outline-success" id="btnSimpan" onclick="update(event)">
                                    <i class="fa fa-save"></i> Simpan
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
        document.getElementById('name').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            const words = this.value.trim().split(/\s+/);
            if (words.length >= 2) {
                const code = words[0][0] + words[1][0];
                document.getElementById('code').value = code;
                document.getElementById('nameHelp').style.display = 'none';
                document.getElementById('btnSimpan').disabled = false;
            } else {
                document.getElementById('code').value = '';
                document.getElementById('nameHelp').style.display = 'block';
                document.getElementById('btnSimpan').disabled = true;
            }
        });
    </script>
    @include('admin.warehouses.script')
@endpush
