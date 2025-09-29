@extends('layouts.adm.base')

@section('title', 'Detail Resep - ' . $menu->name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.resep') }}" class="text-decoration-none">
                                    <i class="fa-solid fa-utensils me-1"></i>Dashboard Resep
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Resep</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0 text-gray-800 fw-bold">
                        <i class="fa-solid fa-eye text-primary me-2"></i>
                        {{ $menu->name }}
                    </h1>
                    <p class="text-muted mb-0">Detail informasi dan riwayat transaksi resep</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.resep') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                    <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-warning">
                        <i class="fa-solid fa-edit me-1"></i>
                        Edit Resep
                    </a>
                    <a href="{{ route('admin.out_stock.create') }}?menu_id={{ $menu->id }}" class="btn btn-success">
                        <i class="fa-solid fa-plus me-1"></i>
                        Buat Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Resep Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Informasi Resep
                    </h6>
                </div>
                <div class="card-body">
                    @if($menu->image)
                        <div class="text-center mb-4">
                            <img src="{{ asset('storage/' . $menu->image) }}" class="img-fluid rounded" alt="{{ $menu->name }}" style="max-height: 200px;">
                        </div>
                    @else
                        <div class="text-center mb-4">
                            <div class="bg-gradient-primary rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fa-solid fa-utensils fa-4x text-white"></i>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h5 class="text-primary fw-bold">{{ $menu->name }}</h5>
                        @if($menu->description)
                            <p class="text-muted">{{ $menu->description }}</p>
                        @endif
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <div class="fw-bold text-primary">{{ $menu->stock ?? 0 }}</div>
                                <small class="text-muted">Stok Tersedia</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <div class="fw-bold text-success">{{ $transaksiMenus->count() }}</div>
                                <small class="text-muted">Total Transaksi</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-tag text-muted me-2"></i>
                            <span class="badge bg-primary">{{ $menu->category ?? 'Tidak ada kategori' }}</span>
                        </div>
                    </div>

                    @if($menu->price)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga</label>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-dollar-sign text-muted me-2"></i>
                            <span class="fw-bold text-success">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div class="d-flex align-items-center">
                            @if($menu->status == 'active')
                                <i class="fa-solid fa-check-circle text-success me-2"></i>
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <i class="fa-solid fa-pause-circle text-secondary me-2"></i>
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <small class="text-muted">Dibuat oleh</small>
                        <div class="fw-bold">{{ $menu->createdBy->fullname ?? 'Unknown' }}</div>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Tanggal dibuat</small>
                        <div class="fw-bold">{{ $menu->created_at ? \Carbon\Carbon::parse($menu->created_at)->format('d F Y, H:i') : 'Tidak diketahui' }}</div>
                    </div>

                    @if($menu->updated_at)
                    <div class="mb-2">
                        <small class="text-muted">Terakhir diupdate</small>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($menu->updated_at)->format('d F Y, H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaksi History -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-history me-2"></i>
                        Riwayat Transaksi
                    </h6>
                </div>
                <div class="card-body">
                    @if($transaksiMenus->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaksiMenus as $index => $transaksi)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <i class="fa-solid fa-calendar text-muted me-1"></i>
                                            {{ $transaksi->created_at ? \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $transaksi->qty }}</span>
                                        </td>
                                        <td>
                                            {{ $transaksi->keterangan ?? '-' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fa-solid fa-check me-1"></i>
                                                Selesai
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada transaksi</h5>
                            <p class="text-muted">Resep ini belum pernah digunakan dalam transaksi</p>
                            <a href="{{ route('admin.out_stock.create') }}?menu_id={{ $menu->id }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-1"></i>
                                Buat Transaksi Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-bolt me-2"></i>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.out_stock.create') }}?menu_id={{ $menu->id }}" class="btn btn-success w-100">
                                <i class="fa-solid fa-plus me-2"></i>
                                Buat Transaksi Baru
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-warning w-100">
                                <i class="fa-solid fa-edit me-2"></i>
                                Edit Resep
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.live_stock.index') }}" class="btn btn-info w-100">
                                <i class="fa-solid fa-chart-line me-2"></i>
                                Lihat Live Stock
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.out_stock.index') }}" class="btn btn-primary w-100">
                                <i class="fa-solid fa-list me-2"></i>
                                Lihat Semua Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card {
        border: none;
        border-radius: 10px;
    }
    .table th {
        border-top: none;
        font-weight: 600;
    }
    .breadcrumb {
        background: none;
        padding: 0;
    }
    .breadcrumb-item a {
        color: #6c757d;
    }
    .breadcrumb-item.active {
        color: #495057;
    }
</style>
@endpush
@endsection
