@extends('layouts.adm.base')

@section('title', 'Resep & Manajemen Bahan')

@push('styles')
<style>
    .resep-card { border-radius: 20px; border: none; transition: all 0.3s ease; overflow: hidden; }
    .resep-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
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
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .container-fluid { padding-left: 1rem !important; padding-right: 1rem !important; }
        .card-body { padding: 1.25rem !important; }
        .resep-card h4 { font-size: 1.15rem; }
    }
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
            <button class="btn btn-success rounded-pill px-4 shadow-sm w-100 w-md-auto" onclick="addResep()">
                <i class="fa-solid fa-plus me-2"></i>Tambah Resep Baru
            </button>
        </div>
    </div>

    <div class="row">
        @forelse($menus as $menu)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card resep-card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge bg-primary rounded-pill px-2" style="font-size: 0.6rem;">{{ $menu->recipe_number ?? 'No #' }}</span>
                                @forelse($menu->categories as $cat)
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2" style="font-size: 0.6rem;">{{ $cat->name }}</span>
                                @empty
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2" style="font-size: 0.6rem;">Uncategorized</span>
                                @endforelse
                            </div>
                            <h4 class="fw-bold text-dark mb-1">{{ $menu->name }}</h4>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3" style="font-size: 0.7rem;">
                                    {{ $menu->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                                @if($menu->yield)
                                <span class="text-muted small"><i class="fa-solid fa-utensils me-1"></i>{{ $menu->yield }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0 pt-1">
                            <button class="btn btn-outline-primary btn-action" onclick="editResep('{{ $menu->id }}', '{{ $menu->name }}', '{{ addslashes($menu->description) }}', '{{ $menu->is_active }}', '{{ $menu->reduce_stock }}', '{{ $menu->recipe_number }}', '{{ json_encode($menu->categories->pluck('id')) }}', '{{ $menu->yield }}', '{{ $menu->cost_factor }}', '{{ $menu->profit_margin }}')" title="Edit Deskripsi">
                                <i class="fa-solid fa-edit fs-6"></i>
                            </button>
                            <button class="btn btn-outline-info btn-action" onclick="manageIngredients('{{ $menu->id }}', '{{ $menu->name }}')" title="Kelola Bahan">
                                <i class="fa-solid fa-list-check fs-6"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-action" onclick="deleteResep('{{ $menu->id }}')" title="Hapus Resep">
                                <i class="fa-solid fa-trash fs-6"></i>
                            </button>
                        </div>
                    </div>

                    <p class="text-muted small mb-4 line-clamp-3" style="min-height: 4.5em;">
                        {{ $menu->description ?: 'Tidak ada deskripsi untuk resep ini.' }}
                    </p>

                    <div class="mb-4">
                        <h6 class="small fw-bold text-uppercase text-muted mb-3">Bahan Utama:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($menu->menuDetails as $detail)
                            @php
                                $displayQty = (float)$detail->quantity;
                                $displayUnit = $detail->item->unit;
                                
                                if ($displayQty < 1 && $detail->item->retail_unit && $detail->item->retail_conversion > 1) {
                                    $displayQty = $displayQty * $detail->item->retail_conversion;
                                    $displayUnit = $detail->item->retail_unit;
                                }
                            @endphp
                            <div class="ingredient-badge bg-light border p-2 rounded d-flex align-items-center">
                                <span class="small fw-bold">{{ $detail->item->name }}</span>
                                <span class="ms-2 badge bg-primary bg-opacity-10 text-primary">
                                    {{ rtrim(rtrim(number_format($displayQty, 4, '.', ''), '0'), '.') }} {{ $displayUnit }}
                                </span>
                            </div>
                            @empty
                            <div class="text-muted small italic"><i class="fa-solid fa-circle-info me-1"></i>Belum ada bahan.</div>
                            @endforelse
                        </div>
                    </div>

                    <hr class="opacity-10">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div class="small">
                            <i class="fa-solid fa-history me-1 text-muted"></i> Total Digunakan: <strong>{{ $menu->total_usage }}x</strong>
                        </div>
                        <div class="small text-muted">
                            Last: <strong>{{ $menu->transaksiMenus->first() ? $menu->transaksiMenus->first()->created_at->diffForHumans() : '-' }}</strong>
                        </div>
                    </div>

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

                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span class="small fw-bold text-primary">ESTIMASI JUAL:</span>
                        <span class="fw-bold text-primary">Rp {{ number_format($sellingPrice, 0, ',', '.') }}</span>
                    </div>

                    <button class="btn btn-primary w-100 fw-bold py-2 rounded-pill shadow-sm" onclick="useRecipe('{{ $menu->id }}', '{{ $menu->name }}')">
                        <i class="fa-solid fa-play me-2"></i>GUNAKAN RESEP
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <h5 class="text-muted">Belum ada resep yang terdaftar.</h5>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Form Resep -->
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
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Nama Masakan</label>
                            <input type="text" name="name" class="form-control rounded-pill px-3" placeholder="Contoh: Ayam Bakar Madu" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">No. Resep</label>
                            <div class="input-group shadow-sm" style="border-radius: 50px; overflow: hidden;">
                                <input type="text" name="recipe_number" id="recipe_number_input" class="form-control border-0 px-3" placeholder="001/..." style="font-size: 0.9rem;">
                                <button type="button" class="btn btn-success border-0 px-3" onclick="autoGenerateNumber()" title="Generate Otomatis">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Auto Generate
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category_ids[]" id="category_select" class="form-select select2-categories rounded-pill px-3" multiple>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Yield (Hasil)</label>
                            <input type="text" name="yield" class="form-control rounded-pill px-3" placeholder="Contoh: 10 Porsi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cost Factor (%) <i class="fa-solid fa-circle-info text-muted ms-1" title="Persentase biaya tambahan (waste/overhead)"></i></label>
                            <input type="number" name="cost_factor" class="form-control rounded-pill px-3" value="20" step="0.1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Profit Margin (%) <i class="fa-solid fa-circle-info text-muted ms-1" title="Persentase keuntungan dari total biaya"></i></label>
                            <input type="number" name="profit_margin" class="form-control rounded-pill px-3" value="30" step="0.1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi / Cara Masak</label>
                            <textarea name="description" class="form-control rounded-4 p-3" rows="3" placeholder="Jelaskan singkat tentang masakan ini..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="is_active" class="form-select rounded-pill px-3">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                        <!-- <div class="col-md-6">
                            <label class="form-label fw-bold">Potong Stok Otomatis</label>
                            <select name="reduce_stock" class="form-select rounded-pill px-3">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
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
    <div class="modal-dialog modal-xl modal-dialog-centered">
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
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%">Nama Bahan Baku</th>
                                    <th width="15%">Takaran (Input)</th>
                                    <th width="12%">Satuan Input</th>
                                    <th width="12%" class="text-end">Harga Satuan</th>
                                    <th width="12%" class="text-end">Subtotal</th>
                                    <th width="20%">Set Konversi Master</th>
                                    <th width="4%"></th>
                                </tr>
                            </thead>
                            <tbody id="ingredientList">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-7">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="addIngredientRow()">
                                <i class="fa-solid fa-plus me-1"></i>Tambah Baris Bahan
                            </button>
                        </div>
                        <div class="col-md-5">
                            <div class="bg-light p-3 rounded-4 shadow-sm">
                                <div class="calc-row">
                                    <span class="calc-label">Total Cost 1 (Bahan Baku)</span>
                                    <span class="calc-value" id="total_cost_1">Rp 0</span>
                                </div>
                                <div class="calc-row">
                                    <span class="calc-label text-muted">Cost Factor (<span id="cost_factor_label">20</span>%)</span>
                                    <span class="calc-value text-muted" id="cost_factor_val">Rp 0</span>
                                </div>
                                <div class="calc-row border-top-0 pt-0">
                                    <span class="calc-label fw-bold">Total Cost 2 (Nett)</span>
                                    <span class="calc-value text-primary fs-6" id="total_cost_2">Rp 0</span>
                                </div>
                                <div class="calc-row">
                                    <span class="calc-label text-muted">Profit Margin (<span id="profit_margin_label">30</span>%)</span>
                                    <span class="calc-value text-muted" id="profit_margin_val">Rp 0</span>
                                </div>
                                <div class="calc-row bg-primary bg-opacity-10 p-2 rounded-3 mt-2">
                                    <span class="calc-label fw-bold text-primary">ESTIMASI HARGA JUAL</span>
                                    <span class="calc-value text-primary fs-5" id="price_selling">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
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
    const availableItems = @json($items);
    let currentCostFactor = 20;
    let currentProfitMargin = 30;
    
    function formatIDR(val) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
    }

    function cleanDecimal(val) {
        if (!val || val === '') return '';
        return parseFloat(parseFloat(val).toFixed(6)).toString();
    }

    function addResep() {
        $('#modalResepTitle').text('Tambah Resep Baru');
        $('#formResep').attr('action', "{{ route('admin.resep.store') }}");
        $('#formResep')[0].reset();
        $('#category_select').val([]).trigger('change');
        $('#recipe_number_input').val('');
        $('#modalResep').modal('show');
    }

    function editResep(id, name, description, isActive, reduceStock, recipeNumber, categoryIds, yield, costFactor, profitMargin) {
        $('#modalResepTitle').html(`Edit Resep: <span class="text-primary">${name}</span>`);
        $('#formResep').attr('action', `/admin/resep/update/${id}`);
        $('#formResep input[name="name"]').val(name);
        $('#formResep input[name="recipe_number"]').val(recipeNumber !== 'null' ? recipeNumber : '');
        
        try {
            const catIds = JSON.parse(categoryIds);
            $('#category_select').val(catIds).trigger('change');
        } catch(e) {
            $('#category_select').val([]).trigger('change');
        }

        $('#formResep input[name="yield"]').val(yield !== 'null' ? yield : '');
        $('#formResep input[name="cost_factor"]').val(costFactor);
        $('#formResep input[name="profit_margin"]').val(profitMargin);
        $('#formResep textarea[name="description"]').val(description);
        $('#formResep select[name="is_active"]').val(isActive);
        $('#formResep select[name="reduce_stock"]').val(reduceStock);
        $('#modalResep').modal('show');
    }

    function autoGenerateNumber() {
        const categoryId = $('#category_select').val();
        const btn = event.currentTarget;
        const originalHtml = $(btn).html();
        
        $(btn).html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.resep.generateNumber') }}",
            type: 'GET',
            data: { category_id: categoryId },
            success: function(response) {
                if (response.status === 'success') {
                    $('#recipe_number_input').val(response.number);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Gagal generate nomor resep', 'error');
            },
            complete: function() {
                $(btn).html(originalHtml).prop('disabled', false);
            }
        });
    }

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
        const menus = @json($menus);
        const menu = menus.find(m => m.id == id);
        
        currentCostFactor = menu ? menu.cost_factor : 20;
        currentProfitMargin = menu ? menu.profit_margin : 30;
        
        $('#cost_factor_label').text(currentCostFactor);
        $('#profit_margin_label').text(currentProfitMargin);
        
        $('#modalManageIngredients .modal-title').html(`Kelola Bahan Resep: <span class="text-primary">${name}</span>`);
        $('#resepNameDisplay').text('Resep: ' + name);
        $('#formManageIngredients').attr('action', `/admin/resep/update-items/${id}`);
        $('#ingredientList').empty();
        
        if (menu && menu.menu_details.length > 0) {
            menu.menu_details.forEach((detail, index) => {
                addIngredientRow(detail.item_id, detail.quantity, true);
            });
        } else {
            addIngredientRow();
        }
        
        calculateTotals();
        $('#modalManageIngredients').modal('show');
    }

    function addIngredientRow(itemId = '', quantity = '', isLoad = false) {
        const index = $('#ingredientList tr').length;
        let options = '<option value="">Pilih Bahan Baku</option>';
        
        availableItems.forEach(item => {
            options += `<option value="${item.id}" ${itemId == item.id ? 'selected' : ''} 
                        data-unit="${item.unit}" 
                        data-retail="${item.retail_unit || ''}" 
                        data-conv="${item.retail_conversion || 1}"
                        data-price="${item.price}">
                        ${item.name} (${item.unit})
                    </option>`;
        });

        const displayQty = isLoad ? cleanDecimal(quantity) : quantity;

        const row = `
            <tr class="ingredient-row">
                <td>
                    <select name="items[${index}][item_id]" class="form-select select2-modal rounded-pill px-3 ingredient-select" required onchange="updateUnitOptions(this)">
                        ${options}
                    </select>
                </td>
                <td>
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <input type="number" name="items[${index}][quantity]" class="form-control border-0 px-3 ingredient-qty" step="any" value="${displayQty}" placeholder="0" required oninput="updateConversionPreview(this)">
                        <span class="input-group-text bg-white border-0 small dynamic-unit-label text-muted" style="font-size: 0.7rem;">-</span>
                    </div>
                    <div class="preview-box px-2" id="preview_${index}"></div>
                </td>
                <td>
                    <select name="items[${index}][unit_type]" class="form-select rounded-pill px-2 unit-select" style="font-size: 0.75rem;" onchange="updateConversionPreview(this)">
                        <!-- Dinamis -->
                    </select>
                </td>
                <td class="text-end">
                    <span class="small text-muted display-unit-price">-</span>
                </td>
                <td class="text-end fw-bold">
                    <span class="display-subtotal">-</span>
                </td>
                <td class="bg-light rounded-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="items[${index}][retail_unit]" class="form-control mini-input retail-unit-input" placeholder="Ecer" oninput="updateAllLabels(this)">
                        </div>
                        <div class="col-md-2 text-center small text-muted" style="font-size: 0.5rem;">=</div>
                        <div class="col-md-5">
                            <input type="number" name="items[${index}][retail_conversion]" class="form-control mini-input retail-conv-input" placeholder="Isi" oninput="updateAllLabels(this)">
                        </div>
                    </div>
                    <div class="mt-1 text-center" style="font-size: 0.55rem; color: #888;">
                        1 <span class="main-unit-label">-</span> = <span class="retail-val-label">-</span> <span class="retail-unit-label">-</span>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-link text-danger p-0" onclick="$(this).closest('tr').remove(); calculateTotals();">
                        <i class="fa-solid fa-circle-xmark fs-5"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#ingredientList').append(row);
        
        const lastRow = $('#ingredientList tr').last();
        updateUnitOptions(lastRow.find('.ingredient-select')[0], isLoad);

        $('.select2-modal').select2({
            dropdownParent: $('#modalManageIngredients'),
            width: '100%'
        });
    }

    function updateUnitOptions(select, isLoad = false) {
        const option = $(select).find(':selected');
        const unit = option.data('unit');
        const retail = option.data('retail');
        const conv = option.data('conv');
        const price = option.data('price');
        const row = $(select).closest('tr');
        const qtyInput = row.find('.ingredient-qty');
        const currentVal = parseFloat(qtyInput.val()) || 0;
        
        row.find('.retail-unit-input').val(retail);
        row.find('.retail-conv-input').val(conv > 1 ? parseFloat(conv) : '');
        row.find('.main-unit-label').text(unit || '-');
        
        const unitSelect = row.find('.unit-select');
        unitSelect.empty();
        if (unit) {
            unitSelect.append(`<option value="main" data-unit="${unit}" data-conv="1">Utama (${unit})</option>`);
            if (retail && conv > 1) {
                unitSelect.append(`<option value="retail" data-unit="${retail}" data-conv="${conv}">Eceran (${retail})</option>`);
            }
        }

        if (isLoad && currentVal > 0 && currentVal < 1 && retail && conv > 1) {
            const retailVal = currentVal * conv;
            qtyInput.val(cleanDecimal(retailVal));
            unitSelect.val('retail');
        }

        updateAllLabels(row.find('.retail-unit-input')[0], false);
        updateConversionPreview(qtyInput[0]);
    }

    function updateAllLabels(input, autoSwitch = true) {
        const row = $(input).closest('tr');
        const retailUnit = row.find('.retail-unit-input').val() || '-';
        const retailConv = row.find('.retail-conv-input').val() || '-';
        const mainUnit = row.find('.main-unit-label').first().text();

        row.find('.retail-unit-label').text(retailUnit);
        row.find('.retail-val-label').text(retailConv);

        const unitSelect = row.find('.unit-select');
        const hasRetailOption = unitSelect.find('option[value="retail"]').length > 0;
        
        if (retailUnit !== '-' && retailConv !== '-' && !isNaN(retailConv) && retailConv > 1) {
            if (!hasRetailOption) {
                unitSelect.append(`<option value="retail" data-unit="${retailUnit}" data-conv="${retailConv}">Eceran (${retailUnit})</option>`);
                if (autoSwitch) {
                    unitSelect.val('retail').addClass('highlight-select');
                    setTimeout(() => unitSelect.removeClass('highlight-select'), 2000);
                }
            } else {
                unitSelect.find('option[value="retail"]').text('Eceran (' + retailUnit + ')').data('unit', retailUnit).data('conv', retailConv);
            }
        }
        updateConversionPreview(row.find('.ingredient-qty')[0]);
    }

    function updateConversionPreview(input) {
        const row = $(input).closest('tr');
        const qty = parseFloat(row.find('.ingredient-qty').val()) || 0;
        const option = row.find('.ingredient-select :selected');
        const basePrice = parseFloat(option.data('price')) || 0;
        
        const unitOption = row.find('.unit-select option:selected');
        const unitType = unitOption.val();
        const unitName = unitOption.data('unit');
        const conv = parseFloat(row.find('.retail-conv-input').val()) || 1;
        const mainUnit = row.find('.main-unit-label').first().text();
        
        row.find('.dynamic-unit-label').text(unitName || '-');

        let subtotal = 0;
        let pricePerUnit = basePrice;
        let previewText = '';

        if (unitType === 'retail' && qty > 0) {
            const finalVal = qty / conv;
            pricePerUnit = basePrice / conv;
            subtotal = finalVal * basePrice;
            previewText = `Simpan sebagai: ${parseFloat(finalVal.toFixed(6))} ${mainUnit}`;
        } else if (unitType === 'main' && qty > 0) {
            subtotal = qty * basePrice;
            if (conv > 1) {
                const retailVal = qty * conv;
                const retailUnit = row.find('.retail-unit-input').val() || 'Eceran';
                previewText = `Setara: ${parseFloat(retailVal.toFixed(2))} ${retailUnit}`;
            }
        }

        row.find('.display-unit-price').text(formatIDR(pricePerUnit));
        row.find('.display-subtotal').text(formatIDR(subtotal)).data('value', subtotal);
        row.find('.preview-box').text(previewText);
        
        calculateTotals();
    }

    function calculateTotals() {
        let totalCost1 = 0;
        $('.display-subtotal').each(function() {
            totalCost1 += parseFloat($(this).data('value')) || 0;
        });

        const costFactorVal = totalCost1 * (currentCostFactor / 100);
        const totalCost2 = totalCost1 + costFactorVal;
        const profitMarginVal = totalCost2 * (currentProfitMargin / 100);
        const priceSelling = totalCost2 + profitMarginVal;

        $('#total_cost_1').text(formatIDR(totalCost1));
        $('#cost_factor_val').text(formatIDR(costFactorVal));
        $('#total_cost_2').text(formatIDR(totalCost2));
        $('#profit_margin_val').text(formatIDR(profitMarginVal));
        $('#price_selling').text(formatIDR(priceSelling));
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

    $(document).ready(function() {
        $('.select2-categories').select2({
            dropdownParent: $('#modalResep'),
            placeholder: 'Pilih Kategori (Bisa lebih dari satu)',
            width: '100%',
            allowClear: true
        });
    });
</script>
@endpush
