@extends('layouts.adm.base')

@section('title', 'Kelola Bahan Resep')

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
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Kelola Bahan Resep</h2>
                <p class="text-muted mb-0">Resep: <span class="text-primary fw-bold">{{ $menu->name }}</span></p>
            </div>
            <a href="{{ route('admin.resep.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <form id="formManageIngredients" method="POST" action="{{ route('admin.resep.updateItems', $menu->id) }}">
        @csrf
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="23%">Nama Bahan Baku</th>
                                <th width="15%">Takaran (Input)</th>
                                <th width="12%">Satuan Input</th>
                                <th width="12%" class="text-end">Harga Satuan</th>
                                <th width="12%" class="text-end">Subtotal</th>
                                <th width="17%">Set Konversi Master</th>
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
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="addIngredientRow()">
                            <i class="fa-solid fa-plus me-2"></i>Tambah Baris Bahan
                        </button>
                    </div>
                    <div class="col-md-5">
                        <div class="bg-light p-3 rounded-4 shadow-sm">
                            <div class="calc-row">
                                <span class="calc-label">Total Cost 1 (Bahan Baku)</span>
                                <span class="calc-value" id="total_cost_1">Rp 0</span>
                            </div>
                            <div class="calc-row">
                                <span class="calc-label text-muted">Cost Factor (<span id="cost_factor_label">{{ $menu->cost_factor ?? 20 }}</span>%)</span>
                                <span class="calc-value text-muted" id="cost_factor_val">Rp 0</span>
                            </div>
                            <div class="calc-row border-top-0 pt-0">
                                <span class="calc-label fw-bold">Total Cost 2 (Nett)</span>
                                <span class="calc-value text-primary fs-6" id="total_cost_2">Rp 0</span>
                            </div>
                            <div class="calc-row">
                                <span class="calc-label text-muted">Profit Margin (<span id="profit_margin_label">{{ $menu->profit_margin ?? 30 }}</span>%)</span>
                                <span class="calc-value text-muted" id="profit_margin_val">Rp 0</span>
                            </div>
                            <div class="calc-row bg-primary bg-opacity-10 p-2 rounded-3 mt-2">
                                <span class="calc-label fw-bold text-primary">ESTIMASI HARGA JUAL</span>
                                <span class="calc-value text-primary fs-5" id="price_selling">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow fw-bold">
                        <i class="fa-solid fa-save me-2"></i>Simpan Bahan Resep
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Quick Create Item (Bahan Baku) -->
<!-- Modal Quick Create Item (Bahan Baku) -->
<div class="modal fade" id="modalQuickCreateItem" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="formQuickCreateItem" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Bahan Baku Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category_id" class="form-select rounded-pill px-3" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->code }} - {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Nama Bahan Baku</label>
                            <input type="text" name="name" class="form-control rounded-pill px-3" placeholder="Ketikkan Nama Bahan Baku" autocomplete="off" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Satuan Utama (Unit)</label>
                            <input type="text" name="unit" class="form-control rounded-pill px-3" placeholder="Contoh: Kg, Pcs, Box" autocomplete="off" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Harga Satuan (Rp)</label>
                            <input type="text" name="price" id="quick_price_input" class="form-control rounded-pill px-3" placeholder="Ketikkan Harga" autocomplete="off" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Satuan Eceran (Opsional)</label>
                            <input type="text" name="retail_unit" class="form-control rounded-pill px-3" placeholder="Contoh: Gr, Pcs" autocomplete="off">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Isi per Satuan Utama (Opsional)</label>
                            <input type="number" step="0.0001" name="retail_conversion" class="form-control rounded-pill px-3" placeholder="Contoh: 1000" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow">Simpan Bahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    const availableItems = @json($items);
    const menuDetails = @json($menu->menuDetails);
    let currentCostFactor = {{ $menu->cost_factor ?? 20 }};
    let currentProfitMargin = {{ $menu->profit_margin ?? 30 }};
    let rowCounter = 0;
    let cachedItemOptions = '';

    $(document).ready(function() {
        if (menuDetails && menuDetails.length > 0) {
            menuDetails.forEach((detail) => {
                addIngredientRow(detail.item_id, detail.quantity, true);
            });
        } else {
            addIngredientRow();
        }
        calculateTotals();
    });

    function formatIDR(val) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
    }

    function cleanDecimal(val) {
        if (!val || val === '') return '';
        return parseFloat(parseFloat(val).toFixed(6)).toString();
    }
function getCachedItemOptions() {
        if (!cachedItemOptions) {
            cachedItemOptions = '<option value="">Pilih Bahan Baku</option>';
            availableItems.forEach(item => {
                const unit = (item.unit || '').replace(/"/g, '&quot;');
                const retailUnit = (item.retail_unit || '').replace(/"/g, '&quot;');
                const name = (item.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                cachedItemOptions += `<option value="${item.id}" 
                            data-unit="${unit}" 
                            data-retail="${retailUnit}" 
                            data-conv="${item.retail_conversion || 1}"
                            data-price="${item.price}">
                            ${name} (${unit})
                        </option>`;
            });
        }
        return cachedItemOptions;
    }

    function addIngredientRow(itemId = '', quantity = '', isLoad = false) {
        const index = rowCounter++;
        const displayQty = isLoad ? cleanDecimal(quantity) : quantity;

        const row = `
            <tr class="ingredient-row">
                <td class="text-center">
                    <span class="row-number fw-bold text-muted small"></span>
                </td>
                <td>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="flex-grow-1">
                            <select name="items[${index}][item_id]" class="form-select select2-modal rounded-pill px-3 ingredient-select" required onchange="updateUnitOptions(this)">
                                ${getCachedItemOptions()}
                            </select>
                        </div>
                        <button type="button" class="btn btn-outline-success btn-action btn-sm rounded-circle d-flex align-items-center justify-content-center" onclick="quickCreateItem(${index})" title="Tambah Bahan Baru" style="width: 32px; height: 32px; flex-shrink: 0;">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
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
        const $lastRow = $('#ingredientList tr').last();
        const $select = $lastRow.find('.ingredient-select');
        
        if (itemId) {
            $select.val(itemId);
        }
        
        // Initial setup for unit options
        updateUnitOptions($select[0], isLoad);

        // Initialize Select2 ONLY for the newly added row to prevent conflicts
        $select.select2({
            // dropdownParent removed,
            width: '100%',
            placeholder: 'Pilih Bahan Baku'
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

    let targetRowIndex = null;

    function quickCreateItem(rowIndex) {
        targetRowIndex = rowIndex;
        $('#formQuickCreateItem')[0].reset();
        
        // Prefill name from select2 search term if possible
        let searchTerm = '';
        const activeSelect = $(`select[name="items[${rowIndex}][item_id]"]`);
        const select2Data = activeSelect.data('select2');
        if (select2Data && select2Data.dropdown && select2Data.dropdown.$search) {
            searchTerm = select2Data.dropdown.$search.val() || '';
        }
        $('#formQuickCreateItem input[name="name"]').val(searchTerm.toUpperCase());
        
        $('#modalQuickCreateItem').modal('show');
    }

    function formatRupiah(angka, prefix) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
    }

    $(document).ready(function() {
        // Format price in quick create form
        const quickPriceInput = document.getElementById('quick_price_input');
        if (quickPriceInput) {
            quickPriceInput.addEventListener('input', function(e) {
                let value = this.value.replace(/[^0-9]/g, '');
                this.value = formatRupiah(value, 'Rp ');
            });
        }

        // Force uppercase on name input
        const quickNameInput = document.querySelector('#formQuickCreateItem input[name="name"]');
        if (quickNameInput) {
            quickNameInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }

        // Handle Form Submit
        $('#formQuickCreateItem').on('submit', function(e) {
            e.preventDefault();
            
            const myForm = this;
            const formData = new FormData(myForm);
            
            Swal.fire({
                title: 'Sedang diproses',
                html: 'Mohon ditunggu sampai selesai',
                allowOutsideClick: false,
                allowEscapeKey: false,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            $.ajax({
                url: "{{ route('admin.items.store') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status == 200 && response.item) {
                        const newItem = response.item;
                        
                        Swal.fire({
                            text: "Data sukses tersimpan",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Selesai",
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        }).then(function() {
                            // Close modal
                            $('#modalQuickCreateItem').modal('hide');
                            
                            // Push to local JS array
                            availableItems.push(newItem);
                            
                            // Append to all select dropdowns
                            const newOption = new Option(`${newItem.name} (${newItem.unit})`, newItem.id, false, false);
                            $(newOption).attr('data-unit', newItem.unit)
                                        .attr('data-retail', newItem.retail_unit || '')
                                        .attr('data-conv', newItem.retail_conversion || 1)
                                        .attr('data-price', newItem.price);
                            
                            $('.ingredient-select').each(function() {
                                const $select = $(this);
                                if ($select.find(`option[value="${newItem.id}"]`).length === 0) {
                                    $select.append($(newOption).clone());
                                }
                            });
                            
                            // Set selected item in active row and trigger update
                            if (targetRowIndex !== null) {
                                const targetSelect = $(`select[name="items[${targetRowIndex}][item_id]"]`);
                                targetSelect.val(newItem.id).trigger('change');
                            }
                        });
                    } else {
                        Swal.fire({
                            text: response.message || "Gagal menyimpan data",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    }
                },
                error: function(request, status, error) {
                    if (request.status === 422) {
                        let errors = request.responseJSON.errors;
                        let messages = '';
                        Object.keys(errors).forEach(function(key) {
                            messages += '&bull; ' + errors[key][0] + '<br>';
                        });

                        Swal.fire({
                            title: "Error",
                            html: messages,
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Gagal menyimpan data",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    }
                }
            });
        });

        // Fix scroll lock when stacked modal is closed
        $(document).on('hidden.bs.modal', '#modalQuickCreateItem', function () {
            if ($('#modalManageIngredients').hasClass('show')) {
                $('body').addClass('modal-open');
            }
        });
    });
</script>
@endpush