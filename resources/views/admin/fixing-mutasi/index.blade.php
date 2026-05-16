@extends('layouts.adm.base')

@section('title', 'Fixing Mutasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-tools me-2"></i>Fixing Mutasi & Opname</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Halaman ini digunakan untuk memperbaiki data mutasi stok yang tidak konsisten atau memperbaiki data opname yang salah.
                    </div>

                    <div class="row">
                        <!-- Recalculate Stock -->
                        <div class="col-md-6">
                            <div class="card border-primary mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Kalkulasi Ulang Stok Sebelumnya</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.fixing-mutasi.recalculate') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Item</label>
                                            <select name="item_id" class="form-select select2" required>
                                                <option value="">-- Pilih Item --</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Lokasi</label>
                                            <select name="warehouse_id" class="form-select select2" required>
                                                <option value="">-- Pilih Lokasi --</option>
                                                @foreach($warehouses as $wh)
                                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Apakah Anda yakin ingin melakukan kalkulasi ulang? Proses ini akan mengubah field stok_sebelumnya pada riwayat transaksi.')">
                                            <i class="fas fa-play me-2"></i>Mulai Kalkulasi Ulang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Fix Opname -->
                        <div class="col-md-6">
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Perbaiki Data Opname</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.fixing-mutasi.fix-opname') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Item</label>
                                            <select name="item_id" class="form-select select2-opname" required>
                                                <option value="">-- Pilih Item --</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Lokasi</label>
                                            <select name="warehouse_id" class="form-select select2-opname" required>
                                                <option value="">-- Pilih Lokasi --</option>
                                                @foreach($warehouses as $wh)
                                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Opname (YYYY-MM-DD HH:MM:SS)</label>
                                            <input type="text" name="date" class="form-control" placeholder="2026-04-30 23:59:59" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Stok Akhir Baru</label>
                                            <input type="number" step="0.0001" name="final_stock" class="form-control" placeholder="0.00" required>
                                        </div>
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan Opname
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Force Adjust Stock (Superadmin Tool) -->
                        <div class="col-md-12">
                            <div class="card border-danger mb-3">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Force Adjust Stock (Direct Correction)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning py-2 small">
                                        <i class="fas fa-exclamation-triangle me-2"></i> <b>PERINGATAN:</b> Fitur ini akan membuat transaksi penyesuaian otomatis (In/Out) agar stok saat ini sama dengan target. Gunakan hanya jika ada bug sistem atau selisih yang tidak bisa dijelaskan.
                                    </div>
                                    <form action="{{ route('admin.fixing-mutasi.force-adjust') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Pilih Item</label>
                                                    <select name="item_id" class="form-select select2-force" required>
                                                        <option value="">-- Pilih Item --</option>
                                                        @foreach($items as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Pilih Lokasi</label>
                                                    <select name="warehouse_id" class="form-select select2-force" required>
                                                        <option value="">-- Pilih Lokasi --</option>
                                                        @foreach($warehouses as $wh)
                                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Stok Saat Ini</label>
                                                    <div class="input-group">
                                                        <input type="text" id="current_stock_display" class="form-control bg-light" readonly placeholder="Pilih item & lokasi...">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="checkCurrentStock()"><i class="fas fa-sync"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Target Stok Baru</label>
                                                    <input type="number" step="0.0001" name="target_stock" class="form-control border-danger" placeholder="0.00" required>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Koreksi</label>
                                                    <input type="text" name="reason" class="form-control" placeholder="Contoh: Perbaikan minus stok karena bug sistem" required>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin? Sistem akan menghitung selisih dan membuat transaksi penyesuaian secara otomatis.')">
                                            <i class="fas fa-wrench me-2"></i>Eksekusi Koreksi Stok
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ledger View -->
                        <div class="col-md-12">
                            <div class="card border-info mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-list-check me-2"></i>Cek Ledger Running Balance (Audit)</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.fixing-mutasi.ledger') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="form-label">Pilih Item</label>
                                                    <select name="item_id" class="form-select select2" required>
                                                        <option value="">-- Pilih Item --</option>
                                                        @foreach($items as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="form-label">Pilih Lokasi</label>
                                                    <select name="warehouse_id" class="form-select select2" required>
                                                        <option value="">-- Pilih Lokasi --</option>
                                                        @foreach($warehouses as $wh)
                                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="submit" class="btn btn-info text-white w-100">
                                                        <i class="fas fa-search me-1"></i>Cek Ledger
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        $('.select2, .select2-opname, .select2-force').select2({
            theme: 'bootstrap-5'
        });

        const fp = flatpickr("input[name='date']", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:s",
            enableSeconds: true,
            time_24hr: true,
            defaultDate: "2026-04-30 23:59:59"
        });

        // Auto-fill latest opname date for Perbaiki Data Opname
        $('.select2-opname').on('change', function() {
            const itemId = $('select[name="item_id"].select2-opname').val();
            const warehouseId = $('select[name="warehouse_id"].select2-opname').val();
            
            if (itemId && warehouseId) {
                $.ajax({
                    url: "{{ route('admin.fixing-mutasi.get-latest-opname') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: itemId,
                        warehouse_id: warehouseId
                    },
                    success: function(response) {
                        if (response.date) {
                            fp.setDate(response.date);
                        }
                    }
                });
            }
        });

        // Auto-check stock for Force Adjust
        $('.select2-force').on('change', function() {
            checkCurrentStock();
        });
    });

    function checkCurrentStock() {
        const itemId = $('select[name="item_id"].select2-force').val();
        const warehouseId = $('select[name="warehouse_id"].select2-force').val();
        
        if (itemId && warehouseId) {
            $('#current_stock_display').val('Loading...');
            $.ajax({
                url: "{{ route('admin.fixing-mutasi.get-current-stock') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    item_id: itemId,
                    warehouse_id: warehouseId
                },
                success: function(response) {
                    $('#current_stock_display').val(response.stock);
                },
                error: function() {
                    $('#current_stock_display').val('Error');
                }
            });
        }
    }
</script>
@endpush
