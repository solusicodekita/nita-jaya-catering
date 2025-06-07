@extends('layouts.adm.base')
@section('title', 'Menu Stok Masuk')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Riwayat Stok Keluar</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('admin.out_stock.create') }}" class="btn btn-primary"><i
                                            class="fas fa-plus"></i> Tambah</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <form action="{{ route('admin.out_stock.index') }}" method="GET" class="form-inline">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-auto">
                                                <label class="form-label">Tanggal Awal</label>
                                                <input type="date" class="form-control" name="start_date"
                                                    value="{{ request('start_date') }}">
                                            </div>
                                            <div class="col-auto">
                                                <label class="form-label">Tanggal Akhir</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                            <div class="col-auto" style="margin-top: 32px;">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                                <a href="{{ route('admin.out_stock.index') }}"
                                                    class="btn btn-secondary">Reset</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive">
                                    <table id="tabelStock" class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Tanggal Transaksi</th>
                                                <th class="text-center">Item - Jumlah - Lokasi</th>
                                                <th class="text-center">Total Transaksi</th>
                                                <th class="text-center">Keterangan</th>
                                                <th class="text-center">Dibuat Oleh</th>
                                                <th class="text-center">Diperbarui Oleh</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($model as $row)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ date('d-m-Y H:i', strtotime($row->date)) }}</td>
                                                    <td style="text-align: left;">
                                                        <ul>
                                                            @foreach ($row->stockTransactionDetails as $item)
                                                                <li>{{ $item->item->name }} -
                                                                    {{ floatval($item->quantity) }} {{ $item->item->unit }}
                                                                    - {{ $item->warehouse->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>Rp {{ number_format($row->total_harga_keseluruhan, 2, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        @if ($row->stockTransactionDetails->isNotEmpty())
                                                            @foreach ($row->stockTransactionDetails as $item)
                                                                {{ $item->description ?? '-' }}
                                                                @if (!$loop->last)
                                                                    <br>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $row->createdBy ? $row->createdBy->firstname . ' ' . $row->createdBy->lastname : ' ' }}</td>
                                                    <td>{{ $row->updatedBy ? $row->updatedBy->firstname . ' ' . $row->updatedBy->lastname : ' ' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
            @if (count($model) > 0)
                $('#tabelStock').DataTable({
                    responsive: true,
                    searching: false,
                });
            @endif
        });
    </script>
@endpush
