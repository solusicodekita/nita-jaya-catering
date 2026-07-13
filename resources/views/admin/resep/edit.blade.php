@extends('layouts.adm.base')
@section('title', 'Edit Resep Masakan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="m-0 fw-bold text-primary">Edit Resep: {{ $menu->name }}</h2>
            <a href="{{ route('admin.resep.index') }}" class="btn btn-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.resep.update', $menu->id) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Masakan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-pill px-3" value="{{ old('name', $menu->name) }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. Resep</label>
                        <div class="input-group shadow-sm" style="border-radius: 50px; overflow: hidden;">
                            <input type="text" name="recipe_number" id="recipe_number_input" class="form-control border-0 px-3" value="{{ old('recipe_number', $menu->recipe_number !== 'null' ? $menu->recipe_number : '') }}">
                            <button type="button" class="btn btn-success border-0 px-3" onclick="autoGenerateNumber()" title="Generate Otomatis">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Auto Generate
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="category_ids[]" id="category_select" class="form-select select2-categories rounded-pill px-3" multiple>
                            @php
                                $selectedCats = is_array(old('category_ids')) ? old('category_ids') : $menu->categories->pluck('id')->toArray();
                            @endphp
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCats) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Yield (Hasil)</label>
                        <input type="text" name="yield" class="form-control rounded-pill px-3" value="{{ old('yield', $menu->yield !== 'null' ? $menu->yield : '') }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Cost Factor (%) <i class="fa-solid fa-circle-info text-muted ms-1" title="Persentase biaya tambahan (waste/overhead)"></i></label>
                        <input type="number" name="cost_factor" class="form-control rounded-pill px-3" value="{{ old('cost_factor', $menu->cost_factor) }}" step="0.1">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Profit Margin (%) <i class="fa-solid fa-circle-info text-muted ms-1" title="Persentase keuntungan dari total biaya"></i></label>
                        <input type="number" name="profit_margin" class="form-control rounded-pill px-3" value="{{ old('profit_margin', $menu->profit_margin) }}" step="0.1">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-select rounded-pill px-3" required>
                            <option value="1" {{ old('is_active', $menu->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $menu->is_active) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Potong Stok Otomatis <span class="text-danger">*</span></label>
                        <select name="reduce_stock" class="form-select rounded-pill px-3" required>
                            <option value="1" {{ old('reduce_stock', $menu->reduce_stock) == '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('reduce_stock', $menu->reduce_stock) == '0' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi / Cara Masak</label>
                        <textarea name="description" class="form-control rounded-4 p-3" rows="4">{{ old('description', $menu->description) }}</textarea>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">
                            <i class="fas fa-save me-2"></i>Perbarui Resep
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-categories').select2({
            placeholder: 'Pilih Kategori (Bisa lebih dari satu)',
            width: '100%',
            allowClear: true
        });
    });

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
</script>
@endpush
