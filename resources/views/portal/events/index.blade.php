@extends('layouts.portal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-camera-retro me-2"></i> Galeri Acara / Event</h5>
                <a href="{{ route('portal.events.create') }}" class="btn btn-sm btn-light">Tambah Event Baru</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row">
                    @forelse($events as $event)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border overflow-hidden">
                            <div style="height: 180px; overflow: hidden;">
                                @if($event->image)
                                    <img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" style="object-fit: cover; height: 100%;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center h-100 text-muted">No Image</div>
                                @endif
                            </div>
                            <div class="card-body">
                                <span class="badge badge-info mb-2">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</span>
                                <h6 class="fw-bold mb-1">{{ $event->title }}</h6>
                                <p class="small text-muted mb-0">{{ Str::limit($event->description, 60) }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                                <a href="{{ route('portal.events.edit', $event->id) }}" class="btn btn-sm btn-outline-info me-2"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('portal.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus dokumentasi event ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" height="150" class="mb-3 opacity-50">
                        <p class="text-muted">Belum ada dokumentasi event. Mulai abadikan momen katering Anda!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
