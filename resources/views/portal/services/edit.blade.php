@extends('layouts.portal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Edit Layanan Katering</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('portal.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="fw-bold">Nama Layanan</label>
                        <input type="text" name="title" class="form-control" value="{{ $service->title }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Ganti Foto (Opsional)</label>
                        <input type="file" name="image" class="form-control">
                        @if($service->image)
                            <div class="mt-2 text-center p-2 border rounded bg-light">
                                <img src="{{ asset('storage/'.$service->image) }}" height="100" class="rounded shadow-sm">
                                <p class="small text-muted mb-0 mt-1">Gambar Saat Ini</p>
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Deskripsi Layanan</label>
                        <textarea name="description" class="form-control" rows="4">{{ $service->description }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch border p-3 rounded bg-light">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ $service->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label fw-bold" for="is_active">Aktifkan Layanan</label>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 text-end">
                        <a href="{{ route('portal.services.index') }}" class="btn btn-light px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-info text-white px-5 shadow">Perbarui Layanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
