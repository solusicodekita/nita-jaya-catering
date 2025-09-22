@extends('layouts.app')

@section('title', 'Proses Stock Out Resep')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Proses Stock Out Resep</h3>
                </div>
                <form action="{{ route('recipes.stock-out.process', $recipe->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 200px">Nomor Resep</th>
                                        <td>{{ $recipe->recipe_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $recipe->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Yield per Resep</th>
                                        <td>{{ $recipe->yield_quantity }} {{ $recipe->yield_unit }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="portions">Jumlah Porsi</label>
                                    <input type="number" class="form-control @error('portions') is-invalid @enderror" 
                                           id="portions" name="portions" value="{{ old('portions', 1) }}" required min="1">
                                    @error('portions')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Tanggal</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Preview Bahan yang Akan Dikeluarkan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="preview-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bahan</th>
                                        <th>Jumlah per Resep</th>
                                        <th>Unit</th>
                                        <th>Total Jumlah</th>
                                        <th>Total Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recipe->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->ingredient->name }}</td>
                                        <td class="quantity-per-recipe" data-value="{{ $detail->quantity }}">
                                            {{ $detail->quantity }}
                                        </td>
                                        <td>{{ $detail->unit->name }}</td>
                                        <td class="total-quantity">{{ $detail->quantity }}</td>
                                        <td class="total-cost" data-price="{{ $detail->price_per_unit }}">
                                            {{ number_format($detail->total_cost, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total</th>
                                        <th id="grand-total">{{ number_format($recipe->total_cost, 2, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Proses Stock Out</button>
                        <a href="{{ route('recipes.show', $recipe->id) }}" class="btn btn-default">Batal</a>
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
    function updateCalculations() {
        const portions = parseInt($('#portions').val()) || 1;
        let grandTotal = 0;

        $('#preview-table tbody tr').each(function() {
            const quantityPerRecipe = parseFloat($(this).find('.quantity-per-recipe').data('value'));
            const pricePerUnit = parseFloat($(this).find('.total-cost').data('price'));
            
            const totalQuantity = quantityPerRecipe * portions;
            const totalCost = totalQuantity * pricePerUnit;
            
            $(this).find('.total-quantity').text(totalQuantity.toFixed(2));
            $(this).find('.total-cost').text(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(totalCost));
            
            grandTotal += totalCost;
        });

        $('#grand-total').text(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(grandTotal));
    }

    $('#portions').on('input', updateCalculations);
});
</script>
@endpush
