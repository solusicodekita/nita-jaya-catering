@extends('layouts.adm.base')
@section('title', 'Edit Adjustment Stock')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Form Edit Adjustment Stock</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('admin.adjustment_stock.index') }}" class="btn btn-primary"><i
                                            class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="formSave" method="POST" action="{{ route('admin.adjustment_stock.update', $stockTransactionDetail->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="item_id">Item <span class="text-danger">*</span></label>
                                    <select class="form-control ubahSelect" name="item_id" id="item_id"
                                        onchange="getWarehouse(this)">
                                        <option value="">-- Pilih Item --</option>
                                        @foreach ($item as $row)
                                            <option value="{{ $row->id }}" {{ $stockTransactionDetail->item_id == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="warehouse_id">Lokasi Item <span class="text-danger">*</span></label>
                                    <select class="form-control" name="warehouse_id" id="warehouse_id"
                                        onchange="cekJumlahTerakir()">
                                        <option value="">-- Pilih Lokasi --</option>
                                        @if($stockTransactionDetail->warehouse)
                                            <option value="{{ $stockTransactionDetail->warehouse_id }}" selected>{{ $stockTransactionDetail->warehouse->name }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="jumlah_adjustment">Jumlah Adjustment <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control desimal" name="jumlah_adjustment"
                                        id="jumlah_adjustment" autocomplete="off" placeholder="..." value="{{ $stockTransactionDetail->quantity }}">
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="tipe_adjustment">Tipe Adjustment <span class="text-danger">*</span></label>
                                    <select class="form-control ubahSelect" name="tipe_adjustment" id="tipe_adjustment">
                                        <option value="out" {{ $stockTransactionDetail->stockTransaction->type == 'out' ? 'selected' : '' }}>Berkurang</option>
                                        <option value="in" {{ $stockTransactionDetail->stockTransaction->type == 'in' ? 'selected' : '' }}>Bertambah</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="kategori_adjustment">Kategori Adjustment <span class="text-danger">*</span></label>
                                    <select class="form-control ubahSelect" name="kategori_adjustment" id="kategori_adjustment">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="stok" {{ $stockTransactionDetail->stockTransaction->kategori_adjustment == 'stok' ? 'selected' : '' }}>Stok</option>
                                        <option value="qty" {{ $stockTransactionDetail->stockTransaction->kategori_adjustment == 'qty' ? 'selected' : '' }}>Qty</option>
                                        <option value="pengembalian" {{ $stockTransactionDetail->stockTransaction->kategori_adjustment == 'pengembalian' ? 'selected' : '' }}>Pengembalian</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="alasan_adjustment">Alasan Adjustment <span
                                            class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" name="alasan_adjustment"
                                        id="alasan_adjustment" autocomplete="off" placeholder="...">{{ $stockTransactionDetail->stockTransaction->alasan_adjustment }}</textarea>
                                </div>
                                <hr>
                                <button type="button" class="btn btn-success" onclick="validasi(this)">
                                    <i class="fa fa-save"></i> Update
                                </button>
                            </form>
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
            $(".desimal").keypress(function(e) {
                var charCode = (e.which) ? e.which : event.keyCode;
                var value = $(this).val();
                if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
                    return false;
                }
                // Memastikan hanya ada satu titik
                if (charCode == 46 && value.indexOf('.') !== -1) {
                    return false;
                }
                return true;
            });

            $('#item_id').select2({
                placeholder: "-- Pilih Item --",
                allowClear: false
            });

            $('#kategori_adjustment').select2({
                placeholder: "-- Pilih Kategori --",
                allowClear: false
            });

            // Load warehouse saat pertama kali
            var item_id = $('#item_id').val();
            if (item_id != '') {
                getWarehouse(document.getElementById('item_id'));
            }
        });

        function cekJumlahTerakir() {
            let item_id = $('#item_id').val();
            let warehouse_id = $('#warehouse_id').val();

            if (item_id != '' && warehouse_id != '') {
                $.ajax({
                    url: "{{ route('admin.adjustment_stock.cekJumlahTerakhir') }}",
                    type: 'GET',
                    data: {
                        item_id: item_id,
                        warehouse_id: warehouse_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 1) {
                            let stokAkhir = parseFloat(response.stokAkhir);
                            if (stokAkhir % 1 === 0) {
                                $('#jumlah_adjustment').val(parseInt(stokAkhir));
                            } else {
                                $('#jumlah_adjustment').val(stokAkhir);
                            }
                        } else {
                            $('#jumlah_adjustment').val('0');
                        }
                    }
                });
            }
        }

        function getWarehouse(obj) {
            var item_id = $(obj).val();
            $.ajax({
                url: "{{ route('admin.adjustment_stock.getWarehouse') }}",
                type: "GET",
                data: {
                    item_id: item_id
                },
                dataType: "json",
                success: function(response) {
                    var $warehouseSelect = $(obj).parents('form').find('#warehouse_id');
                    var currentWarehouseId = '{{ $stockTransactionDetail->warehouse_id }}';
                    
                    // Destroy existing Select2 instance
                    try {
                        $warehouseSelect.select2('destroy');
                    } catch (e) {
                        // Ignore if not initialized
                    }
                    
                    // Update HTML content
                    $warehouseSelect.html(response);
                    
                    // Set selected warehouse jika masih sama
                    if (currentWarehouseId) {
                        $warehouseSelect.val(currentWarehouseId);
                    }
                    
                    // Re-initialize Select2 for the updated warehouse dropdown
                    $warehouseSelect.select2({
                        placeholder: "-- Pilih Lokasi --",
                        allowClear: false,
                        width: '100%',
                        theme: 'default',
                        dropdownParent: $('body')
                    });

                    cekJumlahTerakir();
                }
            });
        }

        function validasi(obj) {
            $(obj).prop('disabled', true);
            let item_id = $('#item_id').val();
            let warehouse_id = $('#warehouse_id').val();
            let jumlah_adjustment = $('#jumlah_adjustment').val();
            let kategori_adjustment = $('#kategori_adjustment').val();
            let alasan_adjustment = $('#alasan_adjustment').val();

            if (item_id == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Item wajib diisi!',
                });
                $(obj).prop('disabled', false);
                return false;
            }

            if (warehouse_id == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Lokasi wajib diisi!',
                });
                $(obj).prop('disabled', false);
                return false;
            }

            if (jumlah_adjustment == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Jumlah Adjustment wajib diisi!',
                });
                $(obj).prop('disabled', false);
                return false;
            }

            if (kategori_adjustment == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Kategori Adjustment wajib diisi!',
                });
                $(obj).prop('disabled', false);
                return false;
            }

            if (alasan_adjustment == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Alasan Adjustment wajib diisi!',
                });
                $(obj).prop('disabled', false);
                return false;
            }

            $('#formSave').submit();
        }
    </script>
@endpush

