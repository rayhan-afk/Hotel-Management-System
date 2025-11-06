{{-- resources/views/ruangrapat/index.blade.php --}}

@extends('template.master')

@section('title', 'Manajemen Paket Ruang Rapat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-briefcase me-2"></i> Daftar Paket Ruang Rapat
                </h3>
                <div class="card-tools">
                    <a href="{{ route('ruangrapat.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i> Tambah Paket Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Paket</th>
                            <th>Isi Paket</th>
                            <th>Fasilitas</th>
                            <th>Harga (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pakets as $key => $paket)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $paket->name }}</td>
                                <td>{!! nl2br(e($paket->isi_paket)) !!}</td> <td>{!! nl2br(e($paket->fasilitas)) !!}</td> <td>{{ number_format($paket->harga, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('ruangrapat.destroy', $paket->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus paket ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('ruangrapat.edit', $paket->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada paket yang ditambahkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush