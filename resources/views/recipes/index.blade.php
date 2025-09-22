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
                        <table class="table table-bordered table-striped" id="recipes-table">
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
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

$(document).ready(function() {
    $('#recipes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('recipes.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'recipe_number', name: 'recipe_number' },
            { data: 'name', name: 'name' },
            { data: 'category.name', name: 'category.name', defaultContent: '-' },
            { data: 'details_count', name: 'details_count' },
            { 
                data: null,
                render: function(data) {
                    return data.yield_quantity + ' ' + data.yield_unit;
                }
            },
            { 
                data: 'total_cost',
                render: function(data) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(data);
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script>
@endpush