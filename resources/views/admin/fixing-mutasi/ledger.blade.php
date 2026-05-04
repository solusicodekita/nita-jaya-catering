@extends('layouts.adm.base')

@section('title', 'Ledger ' . $item->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-list-check me-2"></i>Ledger Transaksi: {{ $item->name }}</h5>
                    <a href="{{ route('admin.fixing-mutasi.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block">Lokasi</small>
                                <strong>{{ $warehouse->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block">Terakhir Opname</small>
                                <strong>{{ $opname->date_opname }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block">Stok Opname</small>
                                <strong>{{ number_format($opname->final_stock, 2) }} {{ $item->unit }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="ledgerTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>TX ID</th>
                                    <th>Tipe</th>
                                    <th>Qty</th>
                                    <th>Stok Sebelum (Tercatat)</th>
                                    <th>Stok Sebelum (Kalkulasi)</th>
                                    <th>Running Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">SALDO OPNAME</td>
                                    <td class="fw-bold">{{ number_format($opname->final_stock, 2) }}</td>
                                    <td><span class="badge bg-info">OPNAME</span></td>
                                </tr>
                                @foreach($ledger as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td>#{{ $row['tx_id'] }}</td>
                                    <td>
                                        <span class="badge {{ $row['type'] == 'in' ? 'bg-success' : 'bg-danger' }}">
                                            {{ strtoupper($row['type']) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($row['qty'], 2) }}</td>
                                    <td>{{ number_format($row['stok_sebelumnya_recorded'], 2) }}</td>
                                    <td class="{{ $row['stok_sebelumnya_recorded'] != $row['expected_before'] ? 'text-danger fw-bold' : '' }}">
                                        {{ number_format($row['expected_before'], 2) }}
                                    </td>
                                    <td class="fw-bold text-primary">{{ number_format($row['running_balance'], 2) }}</td>
                                    <td>
                                        @if($row['stok_sebelumnya_recorded'] != $row['expected_before'])
                                            <i class="fas fa-triangle-exclamation text-warning" title="Discrepancy detected!"></i>
                                        @else
                                            <i class="fas fa-check-circle text-success"></i>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#ledgerTable').DataTable({
            "paging": false,
            "info": false,
            "ordering": false
        });
    });
</script>
@endpush
