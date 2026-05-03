@extends('layouts.portal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Perbarui Detail Paket Katalog</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('portal.menus.update', $resep->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="fw-bold text-info">Nama Menu / Paket</label>
                                <input type="text" name="name" class="form-control form-control-lg" value="{{ $resep->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Kategori</label>
                                <select name="category" id="menu_category" class="form-control form-control-lg select2-tags">
                                    @php
                                        $defaultCategories = ['Paket Prasmanan', 'Nasi Kotak Premium', 'Tumpeng Hias', 'Snack Box & Coffee Break', 'Pondokan / Stand Wedding'];
                                        $allCategories = collect($defaultCategories)->merge($categories)->push($resep->category)->unique();
                                    @endphp
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat }}" {{ $resep->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted small">Ketik kategori baru lalu tekan Enter jika tidak ada dalam daftar.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Harga Start From</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="price" class="form-control" value="{{ $resep->price }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold text-danger">Ganti Foto (Opsional)</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Deskripsi Pemasaran (Singkat)</label>
                        <textarea name="description" class="form-control" rows="2">{{ $resep->description }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-bold"><i class="fas fa-list-ul me-1"></i> Daftar Menu Detail (Satu baris satu menu)</label>
                        @php 
                            $selectedItems = explode("\n", str_replace("\r", "", $resep->items));
                            $selectedItems = array_map('trim', $selectedItems);
                            $selectedItems = array_filter($selectedItems);
                        @endphp
                        <select name="items[]" id="menu_items" class="form-control select2" multiple="multiple">
                            @foreach($posItems as $item)
                                <option value="{{ $item->name }}" {{ in_array($item->name, $selectedItems) ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                            {{-- Add selected items that are NOT in posItems --}}
                            @foreach($selectedItems as $sItem)
                                @if(!$posItems->contains('name', $sItem))
                                    <option value="{{ $sItem }}" selected>{{ $sItem }}</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-info italic"><i class="fas fa-info-circle me-1"></i> Anda bisa memilih dari item POS atau mengetik menu baru lalu tekan Enter.</small>
                    </div>

                    @if($resep->image)
                    <div class="mb-4 text-center p-3 border rounded bg-light">
                        <img src="{{ asset('storage/' . $resep->image) }}" height="180" class="rounded shadow-sm">
                        <p class="small text-muted mt-2 mb-0">Foto Paket Saat Ini</p>
                    </div>
                    @endif

                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch border p-3 rounded bg-light">
                            <input type="checkbox" class="custom-control-input" id="is_promoted" name="is_promoted" {{ $resep->is_promoted ? 'checked' : '' }}>
                            <label class="custom-control-label fw-bold" for="is_promoted">Rekomendasikan Ke Halaman Depan</label>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('portal.menus.index') }}" class="btn btn-light btn-lg px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-info text-white btn-lg px-5 shadow">Perbarui Paket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#menu_items').select2({
            tags: true,
            tokenSeparators: [','],
            placeholder: "Pilih atau ketik menu detail...",
            width: '100%'
        });

        $('#menu_category').select2({
            tags: true,
            placeholder: "-- Pilih atau Ketik Kategori --",
            width: '100%'
        });
    });
</script>
@endpush
