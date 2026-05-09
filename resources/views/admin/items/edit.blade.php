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
                                    <h3 class="card-title">Form Edit Bahan</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-primary"><i
                                             class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="formItem" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="category_id">Kategori</label>
                                    <select class="form-control" name="category_id" id="category_id">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategori as $value)
                                            <option value="{{ $value->id }}" {{ $data->category_id == $value->id ? 'selected' : '' }}>{{ $value->code }} - {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="name">Nama Bahan</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Ketikkan Nama Bahan" autocomplete="off" value="{{ $data->name }}">
                                    <input type="hidden" class="form-control" name="id" id="id"
                                        placeholder="Ketikkan Nama Bahan" autocomplete="off" value="{{ $data->id }}">
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="unit">Satuan Besar (Unit)</label>
                                    <input type="text" class="form-control" name="unit" id="unit"
                                        placeholder="Contoh: Ball, Box, Kg" autocomplete="off" value="{{ $data->unit }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 10px;">
                                            <label for="retail_unit">Satuan Kecil (Retail)</label>
                                            <input type="text" class="form-control" name="retail_unit" id="retail_unit"
                                                placeholder="Contoh: Gr, Pcs" autocomplete="off" value="{{ $data->retail_unit }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 10px;">
                                            <label for="retail_conversion">Isi per Satuan Besar</label>
                                            <input type="number" step="0.0001" class="form-control" name="retail_conversion" id="retail_conversion"
                                                placeholder="Contoh: 1000" autocomplete="off" value="{{ $data->retail_conversion }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="price">Harga</label>
                                    <input type="text" class="form-control" name="price" id="price"
                                        placeholder="Ketikkan Harga" autocomplete="off" value="{{ $data->price }}">
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-outline-success" onclick="update(event)">
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
        });
    </script>
    @include('admin.items.script')
@endpush
