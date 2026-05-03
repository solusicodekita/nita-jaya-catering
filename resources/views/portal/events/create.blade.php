@extends('layouts.portal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Abadikan Momen Event Katering</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('portal.events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="fw-bold">Judul Acara / Nama Klien</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Wedding @ Gedung Serbaguna Ketintang" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Tanggal Acara</label>
                                <input type="date" name="event_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Foto Utama (Cover)</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold"><i class="fas fa-images me-1"></i> Galeri Foto Tambahan (Bisa pilih banyak)</label>
                        <input type="file" name="gallery_images[]" class="form-control" multiple>
                        <small class="text-muted">Pilih beberapa foto keseruan acara sekaligus.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-bold">Cerita Singkat / Detail Menu</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Ceritakan kesuksesan acara ini atau menu apa saja yang disajikan..."></textarea>
                    </div>
                    
                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('portal.events.index') }}" class="btn btn-light px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 shadow">Simpan Dokumentasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
