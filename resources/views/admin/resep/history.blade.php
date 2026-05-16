@extends('layouts.adm.base')

@section('title', 'Riwayat Penggunaan Resep')

@push('styles')
<style>
    .history-card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .badge-qty { background-color: #e0e7ff; color: #3730a3; font-weight: 700; padding: 0.4em 0.8em; border-radius: 50px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1"><i class="fa-solid fa-history me-2"></i>Riwayat Penggunaan Resep</h2>
                <p class="text-muted mb-0">Daftar resep yang telah digunakan dan status pemotongan stoknya.</p>
            </div>
            <a href="{{ route('admin.resep.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Resep
            </a>
        </div>
    </div>

    <div class="card history-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Waktu Penggunaan</th>
                            <th>Nama Resep</th>
                            <th class="text-center">Jumlah Porsi</th>
                            <th>Status Stok</th>
                            <th>Operator</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                        <tr>
                            <td class="px-4">
                                <div class="fw-bold">{{ $history->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $history->created_at->format('H:i:s') }}</div>
                            </td>
                             <td>
                                <div class="fw-bold text-uppercase">{{ $history->recipe_name ?? ($history->menu->name ?? 'Resep Terhapus') }}</div>
                                <div class="text-muted small mb-2">{{ $history->recipe_number ?? ($history->menu->recipe_number ?? '-') }}</div>
                                
                                @if($history->stockTransaction && $history->stockTransaction->stockTransactionDetails->count() > 0)
                                    <div class="mt-2">
                                        <button class="btn btn-xs btn-outline-secondary py-0 px-2 small text-decoration-none" style="font-size: 0.7rem;" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $history->id }}">
                                            <i class="fa-solid fa-list me-1"></i> Detail Pemakaian Bahan
                                        </button>
                                        <div class="collapse mt-2" id="details-{{ $history->id }}">
                                            <div class="bg-light p-2 rounded-3 border">
                                                <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                                                    <thead class="text-muted border-bottom">
                                                        <tr>
                                                            <th>Bahan Baku</th>
                                                            <th class="text-end">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($history->stockTransaction->stockTransactionDetails as $detail)
                                                            <tr>
                                                                <td>{{ $detail->item->name ?? 'Item Terhapus' }}</td>
                                                                <td class="text-end fw-bold">{{ number_format($detail->quantity, 3) }} <span class="text-muted fw-normal">{{ $detail->item->unit ?? '' }}</span></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-qty">{{ $history->qty }}x</span>
                                @if($history->selling_price > 0)
                                    <div class="text-primary small fw-bold mt-1">Rp {{ number_format($history->selling_price, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($history->stock_transaction_id)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                        <i class="fa-solid fa-check-circle me-1"></i> Stok Terpotong
                                    </span>
                                    <div class="text-muted small mt-1">Ref: #{{ $history->stock_transaction_id }}</div>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                        <i class="fa-solid fa-info-circle me-1"></i> Tanpa Potong Stok
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $history->createdBy->firstname ?? 'System' }}</div>
                            </td>
                            <td class="text-center">
                                @if($history->stock_transaction_id)
                                <a href="{{ route('admin.out_stock.index') }}?search={{ $history->stock_transaction_id }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                    Detail Stok
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat penggunaan resep.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($histories->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
