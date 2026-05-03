@extends('layouts.portal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Layanan Katering Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('portal.services.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="fw-bold">Nama Layanan</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Prasmanan Pernikahan, Nasi Kotak Karyawan" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Foto Icon / Ilustrasi Layanan</label>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Gunakan gambar yang mewakili jenis layanan ini.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Deskripsi Layanan</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan secara singkat apa saja yang didapat pelanggan pada layanan ini..."></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch border p-3 rounded bg-light">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                            <label class="custom-control-label fw-bold" for="is_active">Aktifkan Layanan (Tampil di Landing Page)</label>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('portal.services.index') }}" class="btn btn-light px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 shadow">Simpan Layanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
