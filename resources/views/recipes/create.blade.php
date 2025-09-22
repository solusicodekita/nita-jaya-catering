@extends('layouts.adm.base')

@section('title', 'Tambah Resep Baru')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Resep Baru</h3>
                </div>
                <form action="{{ route('recipes.store') }}" method="POST" id="recipe-form">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama Resep</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Kategori</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="property">Property</label>
                                    <input type="text" class="form-control @error('property') is-invalid @enderror" 
                                           id="property" name="property" value="{{ old('property') }}" required>
                                    @error('property')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="yield_quantity">Jumlah Porsi</label>
                                    <input type="number" class="form-control @error('yield_quantity') is-invalid @enderror" 
                                           id="yield_quantity" name="yield_quantity" value="{{ old('yield_quantity') }}" required>
                                    @error('yield_quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="yield_unit">Unit Porsi</label>
                                    <input type="text" class="form-control @error('yield_unit') is-invalid @enderror" 
                                           id="yield_unit" name="yield_unit" value="{{ old('yield_unit') }}" required>
                                    @error('yield_unit')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Bahan-bahan</h5>
                        <div id="ingredients-container">
                            <div class="ingredient-row row mb-3">
                                <div class="col-md-4">
                                    <select class="form-control ingredient-select" name="ingredients[0][ingredient_id]" required>
                                        <option value="">Pilih Bahan</option>
                                        @foreach($ingredients as $ingredient)
                                            <option value="{{ $ingredient->id }}" 
                                                    data-price="{{ $ingredient->price }}"
                                                    data-unit="{{ $ingredient->unit }}">
                                                {{ $ingredient->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" class="form-control quantity-input" 
                                           name="ingredients[0][quantity]" placeholder="Jumlah" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control unit-input" 
                                           name="ingredients[0][unit]" readonly>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" class="form-control price-input" 
                                           name="ingredients[0][price_per_unit]" placeholder="Harga per Unit" required>
                                </div>
                                <div class="col-md-1">
                                    <input type="text" class="form-control total-cost" readonly>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger remove-ingredient">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" id="add-ingredient">
                            <i class="fas fa-plus"></i> Tambah Bahan
                        </button>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('recipes.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    let ingredientIndex = 0;

    function updateTotalCost(row) {
        const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        const price = parseFloat(row.find('.price-input').val()) || 0;
        const total = quantity * price;
        row.find('.total-cost').val(new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(total));
    }

    function updateIngredientIndexes() {
        $('.ingredient-row').each(function(index) {
            $(this).find('select, input').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
        });
    }

    $('#add-ingredient').click(function() {
        ingredientIndex++;
        const newRow = $('.ingredient-row').first().clone();
        newRow.find('input').val('');
        newRow.find('select').prop('selectedIndex', 0);
        $('#ingredients-container').append(newRow);
        updateIngredientIndexes();
    });

    $(document).on('click', '.remove-ingredient', function() {
        if ($('.ingredient-row').length > 1) {
            $(this).closest('.ingredient-row').remove();
            updateIngredientIndexes();
        }
    });

    $(document).on('change', '.ingredient-select', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price');
        const unit = selectedOption.data('unit');
        const row = $(this).closest('.ingredient-row');
        row.find('.price-input').val(price);
        row.find('.unit-input').val(unit);
        updateTotalCost(row);
    });

    $(document).on('input', '.quantity-input, .price-input', function() {
        updateTotalCost($(this).closest('.ingredient-row'));
    });
});
</script>
@endpush