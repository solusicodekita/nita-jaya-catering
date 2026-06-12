@extends('layouts.adm.base')
@section('title', 'Edit Pesanan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex align-items-center">
            <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-outline-secondary rounded-circle me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="m-0 fw-bold text-primary">Edit Pesanan: {{ $pesanan->order_number }}</h2>
        </div>
    </div>

    <form action="{{ route('admin.pesanan.update', $pesanan->id) }}" method="POST" id="formPesanan">
        @csrf
        <div class="row">
            <!-- Informasi Pesanan -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="fas fa-info-circle text-primary me-2"></i> Info Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Pesanan</label>
                            <input type="text" class="form-control bg-light" name="order_number" value="{{ $pesanan->order_number }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Klien / Pemesan</label>
                            <input type="text" class="form-control" name="customer_name" value="{{ $pesanan->customer_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Acara</label>
                            <input type="date" class="form-control" name="event_date" value="{{ date('Y-m-d', strtotime($pesanan->event_date)) }}" required>
                        </div>

                        <hr>
                        <div class="bg-light p-3 rounded mt-4 border border-info border-start-0 border-end-0 border-bottom-0 border-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Estimasi HPP:</span>
                                <span class="fw-bold text-danger" id="displayTotalHpp">Rp {{ number_format($pesanan->total_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Estimasi Jual:</span>
                                <span class="fw-bold text-success fs-5" id="displayTotalJual">Rp {{ number_format($pesanan->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small>Menyimpan perubahan akan mengkalkulasi ulang dan memperbarui riwayat stok gudang Anda.</small>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 mt-2 fw-bold shadow" id="btnSubmitPesanan">
                            <i class="fas fa-save me-2"></i> Update Pesanan & Sesuaikan Stok
                        </button>
                    </div>
                </div>
            </div>

            <!-- Detail Resep -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="fas fa-utensils text-primary me-2"></i> Pilih Resep / Menu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end bg-light p-3 rounded mb-4">
                            <div class="col-md-7 mb-2 mb-md-0">
                                <label class="form-label fw-bold">Pilih Resep Tambahan</label>
                                <select id="selectResep" class="form-select select2-menu">
                                    <option value="">-- Pilih Resep --</option>
                                    @foreach($menus as $menu)
                                        <option value="{{ $menu->id }}" 
                                                data-hpp="{{ $menu->total_cost ?? 0 }}" 
                                                data-jual="{{ $menu->selling_price ?? 0 }}">
                                            [{{ $menu->recipe_number }}] {{ $menu->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="form-label fw-bold">Jumlah Porsi</label>
                                <input type="number" class="form-control" id="inputPorsi" min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success w-100 fw-bold" id="btnAddResep">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tablePesanan">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Nama Resep</th>
                                        <th width="15%" class="text-center">Porsi</th>
                                        <th width="20%" class="text-end">Sub HPP</th>
                                        <th width="20%" class="text-end">Sub Jual</th>
                                        <th width="5%" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pesanan->details as $detail)
                                    <tr class="item-row" id="row-{{ $detail->menu_id }}">
                                        <td>
                                            <div class="fw-bold text-dark">{{ $detail->menu->name ?? 'Resep' }}</div>
                                            <input type="hidden" name="menu_id[]" value="{{ $detail->menu_id }}">
                                        </td>
                                        <td class="text-center">
                                            <span class="display-porsi badge bg-primary px-3 py-2 fs-6">{{ $detail->qty_porsi }}</span>
                                            <input type="hidden" name="qty_porsi[]" class="input-porsi" value="{{ $detail->qty_porsi }}">
                                        </td>
                                        <td class="text-end text-muted">
                                            <span class="display-sub-hpp">-</span>
                                            <input type="hidden" name="subtotal_cost[]" class="input-sub-hpp" value="{{ $detail->subtotal_cost }}">
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            <span class="display-sub-jual">-</span>
                                            <input type="hidden" name="subtotal_price[]" class="input-sub-jual" value="{{ $detail->subtotal_price }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove rounded-circle">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    <tr id="emptyRow" style="display: {{ $pesanan->details->count() > 0 ? 'none' : 'table-row' }};">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fs-2 mb-2 opacity-50 d-block"></i>
                                            Belum ada resep yang ditambahkan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-menu').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        function calculateTotal() {
            let totalHpp = 0;
            let totalJual = 0;

            $('#tablePesanan tbody tr.item-row').each(function() {
                totalHpp += parseFloat($(this).find('.input-sub-hpp').val()) || 0;
                totalJual += parseFloat($(this).find('.input-sub-jual').val()) || 0;
            });

            $('#displayTotalHpp').text(formatRupiah(totalHpp));
            $('#displayTotalJual').text(formatRupiah(totalJual));

            if ($('#tablePesanan tbody tr.item-row').length > 0) {
                $('#emptyRow').hide();
                $('#btnSubmitPesanan').prop('disabled', false);
            } else {
                $('#emptyRow').show();
                $('#btnSubmitPesanan').prop('disabled', true);
            }
        }
        
        // Initial Calculation to format existing loaded data
        calculateTotal();

        $('#btnAddResep').click(function() {
            let select = $('#selectResep');
            let option = select.find('option:selected');
            let menuId = select.val();
            let menuName = option.text();
            let porsi = parseInt($('#inputPorsi').val());

            if (!menuId) {
                Swal.fire('Peringatan', 'Silakan pilih resep terlebih dahulu', 'warning');
                return;
            }

            if (porsi < 1 || isNaN(porsi)) {
                Swal.fire('Peringatan', 'Jumlah porsi tidak valid', 'warning');
                return;
            }

            let baseHpp = parseFloat(option.data('hpp')) || 0;
            let baseJual = parseFloat(option.data('jual')) || 0;
            
            let subHpp = baseHpp * porsi;
            let subJual = baseJual * porsi;

            let existingRow = $(`#row-${menuId}`);
            if (existingRow.length > 0) {
                let oldPorsi = parseInt(existingRow.find('.input-porsi').val());
                let newPorsi = oldPorsi + porsi;
                existingRow.find('.input-porsi').val(newPorsi);
                existingRow.find('.display-porsi').text(newPorsi);
                
                // Add sub totals
                let oldSubHpp = parseFloat(existingRow.find('.input-sub-hpp').val()) || 0;
                let oldSubJual = parseFloat(existingRow.find('.input-sub-jual').val()) || 0;
                existingRow.find('.input-sub-hpp').val(oldSubHpp + subHpp);
                existingRow.find('.input-sub-jual').val(oldSubJual + subJual);
            } else {
                let tr = `
                    <tr class="item-row" id="row-${menuId}">
                        <td>
                            <div class="fw-bold text-dark">${menuName}</div>
                            <input type="hidden" name="menu_id[]" value="${menuId}">
                        </td>
                        <td class="text-center">
                            <span class="display-porsi badge bg-primary px-3 py-2 fs-6">${porsi}</span>
                            <input type="hidden" name="qty_porsi[]" class="input-porsi" value="${porsi}">
                        </td>
                        <td class="text-end text-muted">
                            <span class="display-sub-hpp">-</span>
                            <input type="hidden" name="subtotal_cost[]" class="input-sub-hpp" value="${subHpp}">
                        </td>
                        <td class="text-end fw-bold text-success">
                            <span class="display-sub-jual">-</span>
                            <input type="hidden" name="subtotal_price[]" class="input-sub-jual" value="${subJual}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove rounded-circle">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#tablePesanan tbody').append(tr);
            }

            calculateTotal();
            select.val('').trigger('change');
            $('#inputPorsi').val(1);
        });

        $(document).on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $('#formPesanan').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: "Stok akan dikalkulasi ulang berdasarkan data yang baru. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update Pesanan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
