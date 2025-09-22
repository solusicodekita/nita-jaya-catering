@extends('layouts.app')

@section('title', 'Detail Resep')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Resep</h3>
                    <div class="card-tools">
                        <a href="{{ route('recipes.stock-out.create', $recipe->id) }}" class="btn btn-success">
                            <i class="fas fa-box"></i> Proses Stock Out
                        </a>
                        <a href="{{ route('recipes.edit', $recipe->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
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
                                    <th>Kategori</th>
                                    <td>{{ $recipe->category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Property</th>
                                    <td>{{ $recipe->property }}</td>
                                </tr>
                                <tr>
                                    <th>Yield</th>
                                    <td>{{ $recipe->yield_quantity }} {{ $recipe->yield_unit }}</td>
                                </tr>
                                <tr>
                                    <th>Total Biaya</th>
                                    <td>{{ number_format($recipe->total_cost, 2, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h5 class="mt-4">Bahan-bahan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bahan</th>
                                    <th>Jumlah</th>
                                    <th>Unit</th>
                                    <th>Harga per Unit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recipe->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->ingredient->name }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->unit->name }}</td>
                                    <td class="text-right">{{ number_format($detail->price_per_unit, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detail->total_cost, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Total</th>
                                    <th class="text-right">{{ number_format($recipe->total_cost, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('recipes.index') }}" class="btn btn-default">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
