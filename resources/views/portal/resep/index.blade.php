@extends('layouts.portal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Katalog Menu / Resep</h5>
                <a href="{{ route('portal.menus.create') }}" class="btn btn-sm btn-light">Tambah Menu Baru</a>
            </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover border">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pratinjau</th>
                                    <th>Nama Resepi</th>
                                    <th>Deskripsi Singkat</th>
                                    <th class="text-center">Status Promosi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reseps as $resep)
                                <tr class="align-middle">
                                    <td style="width: 100px;">
                                        @if($resep->image)
                                            <img src="{{ asset('storage/' . $resep->image) }}" width="80" height="60" class="rounded shadow-sm" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded text-center py-2 border small text-muted">No Image</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $resep->name }}</div>
                                        <div class="small text-muted">{{ $resep->category }}</div>
                                    </td>
                                    <td class="small text-muted">{{ Str::limit($resep->description, 60) }}</td>
                                    <td class="text-center">
                                        @if($resep->is_promoted)
                                            <span class="badge bg-success px-3 py-2">Rekomendasi</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">Reguler</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('portal.menus.edit', $resep->id) }}" class="btn btn-sm btn-primary text-white"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('portal.menus.destroy', $resep->id) }}" method="POST" onsubmit="return confirm('Hapus resepi ini?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada resepi. Klik "Tambah Resepi Baru" untuk memulai.</td>
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
@endsection
