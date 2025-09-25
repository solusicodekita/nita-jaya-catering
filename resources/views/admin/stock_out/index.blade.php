@extends('layouts.adm.base')
@section('title', 'Menu Stok Keluar')
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
                                                    value="{{ request('start_date') ?? date('Y-m-d') }}">
                                            </div>
                                            <div class="col-auto">
                                                <label class="form-label">Tanggal Akhir</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    value="{{ request('end_date') ?? date('Y-m-d') }}">
                                            </div>
                                            <div class="col-auto">
                                                <label class="form-label">Item</label>
                                                <input type="text" class="form-control" name="item_name" placeholder="Cari Item" value="{{ request('item_name') ?? '' }}">
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
                                                <th class="text-center">Item - Jumlah (Stok Sebelumnya) - Lokasi</th>
                                                <th class="text-center">Menu - Jumlah</th>
                                                <th class="text-center">Total Transaksi</th>
                                                <th class="text-center">Keterangan</th>
                                                <th class="text-center">Dibuat Oleh</th>
                                                <th class="text-center">Tanggal Dibuat</th>
                                                <th class="text-center">Diperbarui Oleh</th>
                                                <th class="text-center">Tanggal Diperbarui</th>
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
                                                                    {{ floatval($item->quantity) }} {{ $item->item->unit }} {{ !empty($item->stok_sebelumnya) ? "(" . floatval($item->stok_sebelumnya) . ' ' . $item->item->unit . ")" : '' }}
                                                                    - {{ $item->warehouse->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td style="text-align: left;">
                                                        <ul>
                                                            @foreach ($row->transaksiMenu as $item)
                                                                <li>
                                                                    {{ $item->menu->name }} - {{ $item->qty }}
                                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                                                        <i class="fas fa-pencil-alt"></i>
                                                                    </a>
                                                                </li>
                                                                <!-- Modal -->
                                                                <div class="modal fade select2modal" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel{{ $item->id }}">Edit Menu</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <!-- Form untuk mengedit menu -->
                                                                                <form action="{{ route('admin.out_stock.updateMenu') }}" method="POST">
                                                                                    @csrf
                                                                                    <div class="mb-3">
                                                                                        <label for="menuName" class="form-label">Nama Menu</label>
                                                                                        <select id="menu_id" class="form-select select2" name="menu_id">
                                                                                            <option value="{{ $item->menu->id }}">{{ $item->menu->name }}</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="menuQty" class="form-label">Jumlah</label>
                                                                                        <input type="text" name="qty" class="form-control hanyaAngka" id="qty" value="{{ $item->qty }}">
                                                                                        <input type="hidden" name="id" class="form-control" id="id" value="{{ $item->id }}">
                                                                                        <input type="hidden" name="start_date" class="form-control" id="start_date" value="{{ request('start_date') }}">
                                                                                        <input type="hidden" name="end_date" class="form-control" id="end_date" value="{{ request('end_date') }}">
                                                                                        <input type="hidden" name="item_name" class="form-control" id="item_name" value="{{ request('item_name') }}">
                                                                                    </div>
                                                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                                                    <td>{{ !empty($row->created_at) ? \Carbon\Carbon::parse($row->created_at)->translatedFormat('d F Y H:i:s') : ' ' }}</td>
                                                    <td>{{ $row->updatedBy ? $row->updatedBy->firstname . ' ' . $row->updatedBy->lastname : ' ' }}</td>
                                                    <td>{{ !empty($row->updated_at) ? \Carbon\Carbon::parse($row->updated_at)->translatedFormat('d F Y H:i:s') : ' ' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Tidak ada data</td>
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

        $(document).on('shown.bs.modal', '.select2modal', function() {
            $(".hanyaAngka").on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            var $modal = $(this);
            var $menu = $modal.find('.select2'); // select2 di dalam modal itu saja

            $menu.select2({
                dropdownParent: $modal,
                ajax: {
                    url: "{{ route('admin.out_stock.getMenu') }}",
                    dataType: 'json',
                    delay: 250,
                    type: "GET",
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush
