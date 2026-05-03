@extends('layouts.portal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-concierge-bell me-2"></i> Kelola Layanan Katering</h5>
                <a href="{{ route('portal.services.create') }}" class="btn btn-light btn-sm fw-bold"><i class="fas fa-plus me-1"></i> Tambah Layanan</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th width="60">Foto</th>
                                <th>Nama Layanan</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $s)
                            <tr>
                                <td>
                                    @if($s->image)
                                        <img src="{{ asset('storage/'.$s->image) }}" width="60" height="40" class="rounded object-cover shadow-sm">
                                    @else
                                        <div class="bg-light rounded text-center py-2" style="width:60px"><i class="fas fa-image text-muted"></i></div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $s->title }}</td>
                                <td><small class="text-muted">{{ Str::limit($s->description, 80) }}</small></td>
                                <td>
                                    @if($s->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('portal.services.edit', $s->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('portal.services.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data layanan katering.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
