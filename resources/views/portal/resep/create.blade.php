@extends('layouts.portal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Produk Katalog / Paket Menu</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('portal.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="fw-bold text-primary">Nama Menu / Paket</label>
                                <input type="text" name="name" class="form-control form-control-lg" placeholder="Contoh: Paket Prasmanan Nusantara A" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Kategori Menu</label>
                                <select name="category" id="menu_category" class="form-control form-control-lg select2-tags">
                                    <option value="">-- Pilih atau Ketik Kategori --</option>
                                    @php
                                        $defaultCategories = ['Paket Prasmanan', 'Nasi Kotak Premium', 'Tumpeng Hias', 'Snack Box & Coffee Break', 'Pondokan / Stand Wedding'];
                                        $allCategories = collect($defaultCategories)->merge($categories)->unique();
                                    @endphp
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted small">Ketik kategori baru lalu tekan Enter jika tidak ada dalam daftar.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Harga Start From (Opsional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="price" class="form-control" placeholder="Contoh: 35.000 / pax">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold text-danger"><i class="fas fa-image me-1"></i> Foto Masakan / Header Paket</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Deskripsi Pemasaran (Singkat)</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Jelaskan keistimewaan paket ini dalam 1-2 kalimat..."></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-bold"><i class="fas fa-list-ul me-1"></i> Daftar Menu Detail (Satu baris satu menu)</label>
                        <select name="items[]" id="menu_items" class="form-control select2" multiple="multiple">
                            @foreach($posItems as $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-info italic"><i class="fas fa-info-circle me-1"></i> Anda bisa memilih dari item POS atau mengetik menu baru lalu tekan Enter.</small>
                    </div>

                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch border p-3 rounded bg-light">
                            <input type="checkbox" class="custom-control-input" id="is_promoted" name="is_promoted" checked>
                            <label class="custom-control-label fw-bold" for="is_promoted">Rekomendasikan Ke Halaman Depan (Sajian Istimewa)</label>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('portal.menus.index') }}" class="btn btn-light btn-lg px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow">Simpan Paket Menu</button>
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
