@extends('layouts.adm.base')
@section('title', 'Mutasi Stok')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title text-white"><i class="fas fa-exchange-alt me-2"></i> Form Mutasi Stok Antar Gudang</h3>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.mutasi_stok.store') }}" method="POST" id="mutasiForm">
                                @csrf
                                
                                <div class="row bg-light p-3 rounded mb-4 border">
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label fw-bold">Dari Gudang Asal <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="from_warehouse_id" id="from_warehouse_id" required>
                                            <option value="" disabled selected>-- Pilih Gudang Asal --</option>
                                            @foreach($warehouses as $wh)
                                                <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Gudang asal seluruh item di bawah.</small>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label fw-bold">Ke Gudang Tujuan <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="to_warehouse_id" id="to_warehouse_id" required>
                                            <option value="" disabled selected>-- Pilih Gudang Tujuan --</option>
                                            @foreach($warehouses as $wh)
                                                <option value="{{ $wh->id }}" {{ $wh->code == 'DP' ? 'selected' : '' }}>{{ $wh->name }} ({{ $wh->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dynamic_field">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="30%">Pilih Item / Barang</th>
                                                <th width="20%">Seting Konversi Dapur</th>
                                                <th width="20%">Input Menggunakan Satuan</th>
                                                <th width="20%">Jumlah Dipindah</th>
                                                <th width="10%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Rows akan digenerate via Javascript -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mb-4">
                                    <button type="button" name="add" id="add" class="btn btn-success fw-bold" disabled><i class="fas fa-plus"></i> Tambah Baris Barang</button>
                                    <small class="text-muted ms-2" id="add_hint">Pilih Gudang Asal terlebih dahulu.</small>
                                </div>

                                <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                                    <a href="{{ route('admin.mutasi_stok.index') }}" class="btn btn-secondary btn-lg">Kembali</a>
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold" id="btnSubmit" disabled><i class="fas fa-paper-plane me-2"></i> Proses Mutasi</button>
                                </div>
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
        $('.select2').select2({ theme: 'bootstrap-5' });

        let i = 0;
        let itemOptionsHtml = '<option value="" disabled selected>-- Pilih Item --</option>';
        let warehouseId = null;

        // Reset Table and Reload Items when Warehouse Changes
        $('#from_warehouse_id').change(function() {
            warehouseId = $(this).val();
            let to_warehouse = $('#to_warehouse_id').val();
            
            if(warehouseId === to_warehouse) {
                Swal.fire('Peringatan', 'Gudang asal dan tujuan tidak boleh sama.', 'warning');
                $(this).val('').trigger('change');
                return;
            }

            // Lock UI while loading
            $('#add').prop('disabled', true);
            $('#add_hint').text('Memuat daftar barang...');
            $('#dynamic_field tbody').empty();
            i = 0;
            checkSubmitButton();

            $.ajax({
                url: "{{ route('admin.mutasi_stok.get_items') }}",
                type: "GET",
                data: { warehouse_id: warehouseId },
                success: function(res) {
                    itemOptionsHtml = res;
                    $('#add').prop('disabled', false);
                    $('#add_hint').text('Silakan tambahkan item mutasi.');
                    
                    // Add first row automatically
                    addRow();
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memuat data item', 'error');
                }
            });
        });

        $('#to_warehouse_id').change(function() {
            let to_warehouse = $(this).val();
            let from_warehouse = $('#from_warehouse_id').val();
            if(to_warehouse === from_warehouse && to_warehouse != null) {
                Swal.fire('Peringatan', 'Gudang asal dan tujuan tidak boleh sama.', 'warning');
                $(this).val('').trigger('change');
            }
        });

        function checkSubmitButton() {
            let rowCount = $('#dynamic_field tbody tr').length;
            if (rowCount > 0 && $('#from_warehouse_id').val() != null) {
                $('#btnSubmit').prop('disabled', false);
            } else {
                $('#btnSubmit').prop('disabled', true);
            }
        }

        window.updateRowLabels = function(element) {
            let row = $(element).closest('tr');
            let retailUnit = row.find('.retail-unit-input').val() || '';
            let retailConv = parseFloat(row.find('.retail-conv-input').val()) || 1;
            let unit = row.find('.main-unit-val').val() || '-';

            // update the conversion rate hidden input for validations
            let validConv = (!isNaN(retailConv) && retailConv > 1) ? retailConv : 1;
            row.find('.conv-rate-val').val(validConv);

            let unitSelect = row.find('.unit-type-select');
            let currentVal = unitSelect.val();
            unitSelect.empty();
            
            unitSelect.append(`<option value="main" class="opt-main">Satuan Gudang (${unit})</option>`);
            
            if (retailUnit.trim() !== '' && !isNaN(retailConv) && retailConv > 1) {
                unitSelect.append(`<option value="retail" class="opt-retail">Satuan Dapur (${retailUnit})</option>`);
            }

            // Restore selection if possible
            if (unitSelect.find(`option[value="${currentVal}"]`).length > 0) {
                unitSelect.val(currentVal);
            } else {
                unitSelect.val('main');
            }

            // Trigger unit change to recalculate constraints
            unitSelect.trigger('change');
        };

        function addRow() {
            i++;
            let rowId = 'row' + i;
            let html = `
                <tr id="${rowId}" class="item-row">
                    <td>
                        <select name="items[${i}][item_id]" class="form-control select2 item-select" required>
                            ${itemOptionsHtml}
                        </select>
                        <small class="stock-hint text-info mt-1 d-block fw-bold"></small>
                        <input type="hidden" class="max-stock-val" value="0">
                        <input type="hidden" class="conv-rate-val" value="1">
                        <input type="hidden" class="main-unit-val" value="-">
                    </td>
                    <td class="bg-light">
                        <div class="row g-1 align-items-center">
                            <div class="col-5">
                                <input type="text" name="items[${i}][retail_unit]" class="form-control form-control-sm retail-unit-input" placeholder="Gram/Pcs" oninput="updateRowLabels(this)">
                            </div>
                            <div class="col-2 text-center text-muted fw-bold p-0">=</div>
                            <div class="col-5">
                                <input type="number" step="0.01" name="items[${i}][retail_conversion]" class="form-control form-control-sm retail-conv-input" placeholder="Isi/Berat" oninput="updateRowLabels(this)">
                            </div>
                        </div>
                    </td>
                    <td>
                        <select name="items[${i}][unit_type]" class="form-control unit-type-select" required>
                            <option value="main" class="opt-main">Satuan Gudang</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" step="0.0001" min="0.0001" name="items[${i}][quantity]" class="form-control qty-input" required placeholder="0">
                            <span class="input-group-text unit-addon fw-bold" style="width: 70px; justify-content: center;">-</span>
                        </div>
                        <small class="text-danger error-hint mt-1 d-block"></small>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" name="remove" id="${rowId}" class="btn btn-danger btn_remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#dynamic_field tbody').append(html);
            $('#' + rowId + ' .select2').select2({ theme: 'bootstrap-5' });
            checkSubmitButton();
        }

        $('#add').click(function() {
            addRow();
        });

        $(document).on('click', '.btn_remove', function() {
            let button_id = $(this).attr("id");
            $('#' + button_id).remove();
            checkSubmitButton();
        });

        // When Item is Selected
        $(document).on('change', '.item-select', function() {
            let row = $(this).closest('tr');
            let item_id = $(this).val();
            let wh_id = $('#from_warehouse_id').val();
            
            // Get Option Data
            let selectedOption = $(this).find('option:selected');
            let unit = selectedOption.attr('data-unit') || '-';
            let retail = selectedOption.attr('data-retail') || '';
            let conv = parseFloat(selectedOption.attr('data-conv')) || 1;

            // Setup UI states
            row.find('.main-unit-val').val(unit);
            row.find('.retail-unit-input').val(retail);
            row.find('.retail-conv-input').val(conv > 1 ? conv : '');
            
            window.updateRowLabels(row.find('.retail-unit-input')[0]);

            // Clear quantity
            row.find('.qty-input').val('');
            
            // Fetch live stock
            row.find('.stock-hint').text('Mengecek stok...');
            $.ajax({
                url: "{{ route('admin.mutasi_stok.check_stock') }}",
                type: "GET",
                data: { item_id: item_id, warehouse_id: wh_id },
                success: function(res) {
                    let stock = parseFloat(res.stokAkhir);
                    row.find('.max-stock-val').val(stock);
                    
                    if (stock <= 0) {
                        row.find('.stock-hint').removeClass('text-info text-success').addClass('text-danger')
                            .text('Stok kosong (0 ' + unit + ')');
                        row.find('.qty-input').attr('max', 0);
                        Swal.fire('Perhatian', 'Stok kosong di gudang ini!', 'warning');
                    } else {
                        row.find('.stock-hint').removeClass('text-info text-danger').addClass('text-success')
                            .text('Sisa Stok: ' + stock + ' ' + unit);
                        // Trigger unit change to set max constraint correctly
                        unitSelect.trigger('change');
                    }
                }
            });
        });

        // When Unit Type changes
        $(document).on('change', '.unit-type-select', function() {
            let row = $(this).closest('tr');
            let type = $(this).val();
            
            let selectedOption = row.find('.item-select option:selected');
            if(!selectedOption.val()) return; // Item not selected yet

            let unit = row.find('.main-unit-val').val() || '-';
            let retail = row.find('.retail-unit-input').val() || '-';
            let conv = parseFloat(row.find('.conv-rate-val').val());
            let maxStockMain = parseFloat(row.find('.max-stock-val').val());

            row.find('.qty-input').val(''); // reset
            row.find('.error-hint').text(''); // clear error

            if(type === 'retail') {
                row.find('.unit-addon').text(retail);
                row.find('.qty-input').attr('max', maxStockMain * conv);
            } else {
                row.find('.unit-addon').text(unit);
                row.find('.qty-input').attr('max', maxStockMain);
            }
        });

        // Live quantity validation
        $(document).on('keyup change', '.qty-input', function() {
            let max = parseFloat($(this).attr('max'));
            let val = parseFloat($(this).val());
            let row = $(this).closest('tr');
            
            if(val > max) {
                row.find('.error-hint').text('Melebihi batas stok!');
                $(this).val(max);
            } else {
                row.find('.error-hint').text('');
            }
        });

        // Form Submit Validation
        $('#mutasiForm').submit(function(e) {
            let rowCount = $('#dynamic_field tbody tr').length;
            if(rowCount === 0) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Minimal harus ada 1 barang yang dimutasi.', 'warning');
            }
        });
    });
</script>
@endpush
