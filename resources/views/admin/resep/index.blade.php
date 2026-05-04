@extends('layouts.adm.base')

@section('title', 'Dashboard Resep')

@push('styles')
<style>
    .resep-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        border: none;
        border-radius: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        background: #fff;
        height: 100%;
    }
    .resep-card:hover {
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        transform: translateY(-5px);
    }
    .resep-card .card-body {
        padding: 1.5rem;
    }
    .resep-title {
        font-weight: 800;
        color: #2c3e50;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    .ingredient-badge {
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .ingredient-badge:hover {
        background: #e9ecef !important;
    }
    .btn-use {
        border-radius: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .status-badge {
        font-size: 0.65rem;
        padding: 0.4rem 0.8rem;
    }
    .hover-opacity:hover {
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white border-0 shadow-lg" style="border-radius: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="fw-bold mb-1"><i class="fa-solid fa-utensils me-2"></i>Dashboard Resep</h2>
                            <p class="mb-0 opacity-75">Kelola template resep dan monitor penggunaan bahan baku secara cerdas.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center mt-3 mt-md-0">
                            <button class="btn btn-white text-primary fw-bold shadow-sm px-4 py-2" style="border-radius: 12px; background: #fff;" data-bs-toggle="modal" data-bs-target="#modalTambahResep">
                                <i class="fa-solid fa-plus me-1 text-primary"></i>
                                Tambah Resep Baru
                            </button>
                            <!-- <a href="{{ route('admin.setting.index') }}" class="bg-white bg-opacity-25 p-2 rounded d-flex align-items-center text-decoration-none hover-opacity" title="Klik untuk mengubah pengaturan global">
                                <div class="text-white text-end me-3">
                                    <div class="small opacity-75">Stok Global</div>
                                    <div class="fw-bold">{{ $setting->default_reduce_stock ? 'Otomatis' : 'Manual' }}</div>
                                </div>
                                <i class="fa-solid fa-gears text-white fs-4"></i>
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="resepContainer">
        @forelse($menus as $menu)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card resep-card">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="resep-title mb-1">{{ $menu->name }}</h5>
                            @if($menu->is_active)
                            <span class="badge bg-success status-badge rounded-pill">Aktif</span>
                            @else
                            <span class="badge bg-secondary status-badge rounded-pill">Non-Aktif</span>
                            @endif

                            <!-- @if($menu->reduce_stock)
                            <span class="badge bg-danger status-badge rounded-pill ms-1"><i class="fa-solid fa-minus-circle me-1"></i>Potong Stok</span>
                            @endif -->
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-light text-primary border" onclick="editResep({{ $menu->id }}, '{{ addslashes($menu->name) }}', '{{ addslashes($menu->description) }}', {{ $menu->is_active }}, {{ $menu->reduce_stock }})" title="Edit Resep">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-info border" onclick="manageIngredients({{ $menu->id }}, '{{ addslashes($menu->name) }}')" title="Kelola Bahan">
                                <i class="fa-solid fa-list-check"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-danger border" onclick="deleteResep({{ $menu->id }})" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-muted small mb-4 flex-grow-1">
                        {{ $menu->description ?: 'Tidak ada deskripsi.' }}
                    </p>

                    <div class="mb-4">
                        <h6 class="small fw-bold text-uppercase text-muted mb-3">Bahan Utama:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($menu->menuDetails as $detail)
                            <div class="ingredient-badge bg-light border p-2 rounded d-flex align-items-center">
                                <span class="small fw-bold">{{ $detail->item->name }}</span>
                                <span class="ms-2 badge bg-primary bg-opacity-10 text-primary">{{ number_format($detail->quantity, 2) }} {{ $detail->item->unit }}</span>
                            </div>
                            @empty
                            <div class="text-muted small italic"><i class="fa-solid fa-circle-info me-1"></i>Belum ada bahan.</div>
                            @endforelse
                        </div>
                    </div>

                    <hr class="opacity-10">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-muted small">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i>
                            Total Digunakan: <span class="fw-bold text-dark">{{ $menu->total_usage }}x</span>
                        </div>
                        <div class="text-muted small text-end">
                            @if($menu->transaksiMenus->count() > 0)
                                @php
                                    $lastUsage = $menu->transaksiMenus->sortByDesc('created_at')->first();
                                @endphp
                                Last: <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($lastUsage->created_at)->diffForHumans() }}</span>
                            @else
                                <span class="fst-italic">Belum pernah</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary btn-use py-2 shadow-sm" onclick="useRecipe({{ $menu->id }}, '{{ addslashes($menu->name) }}')">
                            <i class="fa-solid fa-play me-2"></i>Gunakan Resep
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="opacity-50 mb-3">
                <i class="fa-solid fa-utensils fs-1"></i>
            </div>
            <h4>Belum ada resep.</h4>
            <p class="text-muted">Klik tombol "Tambah Resep Baru" untuk memulai.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah/Edit Resep -->
<div class="modal fade" id="modalResep" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="formResep" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold" id="modalResepTitle">Tambah Resep Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Resep <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-pill px-3" required placeholder="Contoh: Lontong Kikil">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" style="border-radius: 15px;" placeholder="Tuliskan catatan resep..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="is_active" class="form-select rounded-pill px-3">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                        <!-- <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-danger">Potong Stok?</label>
                            <select name="reduce_stock" class="form-select rounded-pill px-3">
                                <option value="0">Tidak (Manual)</option>
                                <option value="1">Ya (Otomatis)</option>
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Jika Ya, stok bahan akan berkurang setiap resep digunakan.</small>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan Resep</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kelola Bahan -->
<div class="modal fade" id="modalManageIngredients" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="formManageIngredients" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">Kelola Bahan Resep</h5>
                        <p class="text-muted small mb-0" id="resepNameDisplay"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60%">Nama Bahan Baku</th>
                                    <th width="30%">Takaran (Per Porsi)</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody id="ingredientList">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-2" onclick="addIngredientRow()">
                        <i class="fa-solid fa-plus me-1"></i>Tambah Baris Bahan
                    </button>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan Bahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Gunakan Resep (Multiplier) -->
<div class="modal fade" id="modalUseRecipe" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="formUseRecipe" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0" style="background: #ffc107; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold">Gunakan Resep</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted small mb-4">Berapa porsi yang ingin Anda proses untuk <strong id="useResepName" class="text-dark"></strong>?</p>
                    
                    <label class="form-label fw-bold">Jumlah Porsi (Multiplier)</label>
                    <div class="input-group mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <button class="btn btn-outline-secondary border-0 px-3" type="button" onclick="decrementValue()">-</button>
                        <input type="number" name="multiplier" id="multiplierInput" class="form-control text-center fw-bold border-0" value="1" min="1" step="0.5">
                        <button class="btn btn-outline-secondary border-0 px-3" type="button" onclick="incrementValue()">+</button>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2 rounded-pill shadow">Konfirmasi & Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi Data Bahan Baku (untuk Select2 nanti)
    const availableItems = @json($items);
    
    function editResep(id, name, description, isActive, reduceStock) {
        $('#modalResepTitle').text('Edit Resep');
        $('#formResep').attr('action', `/admin/resep/update/${id}`);
        $('#formResep input[name="name"]').val(name);
        $('#formResep textarea[name="description"]').val(description);
        $('#formResep select[name="is_active"]').val(isActive);
        $('#formResep select[name="reduce_stock"]').val(reduceStock);
        $('#modalResep').modal('show');
    }

    // Reset modal saat dibuka untuk tambah baru
    $('[data-bs-target="#modalTambahResep"]').on('click', function() {
        $('#modalResepTitle').text('Tambah Resep Baru');
        $('#formResep').attr('action', '{{ route('admin.resep.store') }}');
        $('#formResep')[0].reset();
        $('#modalResep').modal('show');
    });

    function deleteResep(id) {
        Swal.fire({
            title: 'Hapus Resep?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = `/admin/resep/destroy/${id}`;
                form.method = 'POST';
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function manageIngredients(id, name) {
        $('#resepNameDisplay').text(name);
        $('#formManageIngredients').attr('action', `/admin/resep/update-items/${id}`);
        $('#ingredientList').empty();
        
        // Ambil data bahan resep via AJAX atau parsing dari data yang sudah ada
        // Untuk demo, kita cari menu di koleksi JS
        const menus = @json($menus);
        const menu = menus.find(m => m.id == id);
        
        if (menu && menu.menu_details.length > 0) {
            menu.menu_details.forEach((detail, index) => {
                addIngredientRow(detail.item_id, detail.quantity);
            });
        } else {
            addIngredientRow();
        }
        
        $('#modalManageIngredients').modal('show');
    }

    function addIngredientRow(itemId = '', quantity = '') {
        const index = $('#ingredientList tr').length;
        let options = '<option value="">Pilih Bahan Baku</option>';
        availableItems.forEach(item => {
            options += `<option value="${item.id}" ${itemId == item.id ? 'selected' : ''}>${item.name} (${item.unit})</option>`;
        });

        const row = `
            <tr>
                <td>
                    <select name="items[${index}][item_id]" class="form-select select2-modal rounded-pill px-3" required>
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity]" class="form-control rounded-pill px-3" step="0.01" value="${quantity}" placeholder="Takaran" required>
                </td>
                <td>
                    <button type="button" class="btn btn-link text-danger p-0" onclick="$(this).closest('tr').remove()">
                        <i class="fa-solid fa-circle-xmark fs-5"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#ingredientList').append(row);
        
        $('.select2-modal').select2({
            dropdownParent: $('#modalManageIngredients'),
            width: '100%'
        });
    }

    function useRecipe(id, name) {
        $('#useResepName').text(name);
        $('#formUseRecipe').attr('action', `/admin/resep/use/${id}`);
        $('#multiplierInput').val(1);
        $('#modalUseRecipe').modal('show');
    }

    function incrementValue() {
        let input = $('#multiplierInput');
        input.val(parseFloat(input.val()) + 1);
    }

    function decrementValue() {
        let input = $('#multiplierInput');
        if (parseFloat(input.val()) > 1) {
            input.val(parseFloat(input.val()) - 1);
        }
    }
</script>
@endpush
