@extends('layouts.adm.base')
@section('title', 'Notifikasi Sistem')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="m-0 fw-bold text-primary"><i class="fas fa-bell me-2"></i> Notifikasi Sistem</h2>
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary rounded-pill px-4 btn-sm">
                    <i class="fas fa-check-double me-1"></i> Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notif)
                    <a href="{{ route('admin.notifications.read', $notif->id) }}" 
                       class="list-group-item list-group-item-action py-3 px-3 border-bottom rounded mb-2 d-flex justify-content-between align-items-center {{ $notif->is_read ? 'bg-light text-muted' : 'bg-white shadow-sm border-start border-4 border-primary' }}">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="fw-bold fs-6 {{ $notif->is_read ? 'text-secondary' : 'text-dark' }} me-2">
                                    {{ $notif->title }}
                                </span>
                                @if(!$notif->is_read)
                                    <span class="badge bg-danger rounded-pill">Baru</span>
                                @endif
                            </div>
                            <p class="mb-1 text-secondary small">{{ $notif->message }}</p>
                            <small class="text-muted"><i class="far fa-clock me-1"></i> {{ $notif->created_at ? $notif->created_at->diffForHumans() : '-' }}</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-bell-slash fs-1 d-block mb-3 opacity-50"></i>
                    <h5 class="fw-bold">Belum Ada Notifikasi</h5>
                    <p class="small">Semua notifikasi transaksi dan aktivitas sistem akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
