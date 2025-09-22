@extends('layouts.adm.base')
@section('title', 'Create Stock Opname')

@push('styles')
<style>
    /* Custom Select2 Styles */
    .select2-container {
        width: 100% !important;
    }
    
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        padding: 6px 12px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        padding-left: 0 !important;
        color: #495057 !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 10px !important;
    }
    
    .select2-dropdown {
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
    }
    
    .select2-container--default .select2-selection--single:focus {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff !important;
        color: white !important;
    }
    
    .table td {
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Form Tambah Stok In</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('in_stock.index') }}" class="btn btn-primary"><i
                                            class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 table-responsive">
                            <form id="formStockIn" action="{{ route('in_stock.store') }}" method="post">
                                @csrf
                                <table id="tabelStock" class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Item</th>
                                            <th class="text-center">Lokasi Item</th>
                                            <th class="text-center">Harga Satuan</th>
                                            <th class="text-center" style="width: 10%;">Jumlah Satuan</th>
                                            <th class="text-center" style="width: 10%;">Unit</th>
                                            <th class="text-center">Total Harga Item</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="trTransaksi">
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <select class="form-control item_id select2-item" name="item[1][item_id]"
                                                    onchange="getHargaSatuan(this)">
                                                    <option value="" disabled selected>-- Pilih Item --</option>
                                                    @foreach ($item as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control warehouse_id select2-warehouse"
                                                    name="item[1][warehouse_id]"></select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control harga_satuan ribuan"
                                                    name="item[1][harga_satuan]" id="harga_satuan"
                                                    onblur="totalHargaItem(this)" value="0">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control quantity" name="item[1][quantity]"
                                                    id="quantity" onblur="totalHargaItem(this)" placeholder="..."
                                                    autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control unit" name="item[1][unit]" id="unit" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="item[1][total_harga_item]" id="total_harga_item"
                                                    value="0" class="form-control total_harga_item" value="0"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="item[1][description]"
                                                    id="description" placeholder="Ketikkan Keterangan" autocomplete="off">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="addItem(this)"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody id="trTotal">
                                        <tr>
                                            <td colspan="6" style="text-align: right;vertical-align: middle;">Total</td>
                                            <td>
                                                <input type="text" class="form-control" name="total_harga_keseluruhan"
                                                    id="total_harga_keseluruhan" value="0" readonly>
                                            </td>
                                            <td colspan="2" style="text-align: left;vertical-align: middle;">
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="simpanTransaksi(this)"><i class="fas fa-save"></i> Simpan</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
            initializeSelect2();

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

            $('.ribuan').on('keyup', function() {
                var val = $(this).val();
                $(this).val(formatRupiah(val));
            })
        });

        function initializeSelect2() {
            try {
                $('.select2-item').select2('destroy');
                $('.select2-warehouse').select2('destroy');
            } catch (e) {
                // kosong
            }
            
            $('.select2-item').select2({
                placeholder: "-- Pilih Item --",
                allowClear: false,
                width: '100%',
                theme: 'default',
                dropdownParent: $('body')
            });

            $('.select2-warehouse').select2({
                placeholder: "-- Pilih Lokasi --",
                allowClear: false,
                width: '100%',
                theme: 'default',
                dropdownParent: $('body')
            });
        }

        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        function getHargaSatuan(obj) {
            let item_id = $(obj).val();
            
            let isItemSelected = false;
            $('.item_id').not(obj).each(function() {
                if ($(this).val() == item_id) {
                    isItemSelected = true;
                    return false;
                }
            });

            if (isItemSelected) {
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!', 
                    text: 'Item ini sudah dipilih pada baris lain!'
                });
                $(obj).val('').trigger('change');
                let $row = $(obj).parents('tr');
                $row.find('.harga_satuan').val('0');
                $row.find('.warehouse_id').empty().trigger('change');
                $row.find('.unit').val('');
                $row.find('.total_harga_item').val('0');
                $row.find('.live_stok').val('0');
                $row.find('.description').val('');
                $row.find('.quantity').val('');
                return false;
            }

            $.ajax({
                url: "{{ route('in_stock.getHargaSatuan') }}",
                type: "GET",
                data: {
                    item_id: item_id
                },
                success: function(response) {
                    let harga_satuan = parseFloat(response.harga_satuan);
                    if (Number.isInteger(harga_satuan)) {
                        $(obj).parents('tr').find('.harga_satuan').val(harga_satuan.toLocaleString('id-ID'));
                    } else {
                        $(obj).parents('tr').find('.harga_satuan').val(harga_satuan.toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                    }
                    $(obj).parents('tr').find('.unit').val(response.unit);
                    totalHargaItem(obj);
                    getWarehouse(obj)
                }
            });
        }

        function totalHargaItem(obj) {
            let harga_satuan = $(obj).parents('tr').find('.harga_satuan').val().replace(/\./g, '');
            harga_satuan = harga_satuan.replace(',', '.');
            let quantity = $(obj).parents('tr').find('.quantity').val();
            let total_harga = 0;
            if (harga_satuan != '' && quantity != '') {
                total_harga = harga_satuan * quantity;
                total_harga = parseFloat(total_harga);

                if (Number.isInteger(total_harga)) {
                    $(obj).parents('tr').find('.total_harga_item').val(total_harga.toLocaleString('id-ID'));
                } else {
                    $(obj).parents('tr').find('.total_harga_item').val(total_harga.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                }
            }
            totalKeseluruhan();
        }

        function addItem(obj) {
            let no = $('#trTransaksi > tr').length + 1;
            if (no > 10) {
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    text: 'Maksimal 10 baris per transaksi!',
                });
                return false;
            }
            let tr = `@include('admin.stock_in.trCreate', ['no' => '${no}', 'item' => $item])`;
            $(obj).parents('table').find('#trTransaksi').append(tr);

            setTimeout(function() {
                initializeSelect2();
            }, 100);
        }

        function deleteItem(obj) {
            $(obj).parents('tr').remove();
            totalKeseluruhan();

            setTimeout(function() {
                initializeSelect2();
            }, 100);
        }

        function totalKeseluruhan() {
            let total_keseluruhan = 0;
            $('.total_harga_item').each(function() {
                let total_harga_item = $(this).val().replace(/\./g, '');
                total_harga_item = total_harga_item.replace(',', '.');
                total_harga_item = parseFloat(total_harga_item);
                if (!isNaN(total_harga_item)) {
                    total_keseluruhan += total_harga_item;
                }
            });

            total_keseluruhan = parseFloat(total_keseluruhan);
            if (Number.isInteger(total_keseluruhan)) {
                $('#total_harga_keseluruhan').val(total_keseluruhan.toLocaleString('id-ID'));
            } else {
                $('#total_harga_keseluruhan').val(total_keseluruhan.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        }

        function simpanTransaksi(obj) {
            $(obj).prop('disabled', true);
            let isValid = true;
            $('.item_id').each(function() {
                if ($(this).val() == '' || $(this).val() == null) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Item tidak boleh kosong!',
                    });
                    isValid = false;
                    return false;
                }
            });

            if (!isValid) {
                return false;
            }

            $('.quantity').each(function() {
                if ($(this).val() == '' || $(this).val() == null || $(this).val() <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Jumlah satuan tidak boleh kosong atau kurang dari sama dengan 0!',
                    });
                    isValid = false;
                    return false;
                }
            });

            if (!isValid) {
                return false;
            }

            $('#formStockIn').submit();
        }

        function getWarehouse(obj) {
            var item_id = $(obj).val();
            $.ajax({
                url: "{{ route('in_stock.getWarehouse') }}",
                type: "GET",
                data: {
                    item_id: item_id
                },
                dataType: "json",
                success: function(response) {
                    var $warehouseSelect = $(obj).parents('tr').find('.warehouse_id');
                    
                    // Destroy existing Select2 instance
                    try {
                        $warehouseSelect.select2('destroy');
                    } catch (e) {
                        // Ignore if not initialized
                    }
                    
                    // Update HTML content
                    $warehouseSelect.html(response);
                    
                    // Re-initialize Select2 for the updated warehouse dropdown
                    $warehouseSelect.select2({
                        placeholder: "-- Pilih Lokasi --",
                        allowClear: false,
                        width: '100%',
                        theme: 'default',
                        dropdownParent: $('body')
                    });
                }
            });
        }
    </script>
@endpush
