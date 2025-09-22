@extends('layouts.adm.base')
@section('title', 'Daftar Resep')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">Data Resep</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('recipes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Resep
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No. Resep</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Jumlah Bahan</th>
                                    <th>Yield</th>
                                    <th>Total Biaya</th>
                                    <th style="width: 150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $recipe)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $recipe->recipe_number }}</td>
                                    <td>{{ $recipe->name }}</td>
                                    <td>{{ $recipe->category->name ?? '-' }}</td>
                                    <td>{{ $recipe->details->count() }}</td>
                                    <td>{{ $recipe->yield_quantity }} {{ $recipe->yield_unit }}</td>
                                    <td>Rp {{ number_format($recipe->total_cost, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('recipes.show', $recipe->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('recipes.edit', $recipe->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $recipe->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
@foreach($data as $recipe)
<form id="delete-form-{{ $recipe->id }}" action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>
@endforeach
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(function() {
    $('#example1').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = $('#delete-form-' + id);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.status === 200) {
                        Swal.fire('Berhasil!', response.message, 'success')
                            .then(() => {
                                window.location.reload();
                            });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                }
            });
        }
    });
}
</script>
@endpush
