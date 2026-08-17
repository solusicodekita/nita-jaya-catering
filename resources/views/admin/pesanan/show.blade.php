@extends('layouts.adm.base')

@push('style')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .printable-area, .printable-area * {
            visibility: visible;
        }
        .printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('title', 'Detail Pesanan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="me-3 no-print">
                    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-secondary rounded-circle">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div>
                    <h2 class="m-0 fw-bold text-primary">Detail Pesanan: {{ $pesanan->order_number }}</h2>
                </div>
            </div>
            <div class="no-print">
                <a href="{{ route('admin.pesanan.cetak', $pesanan->id) }}" target="_blank" class="btn btn-primary rounded-pill px-4 me-2">
                    <i class="fas fa-print me-2"></i> Cetak Bukti Pesanan
                </a>
                @if(!auth()->user()->hasRole('admin-dapur'))
                <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}" class="btn btn-warning rounded-pill px-4 me-2">
                    <i class="fas fa-edit me-2"></i> Edit Pesanan
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row printable-area">
        <!-- Informasi Klien -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-user-circle text-info me-2"></i> Info Klien</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Nomor Order</td>
                            <td class="fw-bold">: <span class="badge bg-primary">{{ $pesanan->order_number }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Klien</td>
                            <td class="fw-bold">: {{ $pesanan->customer_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Acara</td>
                            <td class="fw-bold">: {{ date('d M Y', strtotime($pesanan->event_date)) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Pesanan</td>
                            <td class="fw-bold">: 
                                @if(($pesanan->status ?? 'PESANAN') == 'DICEK_DAPUR')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Checked Dapur</span>
                                @elseif(($pesanan->status ?? 'PESANAN') == 'DIPROSES_GUDANG')
                                    <span class="badge bg-primary"><i class="fas fa-boxes me-1"></i> Diproses Gudang</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pesanan Baru</span>
                                @endif
                            </td>
                        </tr>
                        @if($pesanan->verifiedBy)
                        <tr>
                            <td class="text-muted">Diverifikasi Dapur</td>
                            <td class="fw-bold">: {{ $pesanan->verifiedBy->fullname ?? $pesanan->verifiedBy->username }} ({{ date('d/m/Y H:i', strtotime($pesanan->verified_at)) }})</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Dibuat Pada</td>
                            <td class="fw-bold">: {{ $pesanan->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat Oleh</td>
                            <td class="fw-bold">: {{ $pesanan->createdBy->fullname ?? 'Sistem' }}</td>
                        </tr>
                    </table>

                    <hr>
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total HPP:</span>
                            <span class="fw-bold text-danger">Rp {{ number_format($pesanan->total_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Jual:</span>
                            <span class="fw-bold text-success fs-5">Rp {{ number_format($pesanan->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span class="text-muted fw-bold">Estimasi Profit:</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($pesanan->grand_total - $pesanan->total_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Resep -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-list-alt text-success me-2"></i> Daftar Resep Terpesan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Kode / Nama Resep</th>
                                    <th class="text-center">Porsi</th>
                                    <th class="text-end">Subtotal HPP</th>
                                    <th class="text-end">Subtotal Jual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->details as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $detail->menu->name ?? 'Resep Terhapus' }}</div>
                                        <div class="small text-muted">{{ $detail->menu->recipe_number ?? '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary px-3 py-2 fs-6">{{ $detail->qty_porsi }}</span>
                                    </td>
                                    <td class="text-end text-danger fw-bold">Rp {{ number_format($detail->subtotal_cost, 0, ',', '.') }}</td>
                                    <td class="text-end text-success fw-bold">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Ketersediaan Bahan Baku untuk Dapur -->
                    @if(isset($ingredientCheck) && count($ingredientCheck) > 0)
                    <div class="mt-4 border-top pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-primary m-0"><i class="fas fa-boxes me-2"></i> Ketersediaan Bahan Baku (Cek Stok Dapur)</h6>
                            @if(auth()->user()->hasRole('admin-dapur') || auth()->user()->hasRole('admin'))
                                @if(($pesanan->status ?? 'PESANAN') == 'PESANAN')
                                <form action="{{ route('admin.pesanan.verifikasi-dapur', $pesanan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold btn-sm shadow-sm">
                                        <i class="fas fa-check-double me-2"></i> Verifikasi Ketersediaan (ACC Dapur)
                                    </button>
                                </form>
                                @else
                                <span class="badge bg-success px-3 py-2 fs-6"><i class="fas fa-check-circle me-1"></i> Terverifikasi Dapur</span>
                                @endif
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%" class="text-center">No</th>
                                        <th>Nama Bahan Baku</th>
                                        <th class="text-center">Kebutuhan Pesanan</th>
                                        <th class="text-center">Stok Gudang Utama</th>
                                        <th class="text-center">Stok Gudang Dapur</th>
                                        <th class="text-center">Status Dapur</th>
                                        @if(auth()->user()->hasRole('admin-gudang-utama') || auth()->user()->hasRole('admin'))
                                        <th class="text-center">Aksi Gudang Utama</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $idxCheck = 1; @endphp
                                    @foreach($ingredientCheck as $itemId => $dataCheck)
                                    @php 
                                        $needed = $dataCheck['total_needed'];
                                        $stockDapur = $dataCheck['live_stock_dapur'] ?? $dataCheck['live_stock'];
                                        $stockUtama = $dataCheck['live_stock_utama'] ?? 0;
                                        $isSufficient = $stockDapur >= $needed;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $idxCheck++ }}</td>
                                        <td class="fw-bold">{{ $dataCheck['item']->name ?? 'Item Terhapus' }}</td>
                                        <td class="text-center font-monospace fw-bold">{{ number_format($needed, 2) }} {{ $dataCheck['item']->unit ?? '' }}</td>
                                        <td class="text-center font-monospace {{ $stockUtama > 0 ? 'text-primary fw-bold' : 'text-muted' }}">
                                            {{ number_format($stockUtama, 2) }} {{ $dataCheck['item']->unit ?? '' }}
                                        </td>
                                        <td class="text-center font-monospace {{ $stockDapur >= $needed ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                            {{ number_format($stockDapur, 2) }} {{ $dataCheck['item']->unit ?? '' }}
                                        </td>
                                        <td class="text-center">
                                            @if($isSufficient)
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Stok Cukup</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i> Kurang {{ number_format($needed - $stockDapur, 2) }}</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->hasRole('admin-gudang-utama') || auth()->user()->hasRole('admin'))
                                        <td class="text-center">
                                            @if($stockUtama >= $needed)
                                                <!-- Stok Gudang Utama Sudah Cukup: Rekomendasi Utama TRANSFER KE DAPUR -->
                                                <a href="{{ route('admin.mutasi_stok.create', ['item_id' => $itemId]) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-0 me-1 fw-bold shadow-sm" title="Stok Gudang Utama tersedia ({{ number_format($stockUtama, 2) }} {{ $dataCheck['item']->unit ?? '' }}). Klik untuk Transfer ke Gudang Dapur">
                                                    <i class="fas fa-exchange-alt me-1"></i> Transfer ke Dapur
                                                </a>
                                                <a href="{{ route('admin.in_stock.create', ['item_id' => $itemId]) }}" class="btn btn-sm btn-link text-muted px-1 py-0 opacity-75" title="Stok Gudang Utama sudah cukup. Hanya gunakan ini jika ingin membeli stok supplier baru">
                                                    <small><i class="fas fa-plus-circle me-1"></i> Stock In</small>
                                                </a>
                                            @elseif($stockUtama > 0)
                                                <!-- Stok Gudang Utama Kurang Sebagian -->
                                                <a href="{{ route('admin.mutasi_stok.create', ['item_id' => $itemId]) }}" class="btn btn-sm btn-primary rounded-pill px-2 py-0 me-1" title="Transfer sisa stok yang ada di Gudang Utama ke Dapur">
                                                    <i class="fas fa-exchange-alt me-1"></i> Transfer
                                                </a>
                                                <a href="{{ route('admin.in_stock.create', ['item_id' => $itemId]) }}" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0" title="Beli/Tambah Stok dari Supplier">
                                                    <i class="fas fa-plus me-1"></i> Stock In
                                                </a>
                                            @else
                                                <!-- Stok Gudang Utama Kosong: Rekomendasi Utama STOCK IN SUPPLIER -->
                                                <a href="{{ route('admin.in_stock.create', ['item_id' => $itemId]) }}" class="btn btn-sm btn-success rounded-pill px-2 py-0 me-1 fw-bold shadow-sm" title="Stok Gudang Utama Kosong. Beli/Terima barang dari Supplier">
                                                    <i class="fas fa-plus me-1"></i> + Stock In
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0 disabled" title="Stok Gudang Utama Kosong (Lakukan Stock In terlebih dahulu)" disabled>
                                                    <i class="fas fa-exchange-alt me-1"></i> Transfer
                                                </button>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if($pesanan->stockTransaction)
                        <div class="alert alert-info mt-3 d-flex align-items-center">
                            <i class="fas fa-info-circle fs-3 me-3"></i>
                            <div>
                                <strong>Stok Berhasil Dipotong!</strong><br>
                                Sistem telah mencatat pengurangan stok otomatis dari pesanan ini pada tanggal {{ $pesanan->stockTransaction->date }}.
                            </div>
                        </div>

                        <!-- Daftar Bahan Baku / Stok Keluar -->
                        <div class="mt-4">
                            <h6 class="fw-bold"><i class="fas fa-box-open text-warning me-2"></i> Rincian Bahan Baku yang Digunakan</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered align-middle mt-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>Nama Bahan</th>
                                            <th class="text-center">Kuantitas Dipotong</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pesanan->stockTransaction->stockTransactionDetails as $idx => $stDetail)
                                        <tr>
                                            <td class="text-center">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $stDetail->item->name ?? 'Item Terhapus' }}</div>
                                                <small class="text-muted">{{ $stDetail->item->item_code ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger px-2 py-1">{{ $stDetail->quantity }} {{ $stDetail->item->unit ?? '' }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
