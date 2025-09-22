@extends('layouts.adm.base')
@section('title', 'Live Stock')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Live Stock</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('live_stock.export_excel') }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </a>
                                    <a href="{{ route('live_stock.export_pdf') }}" class="btn btn-sm btn-danger" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 table-responsive">
                            <table id="tabelStock" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center">Lokasi Item</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Harga Satuan</th>
                                        <th class="text-center">Satuan / Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $row->item->name }}</td>
                                            <td>{{ $row->warehouse->name }}</td>
                                            <td>{{ $row::liveStock($row->item_id, $row->warehouse_id) }}</td>
                                            <td>Rp {{ number_format($row->item->price, 0, ',', '.') }}</td>
                                            <td>{{ $row->item->unit }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tabelStock').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
    </script>
@endpush
