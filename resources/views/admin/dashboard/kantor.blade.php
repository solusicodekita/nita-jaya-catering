@extends('layouts.adm.base')
@section('title', 'Dashboard Admin Kantor')

@section('content')
<style>
    .dashboard-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: #fff;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    .bg-gradient-primary-custom {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #fff;
    }
    .bg-gradient-success-custom {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: #fff;
    }
    .bg-gradient-warning-custom {
        background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
        color: #fff;
    }
    .bg-gradient-info-custom {
        background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
        color: #fff;
    }
    .icon-bg {
        position: absolute;
        right: -15px;
        bottom: -15px;
        font-size: 5rem;
        opacity: 0.2;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary m-0">Dashboard Admin Kantor</h2>
            <p class="text-muted m-0">Selamat datang kembali, <strong>{{ auth()->user()->fullname }}</strong></p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.pesanan.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="fas fa-plus me-2"></i> Buat Pesanan Baru
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-primary-custom">
                <i class="fas fa-shopping-bag icon-bg"></i>
                <div class="fw-bold text-uppercase small opacity-75">Total Pesanan</div>
                <div class="fs-2 fw-bold my-2">{{ number_format($total_pesanan) }}</div>
                <div class="small">Transaksi terdaftar</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-success-custom">
                <i class="fas fa-money-bill-wave icon-bg"></i>
                <div class="fw-bold text-uppercase small opacity-75">Total Omset (Jual)</div>
                <div class="fs-3 fw-bold my-2">Rp {{ number_format($total_omset, 0, ',', '.') }}</div>
                <div class="small">Nilai Penjualan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-warning-custom">
                <i class="fas fa-calculator icon-bg"></i>
                <div class="fw-bold text-uppercase small opacity-75">Total Estimasi HPP</div>
                <div class="fs-3 fw-bold my-2">Rp {{ number_format($total_hpp, 0, ',', '.') }}</div>
                <div class="small">Biaya Bahan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-info-custom">
                <i class="fas fa-chart-line icon-bg"></i>
                <div class="fw-bold text-uppercase small opacity-75">Estimasi Profit</div>
                <div class="fs-3 fw-bold my-2">Rp {{ number_format($total_profit, 0, ',', '.') }}</div>
                <div class="small">Estimasi Margin Keuntungan</div>
            </div>
        </div>
    </div>

    <!-- Recent Pesanans Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0"><i class="fas fa-list text-primary me-2"></i> Transaksi Pesanan Terbaru</h5>
                    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>No. Order</th>
                                    <th>Nama Customer</th>
                                    <th>Tgl Acara</th>
                                    <th>Estimasi HPP</th>
                                    <th>Estimasi Jual</th>
                                    <th>Dibuat Oleh</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_pesanans as $index => $pesanan)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><span class="badge bg-info text-dark">{{ $pesanan->order_number }}</span></td>
                                    <td class="fw-bold">{{ $pesanan->customer_name ?? '-' }}</td>
                                    <td>{{ $pesanan->event_date ? date('d M Y', strtotime($pesanan->event_date)) : '-' }}</td>
                                    <td class="text-danger">Rp {{ number_format($pesanan->total_cost, 0, ',', '.') }}</td>
                                    <td class="text-success fw-bold">Rp {{ number_format($pesanan->grand_total, 0, ',', '.') }}</td>
                                    <td>{{ $pesanan->createdBy->fullname ?? 'Sistem' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-sm btn-info rounded-circle me-1" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pesanan.cetak', $pesanan->id) }}" target="_blank" class="btn btn-sm btn-success rounded-circle me-1" title="Cetak Bukti Pesanan">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}" class="btn btn-sm btn-warning rounded-circle" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada data pesanan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
