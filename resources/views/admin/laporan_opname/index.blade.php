@extends('layouts.adm.base')
@section('title', 'Laporan Stok Opname')
@section('content')
<div class="app-content">
    <div class="container-fluid">
        <!-- Header Page -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1 text-primary fw-bold">
                    <i class="fa-solid fa-clipboard-check me-2"></i> Laporan Stok Opname
                </h4>
                <p class="text-muted mb-0 small">Monitoring & Rekapitulasi Stok Opname Berkala (Siklus 2 Mingguan / Tengah & Akhir Bulan)</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.laporan_opname.export_excel', request()->all()) }}" class="btn btn-success btn-sm">
                    <i class="fa-solid fa-file-excel me-1"></i> Download Excel
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="konfirmasiKunciOpname()">
                    <i class="fa-solid fa-lock me-1"></i> Kunci Checkpoint Opname
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card Filter -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-filter me-2 text-primary"></i> Filter Periode Opname
                </h6>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.laporan_opname.index') }}" id="formFilter">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Bulan</label>
                            <select name="bulan" id="filterBulan" class="form-select form-select-sm">
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $mVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $mVal }}" {{ $bulan == $mVal ? 'selected' : '' }}>
                                        {{ \App\Models\LaporanTransaksi::convertBulan($mVal) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tahun</label>
                            <select name="tahun" id="filterTahun" class="form-select form-select-sm">
                                @for ($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Siklus Periode</label>
                            <select name="periode" id="filterPeriode" class="form-select form-select-sm" onchange="toggleCustomDate()">
                                <option value="1" {{ $periode == '1' ? 'selected' : '' }}>Periode 1 (Tgl 01 s/d 15)</option>
                                <option value="2" {{ $periode == '2' ? 'selected' : '' }}>Periode 2 (Tgl 16 s/d Akhir Bulan)</option>
                                <option value="custom" {{ $periode == 'custom' ? 'selected' : '' }}>Kustom Rentang Tanggal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Lokasi Gudang</label>
                            <select name="warehouse_id" class="form-select form-select-sm">
                                <option value="all" {{ $warehouseId == 'all' ? 'selected' : '' }}>-- Semua Gudang --</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan
                            </button>
                        </div>
                    </div>

                    <!-- Input Kustom Tanggal (jika dipilih) -->
                    <div class="row g-3 mt-1" id="customDateRow" style="display: {{ $periode == 'custom' ? 'flex' : 'none' }};">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Awal</label>
                            <input type="date" name="tgl_awal" class="form-control form-control-sm" value="{{ $tglAwal }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Akhir</label>
                            <input type="date" name="tgl_akhir" class="form-control form-control-sm" value="{{ $tglAkhir }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary KPI Widget -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary p-3 rounded">
                            <i class="fa-solid fa-boxes-stacked fa-xl"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Jenis Item</div>
                            <div class="fs-5 fw-bold text-dark">{{ number_format($summary['total_items']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 text-info p-3 rounded">
                            <i class="fa-solid fa-clock-rotate-left fa-xl"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Stok Awal</div>
                            <div class="fs-5 fw-bold text-dark">{{ number_format($summary['total_stok_awal'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 text-success p-3 rounded">
                            <i class="fa-solid fa-arrow-down-long fa-xl"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Masuk (+)</div>
                            <div class="fs-5 fw-bold text-success">+{{ number_format($summary['total_masuk'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 text-danger p-3 rounded">
                            <i class="fa-solid fa-arrow-up-long fa-xl"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Keluar (-)</div>
                            <div class="fs-5 fw-bold text-danger">-{{ number_format($summary['total_keluar'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Opname -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="badge bg-primary px-3 py-2 fs-6">
                    <i class="fa-solid fa-calendar-day me-1"></i> {{ $periodeLabel }}
                </span>
                <span class="text-muted small">
                    Rentang: <strong>{{ date('d M Y', strtotime($tglAwal)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($tglAkhir)) }}</strong>
                </span>
            </div>
            <div class="card-body p-3 table-responsive">
                <table id="tabelOpname" class="table table-bordered table-hover align-middle w-100">
                    <thead class="table-light text-center small">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Kode</th>
                            <th>Nama Item</th>
                            <th>Kategori</th>
                            <th>Lokasi Gudang</th>
                            <th>Satuan</th>
                            <th class="bg-light">Stok Awal</th>
                            <th class="text-success">Masuk (+)</th>
                            <th class="text-danger">Keluar (-)</th>
                            <th class="bg-primary text-white">Stok Akhir Sistem</th>
                            <th style="width: 120px;">Stok Fisik</th>
                            <th>Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($reportData as $row)
                            @php
                                $isHabis = $row['stok_akhir'] <= 0;
                            @endphp
                            <tr id="row-{{ $loop->iteration }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-muted font-monospace">{{ $row['item_code'] ?? '-' }}</td>
                                <td class="fw-bold text-dark">{{ $row['item_name'] }}</td>
                                <td><span class="badge bg-secondary">{{ $row['category_name'] }}</span></td>
                                <td>
                                    @if(stripos($row['warehouse_name'], 'DAPUR') !== false)
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-fire-burner me-1"></i>{{ $row['warehouse_name'] }}</span>
                                    @else
                                        <span class="badge bg-info text-dark"><i class="fa-solid fa-warehouse me-1"></i>{{ $row['warehouse_name'] }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $row['unit'] }}</td>
                                <td class="text-end fw-semibold">{{ number_format($row['stok_awal'], 2) }}</td>
                                <td class="text-end text-success">+{{ number_format($row['masuk'], 2) }}</td>
                                <td class="text-end text-danger">-{{ number_format($row['keluar'], 2) }}</td>
                                <td class="text-end fw-bold {{ $isHabis ? 'text-danger bg-danger bg-opacity-10' : 'text-primary bg-primary bg-opacity-10' }}">
                                    {{ number_format($row['stok_akhir'], 2) }}
                                </td>
                                <td>
                                    <input type="number" step="any" class="form-control form-control-sm text-end input-fisik" 
                                        placeholder="0" data-row="{{ $loop->iteration }}" data-sistem="{{ $row['stok_akhir'] }}" 
                                        oninput="hitungSelisih(this)">
                                </td>
                                <td class="text-end fw-bold selisih-col" id="selisih-{{ $loop->iteration }}">-</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i>
                                    Tidak ada data stok untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Form Hidden untuk Kunci Checkpoint -->
<form id="formKunciOpname" method="POST" action="{{ route('admin.laporan_opname.simpan_checkpoint') }}" style="display: none;">
    @csrf
    <input type="hidden" name="tgl_awal" value="{{ $tglAwal }}">
    <input type="hidden" name="tgl_akhir" value="{{ $tglAkhir }}">
    <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}">
</form>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tabelOpname').DataTable({
            paging: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            ordering: true,
            responsive: true,
            language: {
                search: "Cari Item:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ item",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            }
        });
    });

    function toggleCustomDate() {
        const val = $('#filterPeriode').val();
        if (val === 'custom') {
            $('#customDateRow').slideDown();
        } else {
            $('#customDateRow').slideUp();
        }
    }

    function hitungSelisih(el) {
        const rowId = $(el).data('row');
        const sistem = parseFloat($(el).data('sistem')) || 0;
        const valStr = $(el).val();

        if (valStr === '' || isNaN(valStr)) {
            $('#selisih-' + rowId).html('-').removeClass('text-danger text-success');
            return;
        }

        const fisik = parseFloat(valStr);
        const selisih = (fisik - sistem);
        const elSelisih = $('#selisih-' + rowId);

        if (selisih > 0) {
            elSelisih.html('+' + selisih.toFixed(2)).removeClass('text-danger').addClass('text-success');
        } else if (selisih < 0) {
            elSelisih.html(selisih.toFixed(2)).removeClass('text-success').addClass('text-danger');
        } else {
            elSelisih.html('0.00').removeClass('text-danger text-success').addClass('text-muted');
        }
    }

    function konfirmasiKunciOpname() {
        Swal.fire({
            title: 'Kunci Checkpoint Opname?',
            html: `Apakah Anda yakin ingin menyimpan saldo opname periode <strong>{{ $periodeLabel }}</strong>?<br><small class='text-muted'>Tindakan ini akan mengunci saldo akhir sistem tanggal {{ date('d-m-Y', strtotime($tglAkhir)) }} sebagai patokan saldo awal periode berikutnya.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-lock me-1"></i> Ya, Kunci Opname',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Checkpoint...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $('#formKunciOpname').submit();
            }
        });
    }
</script>
@endpush
