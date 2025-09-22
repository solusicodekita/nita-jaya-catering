@extends('layouts.adm.base')
@section('title', 'Create Stock Opname')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Form Tambah Stok Opname Awal</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('stock.index') }}" class="btn btn-primary"><i
                                            class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="formStock" method="POST" action="{{ route('stock.store') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <table class="table table-bordered" id="tableStock">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Lokasi</th>
                                            <th>Stock Awal</th>
                                            <th>Stock Akhir</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="trTransaksi">
                                        <tr>
                                            <td>
                                                <select class="form-control ubahSelect select2-item item_id" name="item[1][item_id]" onchange="cekStokAkhir(this)">
                                                    <option value="">-- Pilih Item --</option>
                                                    @foreach ($item as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control ubahSelect select2-warehouse warehouse_id" name="item[1][warehouse_id]" onchange="cekStokAkhir(this)">
                                                    <option value="">-- Pilih Lokasi Item --</option>
                                                    @foreach ($warehouse as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control desimal initial_stock" name="item[1][initial_stock]"
                                                    id="initial_stock" readonly value="0" autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control desimal final_stock" name="item[1][final_stock]"
                                                    id="final_stock" placeholder="Ketikkan Stock Akhir" autocomplete="off">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary" id="btnTambah" onclick="addItem(this)">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary" id="btnSimpan" onclick="validasi()">
                                    <i class="fa fa-save"></i> Simpan
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
            $('.ubahSelect').select2({
                placeholder: '-- Pilih Item --',
                allowClear: false
            });
        });

        // Fungsi untuk memvalidasi input hanya menerima angka desimal
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

        function validasi() {
            let isValid = true;
            
            $('tr').each(function() {
                let item_id = $(this).find('.item_id').val();
                let warehouse_id = $(this).find('.warehouse_id').val(); 
                let initial_stock = $(this).find('.initial_stock').val();
                let final_stock = $(this).find('.final_stock').val();

                if (item_id == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Item wajib diisi!',
                    });
                    isValid = false;
                    return false;
                }

                if (warehouse_id == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Lokasi wajib diisi!',
                    });
                    isValid = false;
                    return false;
                }

                if (initial_stock == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Stok Awal wajib diisi!',
                    });
                    isValid = false;
                    return false;
                }

                if (final_stock == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Stok Akhir wajib diisi!',
                    });
                    isValid = false;
                    return false;
                }
            });

            if(isValid) {
                $('#formStock').submit();
            }
        }

        function checkDuplicateItems() {
            let isDuplicate = false;
            let items = [];
            
            $('tr').each(function() {
                let item_id = $(this).find('.item_id').val();
                let warehouse_id = $(this).find('.warehouse_id').val();
                
                if(item_id && warehouse_id) {
                    let combination = item_id + '-' + warehouse_id;
                    
                    if(items.includes(combination)) {
                        isDuplicate = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplikasi Data',
                            text: 'Item dan lokasi tidak boleh sama dalam satu transaksi!',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        
                        $(this).find('.item_id').val('').trigger('change');
                        $(this).find('.warehouse_id').val('').trigger('change');
                        $(this).find('.initial_stock').val('0');
                        return false;
                    }
                    
                    items.push(combination);
                }
            });
            
            return !isDuplicate;
        }

        function cekStokAkhir(obj) {
            let item_id = $(obj).parents('tr').find('.item_id').val();
            let warehouse_id = $(obj).parents('tr').find('.warehouse_id').val();

            if (!checkDuplicateItems()) {
                return;
            }

            if (item_id != '' && warehouse_id != '') {
                $.ajax({
                    url: "{{ route('stock.cekStokAkhir') }}",
                    type: 'GET',
                    data: {
                        item_id: item_id,
                        warehouse_id: warehouse_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.status == 1) {
                            let stokAkhir = parseFloat(response.stokAkhir);
                            if (stokAkhir % 1 === 0) {
                                $(obj).parents('tr').find('.initial_stock').val(parseInt(stokAkhir));
                            } else {
                                $(obj).parents('tr').find('.initial_stock').val(stokAkhir);
                            }
                        } else if (response.status == 2) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.msg,
                                timer: 3000,
                                showConfirmButton: false
                            });
                            $(obj).parents('tr').find('.initial_stock').val('0');
                            $(obj).parents('tr').find('.item_id').val('').trigger('change');
                            $(obj).parents('tr').find('.warehouse_id').val('').trigger('change');
                        }
                    }
                });
            }
        }

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

        function addItem(obj) {
            let no = $('#trTransaksi > tr').length + 1;
            let tr = `@include('admin.stock.trCreate', ['no' => '${no}', 'item' => $item, 'warehouse' => $warehouse])`;
            $(obj).parents('table').find('#trTransaksi').append(tr);

            setTimeout(function() {
                initializeSelect2();
            }, 100);
        }

        function deleteItem(obj) {
            $(obj).parents('tr').remove();

            setTimeout(function() {
                initializeSelect2();
            }, 100);
        }
    </script>
@endpush
