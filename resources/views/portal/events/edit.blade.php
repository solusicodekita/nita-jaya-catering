@extends('layouts.portal')

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Perbarui Dokumentasi Event</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('portal.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="fw-bold">Judul Acara / Nama Klien</label>
                        <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Tanggal Acara</label>
                                <input type="date" name="event_date" class="form-control" value="{{ $event->event_date }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Ganti Foto Utama (Cover)</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold text-success"><i class="fas fa-plus-circle me-1"></i> Tambah Foto Galeri Baru</label>
                        <input type="file" name="gallery_images[]" class="form-control" multiple>
                        <small class="text-muted">Pilih beberapa foto sekaligus untuk menambah galeri.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-bold">Cerita Singkat / Detail Menu</label>
                        <textarea name="description" class="form-control" rows="8">{{ $event->description }}</textarea>
                    </div>
                    
                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('portal.events.index') }}" class="btn btn-light px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-info text-white px-5 shadow">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold">Foto Utama Saat Ini</h6>
            </div>
            <div class="card-body text-center p-2">
                <img src="{{ asset('storage/' . $event->image) }}" class="img-fluid rounded shadow-sm" style="max-height: 200px">
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Galeri Dokumentasi ({{ $gallery->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @forelse($gallery as $img)
                    <div class="col-4">
                        <div class="position-relative border rounded p-1">
                            <img src="{{ asset('storage/' . $img->image) }}" class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover;">
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted py-4 small">Belum ada foto galeri tambahan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
