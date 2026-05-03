@extends('layouts.adm.base')
@section('title', __('Riwayat Transaksi'))

@push('style')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('admin_assets') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ asset('admin_assets') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{ asset('admin_assets') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form id="filter-form" method="GET" action="{{ route('admin.riwayat.index') }}">
            <div class="row">
                <div class="col-md-6">
                    <strong>Type:</strong>
                    <select name="type" id="type" class="form-control">
                        <option value="">Please Select Type!</option>
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Type Transaksi</option>
                        <option value="instan" {{ request('type') == 'instan' ? 'selected' : '' }}>Instan</option>
                        <option value="katering" {{ request('type') == 'katering' ? 'selected' : '' }}>Katering</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <strong>Tanggal:</strong>
                    <input type="date" class="form-control" name="tgl" id="tgl" value="{{ request('tgl') }}">
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <br>
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Nama Customer</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['data'] as $dt)
                <tr>
                    <td>{{ $dt->kode_transaksi }}</td>
                    <td>{{ $dt->type }}</td>
                    <td>{{ $dt->customer->fullname }}</td>
                    <td>{{ $dt->created_at }}</td>
                    <td>{{ __('Rp.').number_format($dt->total_harga, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Total Instan</th>
                    <th style="text-align: left">{{ $typeinstan }}</th>
                </tr>
                <tr>
                    <th colspan="4">Total Katering</th>
                    <th style="text-align: left">{{ $typekatering }}</th>
                </tr>
                <tr>
                    <th colspan="4">Total Transaksi</th>
                    <th style="text-align: left">{{ __('Rp.').number_format($total, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
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

<script>
$(function () {
    $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["csv", "excel", "pdf", "print", "colvis"],
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();

            // Total over all pages
            var total = api
                .column(4, { page: 'all' })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a.replace(/[^\d.-]/g, '')) + parseFloat(b.replace(/[^\d.-]/g, ''));
                }, 0);

            // Update footer
            $(api.column(4).footer()).html('Rp.' + total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
</script>
@endpush
