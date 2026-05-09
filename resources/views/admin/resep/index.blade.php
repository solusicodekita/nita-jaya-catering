@extends('layouts.adm.base')

@section('title', 'Resep & Manajemen Bahan')

@push('styles')
<style>
    .resep-card { border-radius: 20px; border: none; transition: all 0.3s ease; overflow: hidden; }
    .resep-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .ingredient-badge { font-size: 0.75rem; transition: all 0.2s; }
    .ingredient-badge:hover { background-color: #e9ecef !important; }
    .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
    .unit-badge { font-size: 0.65rem; padding: 2px 8px; border-radius: 50px; background: #e9ecef; color: #495057; font-weight: 600; }
    .mini-input { font-size: 0.75rem !important; padding: 0.2rem 0.5rem !important; height: auto !important; border-radius: 8px !important; }
    .preview-box { font-size: 0.7rem; color: #0d6efd; font-weight: 600; margin-top: 2px; min-height: 15px; }
    .highlight-select { border-color: #0d6efd !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Daftar Resep Masakan</h2>
                <p class="text-muted">Kelola resep dan takaran bahan baku untuk operasional katering.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalResep">
                <i class="fa-solid fa-plus me-2"></i>Tambah Resep Baru
            </button>
        </div>
    </div>

    <div class="row">
        @forelse($menus as $menu)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card resep-card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold text-dark mb-1">{{ $menu->name }}</h4>
                            <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3" style="font-size: 0.7rem;">
                                {{ $menu->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-action" onclick="editResep('{{ $menu->id }}', '{{ $menu->name }}', '{{ $menu->description }}', '{{ $menu->is_active }}', '{{ $menu->reduce_stock }}')" title="Edit Deskripsi">
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

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="small">
                            <i class="fa-solid fa-history me-1 text-muted"></i> Total Digunakan: <strong>{{ $menu->total_usage }}x</strong>
                        </div>
                        <div class="small text-muted">
                            Last: <strong>{{ $menu->transaksiMenus->first() ? $menu->transaksiMenus->first()->created_at->diffForHumans() : '-' }}</strong>
                        </div>
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
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Potong Stok Otomatis</label>
                            <select name="reduce_stock" class="form-select rounded-pill px-3">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
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
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Nama Bahan Baku</th>
                                    <th width="20%">Takaran (Input)</th>
                                    <th width="15%">Satuan Input</th>
                                    <th width="30%">Set Konversi Master (Update Item)</th>
                                    <th width="5%"></th>
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
    const availableItems = @json($items);
    
    function cleanDecimal(val) {
        if (!val || val === '') return '';
        return parseFloat(parseFloat(val).toFixed(6)).toString();
    }

    function editResep(id, name, description, isActive, reduceStock) {
        $('#modalResepTitle').text('Edit Resep');
        $('#formResep').attr('action', `/admin/resep/update/${id}`);
        $('#formResep input[name="name"]').val(name);
        $('#formResep textarea[name="description"]').val(description);
        $('#formResep select[name="is_active"]').val(isActive);
        $('#formResep select[name="reduce_stock"]').val(reduceStock);
        $('#modalResep').modal('show');
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
        $('#resepNameDisplay').text(name);
        $('#formManageIngredients').attr('action', `/admin/resep/update-items/${id}`);
        $('#ingredientList').empty();
        
        const menus = @json($menus);
        const menu = menus.find(m => m.id == id);
        
        if (menu && menu.menu_details.length > 0) {
            menu.menu_details.forEach((detail, index) => {
                addIngredientRow(detail.item_id, detail.quantity, true);
            });
        } else {
            addIngredientRow();
        }
        
        $('#modalManageIngredients').modal('show');
    }

    function addIngredientRow(itemId = '', quantity = '', isLoad = false) {
        const index = $('#ingredientList tr').length;
        let options = '<option value="">Pilih Bahan Baku</option>';
        
        availableItems.forEach(item => {
            options += `<option value="${item.id}" ${itemId == item.id ? 'selected' : ''} 
                        data-unit="${item.unit}" 
                        data-retail="${item.retail_unit || ''}" 
                        data-conv="${item.retail_conversion || 1}">
                        ${item.name} (${item.unit})
                    </option>`;
        });

        const displayQty = isLoad ? cleanDecimal(quantity) : quantity;

        const row = `
            <tr>
                <td>
                    <select name="items[${index}][item_id]" class="form-select select2-modal rounded-pill px-3 ingredient-select" required onchange="updateUnitOptions(this)">
                        ${options}
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="items[${index}][quantity]" class="form-control rounded-start-pill px-3 ingredient-qty" step="any" value="${displayQty}" placeholder="Takaran" required oninput="updateConversionPreview(this)">
                        <span class="input-group-text bg-light border-start-0 rounded-end-pill small dynamic-unit-label" style="font-size: 0.7rem;">-</span>
                    </div>
                    <div class="preview-box px-2" id="preview_${index}"></div>
                </td>
                <td>
                    <select name="items[${index}][unit_type]" class="form-select rounded-pill px-3 unit-select" onchange="updateConversionPreview(this)">
                        <!-- Dinamis -->
                    </select>
                </td>
                <td class="bg-light rounded-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="items[${index}][retail_unit]" class="form-control mini-input retail-unit-input" placeholder="Satuan Ecer" oninput="updateAllLabels(this)">
                        </div>
                        <div class="col-md-2 text-center small text-muted" style="font-size: 0.6rem;">Isi:</div>
                        <div class="col-md-5">
                            <input type="number" name="items[${index}][retail_conversion]" class="form-control mini-input retail-conv-input" placeholder="Isi" oninput="updateAllLabels(this)">
                        </div>
                    </div>
                    <div class="mt-1 text-center" style="font-size: 0.55rem; color: #888;">
                        Master: 1 <span class="main-unit-label">-</span> = <span class="retail-val-label">-</span> <span class="retail-unit-label">-</span>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-link text-danger p-0" onclick="$(this).closest('tr').remove()">
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
        const unitOption = row.find('.unit-select option:selected');
        const unitType = unitOption.val();
        const unitName = unitOption.data('unit');
        const conv = parseFloat(row.find('.retail-conv-input').val()) || 1;
        const mainUnit = row.find('.main-unit-label').first().text();
        
        row.find('.dynamic-unit-label').text(unitName || '-');

        let previewText = '';
        if (unitType === 'retail' && qty > 0) {
            const finalVal = qty / conv;
            previewText = `Simpan sebagai: ${parseFloat(finalVal.toFixed(6))} ${mainUnit}`;
        } else if (unitType === 'main' && qty > 0 && conv > 1) {
            const retailVal = qty * conv;
            const retailUnit = row.find('.retail-unit-input').val() || 'Eceran';
            previewText = `Setara: ${parseFloat(retailVal.toFixed(2))} ${retailUnit}`;
        }
        
        row.find('.preview-box').text(previewText);
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
