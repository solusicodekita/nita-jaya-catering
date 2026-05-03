@extends('layouts.adm.base')
@section('title', trans('menu.transaction.title'))

@push('style')

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('admin_assets') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('admin_assets') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('admin_assets') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

@endpush

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ trans('menu.transaction.title').' '.$title }}</h3>
            {{-- <div class="card-tools">
                <a href="{{ route('admin.transactions.create') }}" class="btn btn-success btn-sm">{{ trans('global.add')." ".trans('menu.transaction.title') }}</a>
            </div> --}}
        </div>
        <!-- /.card-header -->
        <div class="card-body" style="overflow-x:auto;">
        <table id="example1" class="table table-bordered table-striped" style="width: 1000px;">
                <thead>
                    <tr>
                        <th style="min-width: 150px;">Code</th>
                        <th style="min-width: 150px;">Customer</th>
                        <th style="min-width: 150px;">Order by</th>
                        <th style="min-width: 150px;">Total Harga</th>
                        <th style="min-width: 150px;">Bukti Pembayaran</th>
                        <th style="min-width: 150px;">Alamat</th>
                        <th style="min-width: 150px;">Status</th>
                        <th style="min-width: 150px;">Tanggal</th>
                        <th style="min-width: 150px;">{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $dt)
                    <tr>
                        <td>{{ $dt->kode_transaksi }}</td>
                        <td>{{ $dt->customer->fullname }}</td>
                        <td>{{ strtoupper($dt->type) }}</td>
                        <td>{{ __('Rp.').number_format($dt->total_harga,2,',','.') }}</td>
                        <td>
                            <a href="{{ asset('storage/'. $dt->gambar) }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('storage/'. $dt->gambar) }}" class="img-fluid rounded-start w-25" alt="Thumbnail">
                            </a>
                        </td>
                        <!-- <td><img src="{{ asset('storage/'. $dt->gambar) }}"style="max-width: 100%; height: auto;"></td> -->
                        <td>{{ $dt->address->title }}</td>
                        <td>
                            @if ($dt->status == 'PENDING')
                                <a href="{{ route('admin.transactions.status', $dt->id) }}" class="btn btn-secondary">{{ $dt->status }}</a>
                            @elseif ($dt->status == 'PROSES')
                                <a href="{{ route('admin.transactions.status', $dt->id) }}" class="btn btn-warning">{{ $dt->status }}</a>
                            @elseif ($dt->status == 'SUCCESS')
                                <a href="{{ route('admin.transactions.status', $dt->id) }}" class="btn btn-success">{{ $dt->status }}</a>
                            @else
                                <!-- <a href="{{ route('admin.transactions.status', $dt->id) }}" class="btn btn-danger">{{ $dt->status }}</a> -->
                                <a href="#" class="btn btn-danger">{{ $dt->status }}</a>
                            @endif
                        </td>
                        <td>{{ $dt->updated_at ? $dt->updated_at : $dt->created_at }}</td>
                        <td class="text-center">
                            <form action="{{ route('admin.transactions.destroy', $dt->id) }}" class="row" method="POST">
                                @method('DELETE')
                                @csrf
                                <div class="col-md-3">
                                    <a class="btn btn-info btn-sm" href="{{ route('admin.transactions.show', $dt->id) }}">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a class="btn btn-success btn-sm" href="{{ route('admin.order_products.show', $dt->id) }}">
                                        <i class="fas fa-clipboard-list"></i>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                @if ($dt->status != 'CANCEL' && $dt->payment_status != 4)
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                                </div>

                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')

    <!-- DataTables  & Plugins -->
    <script src="{{ asset('admin_assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/jszip/jszip.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ asset('admin_assets') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- Page specific script -->
    <script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["csv", "excel", "pdf", "print", "colvis"],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
    </script>

@endpush
