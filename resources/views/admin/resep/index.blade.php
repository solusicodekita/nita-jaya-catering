@extends('layouts.adm.base')

@section('title', 'Resep & Manajemen Bahan')

@push('styles')
<style>
    .ingredient-badge { font-size: 0.75rem; transition: all 0.2s; max-width: 100%; }
    .ingredient-badge:hover { background-color: #e9ecef !important; }
    .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0; }
    .unit-badge { font-size: 0.65rem; padding: 2px 8px; border-radius: 50px; background: #e9ecef; color: #495057; font-weight: 600; }
    .mini-input { font-size: 0.75rem !important; padding: 0.2rem 0.5rem !important; height: auto !important; border-radius: 8px !important; }
    .preview-box { font-size: 0.7rem; color: #0d6efd; font-weight: 600; margin-top: 2px; min-height: 15px; }
    .highlight-select { border-color: #0d6efd !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important; }
    .calc-row { border-top: 1px dashed #dee2e6; padding: 8px 0; display: flex; justify-content: space-between; align-items: center; }
    .calc-label { font-size: 0.8rem; color: #6c757d; font-weight: 500; }
    .calc-value { font-size: 0.85rem; font-weight: 700; color: #212529; }
    
    #ingredientList { counter-reset: rowNumber; }
    #ingredientList .ingredient-row { counter-increment: rowNumber; }
    #ingredientList .row-number::before { content: counter(rowNumber); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1">Daftar Resep Masakan</h2>
                <p class="text-muted mb-0">Kelola resep dan takaran bahan baku untuk operasional katering.</p>
            </div>
            <div class="d-flex gap-2 w-100 w-md-auto flex-wrap justify-content-md-end">
                <a href="{{ route('admin.resep.usage-history') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm w-100 w-md-auto">
                    <i class="fa-solid fa-history me-2"></i>Riwayat Penggunaan
                </a>
                <button type="button" class="btn btn-info text-white rounded-pill px-4 shadow-sm w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                    <i class="fa-solid fa-file-excel me-2"></i>Import Excel
                </button>
                <a href="{{ route('admin.resep.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm w-100 w-md-auto">
                    <i class="fa-solid fa-plus me-2"></i>Tambah Resep Baru
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="resepTable" class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Kode Resep</th>
                            <th>Nama Resep</th>
                            <th>Kategori</th>
                            <th>Estimasi Jual</th>
                            <th>Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $index => $menu)
                        @php
                            $totalCost1 = 0;
                            foreach($menu->menuDetails as $detail) {
                                $totalCost1 += $detail->quantity * $detail->item->price;
                            }
                            $costFactorVal = $totalCost1 * ($menu->cost_factor / 100);
                            $totalCost2 = $totalCost1 + $costFactorVal;
                            $profitMarginVal = $totalCost2 * ($menu->profit_margin / 100);
                            $sellingPrice = $totalCost2 + $profitMarginVal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><span class="badge bg-primary text-white">{{ $menu->recipe_number ?? '-' }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $menu->name }}</div>
                                @if($menu->yield)
                                <small class="text-muted"><i class="fa-solid fa-utensils me-1"></i>{{ $menu->yield }}</small>
                                @endif
                            </td>
                            <td>
                                @forelse($menu->categories as $cat)
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2">{{ $cat->name }}</span>
                                @empty
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">Uncategorized</span>
                                @endforelse
                            </td>
                            <td class="fw-bold text-success">Rp {{ number_format($sellingPrice, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3">
                                    {{ $menu->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary rounded-circle mb-1" onclick="useRecipe('{{ $menu->id }}', '{{ $menu->name }}')" title="Gunakan Resep">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                                <a href="{{ route('admin.resep.manageItems.show', $menu->id) }}" class="btn btn-sm btn-info rounded-circle mb-1 text-white" title="Kelola Bahan">
                                    <i class="fa-solid fa-list-check"></i>
                                </a>
                                <a href="{{ route('admin.resep.edit', $menu->id) }}" class="btn btn-sm btn-warning rounded-circle mb-1 text-white" title="Edit Resep">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger rounded-circle mb-1" onclick="deleteResep('{{ $menu->id }}')" title="Hapus Resep">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Resep -->
<div class="modal fade" id="modalImportExcel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('admin.resep.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Import Data Resep & Bahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted mb-4">Pastikan format excel Anda sesuai dengan standar sistem. Kategori dan Bahan yang belum ada akan otomatis ditambahkan.</p>
                    <a href="{{ route('admin.resep.download-template') }}" class="btn btn-outline-primary rounded-pill px-4 mb-4">
                        <i class="fa-solid fa-download me-2"></i>Download Template Standar
                    </a>
                    
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input class="form-control rounded-pill px-3" style="padding: 10px;" type="file" name="file_excel" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function useRecipe(id, name) {
        window.location.href = `/admin/resep/use/${id}`;
    }
</script>
@endpush
