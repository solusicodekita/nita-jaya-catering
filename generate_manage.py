import re

with open('resources/views/admin/resep/index.blade.php', 'r') as f:
    content = f.read()

# Generate new blade file
new_content = """@extends('layouts.adm.base')

@section('title', 'Kelola Bahan Resep')

@push('style')
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
"""

quick_create = re.search(r'<!-- Modal Quick Create Item.*?</div>\n    </div>\n</div>', content, re.DOTALL)
if quick_create:
    new_content += quick_create.group(0) + "\n"

new_content += "@endsection\n\n@push('script')\n<script>\n"

js_part = """
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
"""
new_content += js_part

# Extract the rest of the JS functions
js_rest = re.search(r'function getCachedItemOptions.*?$(.*?)</script>', content, re.DOTALL | re.MULTILINE)
if js_rest:
    rest_code = js_rest.group(0).replace("dropdownParent: $('#modalManageIngredients')", "// dropdownParent removed")
    
    # We must remove useRecipe function from rest_code since it belongs to index
    rest_code = re.sub(r'function useRecipe\(.*?\)\s*\{.*?\}', '', rest_code, flags=re.DOTALL)
    
    # We also need to change the AJAX success behavior of formQuickCreateItem if needed (it looks fine)
    
    # We also want formManageIngredients to NOT be an ajax submit, or if we want it to redirect:
    # Wait, the original formManageIngredients in index.blade.php didn't have AJAX! It was a standard submit. Let me check.
    # Ah, in index.blade.php there's no JS for `formManageIngredients` submit. It submitted normally!
    
    new_content += rest_code + "\n@endpush"
else:
    print("Failed to extract JS rest")

with open('resources/views/admin/resep/manage_items.blade.php', 'w') as f:
    f.write(new_content)

print("Created manage_items.blade.php")
