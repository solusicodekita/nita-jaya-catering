@extends('layouts.adm.base')
@section('title', 'Menu')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Form Edit Menu</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-primary"><i
                                            class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="formMenu" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="name">Nama Menu</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Ketikkan Nama Kategori" autocomplete="off"
                                        value="{{ $menu->name }}">
                                    <small id="nameHelp" class="form-text text-danger" style="display: none;">
                                        Nama kategori harus minimal 2 kata
                                    </small>
                                    <input type="text" class="form-control" name="id" id="id" hidden
                                        autocomplete="off" value="{{ $menu->id }}">
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="is_active">Status</label>
                                    <select class="form-control" name="is_active" id="is_active">
                                        <option value="1" {{ $menu->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $menu->is_active == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
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
        });
    </script>
    @include('admin.menu.script')
@endpush
