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
                                                    <td style="text-align: center;">
                                                        <ul style="text-align: left;">
                                                            @foreach ($row->transaksiMenu as $item)
                                                                <li>{{ $item->menu->name }} - {{ $item->qty }}</li>
                                                            @endforeach
                                                        </ul>
                                                        @if($row->transaksiMenu->count() > 0)
                                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editMenuModal{{ $row->id }}">
                                                                <i class="fas fa-edit"></i>Edit Menu
                                                            </button>
                                                        @endif
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

    <!-- Modal Edit Menu untuk setiap transaksi -->
    @foreach ($model as $row)
        @if($row->transaksiMenu->count() > 0)
            <div class="modal fade select2modal" id="editMenuModal{{ $row->id }}" tabindex="-1" aria-labelledby="editMenuModalLabel{{ $row->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMenuModalLabel{{ $row->id }}">Edit Menu - Transaksi #{{ $row->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editMenuForm{{ $row->id }}" action="{{ route('admin.out_stock.updateMenuBulk') }}" method="POST">
                                @csrf
                                <input type="hidden" name="stock_transaction_id" value="{{ $row->id }}">
                                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                                <input type="hidden" name="item_name" value="{{ request('item_name') }}">
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Nama Menu</th>
                                                <th class="text-center">Jumlah</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="trTransaksiMenu{{ $row->id }}">
                                            @foreach ($row->transaksiMenu as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <select class="form-control select2-menu" name="menu[{{ $index + 1 }}][menu_id]" required>
                                                            <option value="{{ $item->menu->id }}">{{ $item->menu->name }}</option>
                                                        </select>
                                                        <input type="hidden" name="menu[{{ $index + 1 }}][id]" value="{{ $item->id }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control qty hanyaAngka" name="menu[{{ $index + 1 }}][qty]" value="{{ $item->qty }}" required>
                                                    </td>
                                                    <td>
                                                        @if($index == 0)
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="addItemMenu(this, {{ $row->id }})">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteItemMenu(this, {{ $row->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
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
            var $stock_transaction_id = $modal.find('input[name="stock_transaction_id"]').val();

            // Fungsi untuk mendapatkan menu yang sudah dipilih
            function getSelectedMenuIds() {
                var selectedIds = [];
                $modal.find('.select2-menu').each(function() {
                    var value = $(this).val();
                    if (value && value !== '') {
                        selectedIds.push(value);
                    }
                });
                return selectedIds;
            }

            // Initialize select2 for menu dropdowns
            $modal.find('.select2-menu').select2({
                dropdownParent: $modal,
                ajax: {
                    url: "{{ route('admin.out_stock.getMenu') }}",
                    dataType: 'json',
                    delay: 250,
                    type: "GET",
                    data: function(params) {
                        var selectedIds = getSelectedMenuIds();
                        return {
                            term: params.term,
                            exclude_ids: selectedIds
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
                },
                placeholder: "-- Pilih Menu --",
                allowClear: false,
                width: '100%'
            });
        });

        function addItemMenu(obj, stockTransactionId) {
            let no = $('#trTransaksiMenu' + stockTransactionId + ' > tr').length + 1;
            if (no > 10) {
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    text: 'Maksimal 10 menu per transaksi!',
                });
                return false;
            }
            
            let tr = `
                <tr>
                    <td>${no}</td>
                    <td>
                        <select class="form-control select2-menu" name="menu[${no}][menu_id]" required>
                        </select>
                        <input type="hidden" name="menu[${no}][id]" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control qty hanyaAngka" name="menu[${no}][qty]" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteItemMenu(this, ${stockTransactionId})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#trTransaksiMenu' + stockTransactionId).append(tr);

            // Re-initialize select2 for the new row
            setTimeout(function() {
                var $modal = $('#editMenuModal' + stockTransactionId);
                
                // Fungsi untuk mendapatkan menu yang sudah dipilih
                function getSelectedMenuIds() {
                    var selectedIds = [];
                    $modal.find('.select2-menu').each(function() {
                        var value = $(this).val();
                        if (value && value !== '') {
                            selectedIds.push(value);
                        }
                    });
                    return selectedIds;
                }
                
                $modal.find('.select2-menu').select2({
                    dropdownParent: $modal,
                    ajax: {
                        url: "{{ route('admin.out_stock.getMenu') }}",
                        dataType: 'json',
                        delay: 250,
                        type: "GET",
                        data: function(params) {
                            var selectedIds = getSelectedMenuIds();
                            return {
                                term: params.term,
                                exclude_ids: selectedIds
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
                    },
                    placeholder: "-- Pilih Menu --",
                    allowClear: false,
                    width: '100%'
                });
                
                // Re-bind hanyaAngka event
                $(".hanyaAngka").on('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }, 100);
        }

        function deleteItemMenu(obj, stockTransactionId) {
            $(obj).parents('tr').remove();
            
            // Renumber rows
            $('#trTransaksiMenu' + stockTransactionId + ' > tr').each(function(index) {
                $(this).find('td:first').text(index + 1); // Update nomor urut (kolom pertama)
                $(this).find('select').attr('name', `menu[${index + 1}][menu_id]`);
                $(this).find('.qty').attr('name', `menu[${index + 1}][qty]`);
                $(this).find('input[type="hidden"]').attr('name', `menu[${index + 1}][id]`);
                
                // Update button pada baris pertama menjadi plus (kolom terakhir)
                if (index === 0) {
                    $(this).find('td:last button').removeClass('btn-danger').addClass('btn-primary')
                        .html('<i class="fas fa-plus"></i>')
                        .attr('onclick', `addItemMenu(this, ${stockTransactionId})`);
                }
            });
        }
    </script>
@endpush
