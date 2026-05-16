@extends('layouts.adm.base')

@section('title', 'Log Aktivitas Management')

@push('styles')
<style>
    .log-card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .badge-action { padding: 0.5em 1em; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .action-CREATE, .action-create { background-color: #d1fae5; color: #065f46; }
    .action-UPDATE, .action-update { background-color: #fef3c7; color: #92400e; }
    .action-DELETE, .action-delete { background-color: #fee2e2; color: #991b1b; }
    .action-ERROR, .action-error { background-color: #dc3545; color: #fff; }
    .action-USE_RECIPE { background-color: #e0e7ff; color: #3730a3; }
    .action-STOCK_OPNAME { background-color: #f3e8ff; color: #6b21a8; }
    
    .diff-table { font-size: 0.85rem; }
    .diff-table th { background-color: #f8f9fa; font-weight: 600; color: #4b5563; }
    .diff-old { background-color: #fff1f2; color: #991b1b; text-decoration: line-through; }
    .diff-new { background-color: #ecfdf5; color: #065f46; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-white border-0 shadow-lg" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><i class="fa-solid fa-clock-rotate-left me-2"></i>Log Aktivitas Management</h2>
                        <p class="mb-0 opacity-75">Pantau semua transaksi dan input data oleh tim Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card log-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>IP Address</th>
                            <th class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-4 small">
                                <div class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($log->user->firstname ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold small">{{ $log->user->firstname ?? 'System' }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $log->user->username ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-action action-{{ $log->action }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-500">{{ $log->description }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    {{ $log->model_type ? basename(str_replace('\\', '/', $log->model_type)) : '' }} 
                                    @if($log->model_id) <span class="badge bg-light text-dark border">#{{ $log->model_id }}</span> @endif
                                </div>
                            </td>
                            <td class="small text-muted">{{ $log->ip_address }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm" onclick="showDetail({{ $log->id }})">
                                    <i class="fa-solid fa-eye me-1"></i> Lihat
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada aktivitas yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Detail Log -->
<div class="modal fade" id="modalLogDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Detail Perubahan Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="logDetailContent">
                    <!-- Dinamis via JS -->
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showDetail(id) {
        $.get(`/admin/activity-logs/${id}`, function(data) {
            let content = '';
            const props = data.properties;

            if (!props || Object.keys(props).length === 0) {
                content = '<div class="alert alert-info">Tidak ada detail data yang dicatat.</div>';
            } else if (props.old || props.new) {
                // Tampilan Perbandingan (Old vs New)
                content = `
                    <div class="table-responsive">
                        <table class="table table-bordered diff-table">
                            <thead>
                                <tr>
                                    <th>Field / Kolom</th>
                                    <th>Data Lama (Sebelum)</th>
                                    <th>Data Baru (Sesudah)</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                // Ambil semua key unik dari old dan new
                const keys = new Set([...Object.keys(props.old || {}), ...Object.keys(props.new || {})]);
                
                keys.forEach(key => {
                    const oldVal = props.old ? props.old[key] : '-';
                    const newVal = props.new ? props.new[key] : '-';
                    
                    if (oldVal != newVal) {
                        content += `
                            <tr>
                                <td class="fw-bold text-capitalize">${key.replace(/_/g, ' ')}</td>
                                <td class="diff-old">${oldVal === null || oldVal === '' ? '<i>(kosong)</i>' : oldVal}</td>
                                <td class="diff-new">${newVal === null || newVal === '' ? '<i>(kosong)</i>' : newVal}</td>
                            </tr>
                        `;
                    }
                });

                content += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                // Tampilan Flat Object / JSON
                content = `
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Key</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                for (const [key, value] of Object.entries(props)) {
                    let displayValue = value;
                    if (typeof value === 'object' && value !== null) {
                        displayValue = '<pre class="mb-0" style="font-size: 0.7rem;">' + JSON.stringify(value, null, 2) + '</pre>';
                    }
                    
                    content += `
                        <tr>
                            <td class="fw-bold text-muted small">${key}</td>
                            <td class="small">${displayValue === null ? '<i>null</i>' : displayValue}</td>
                        </tr>
                    `;
                }

                content += `
                            </tbody>
                        </table>
                    </div>
                `;
            }

            $('#logDetailContent').html(content);
            $('#modalLogDetail').modal('show');
        });
    }
</script>
@endpush
