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
                
                <p style="font-size: 1.05rem;" class="mb-3">
                    Pilih harga dan paket untuk reservasi ruang rapat.
                </p>
                <table id="example1" class="table table-bordered"> <thead>
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
                                <td>{!! nl2br(e($paket->isi_paket)) !!}</td>
                                <td>{!! nl2br(e($paket->fasilitas)) !!}</td>
                                <td>{{ number_format($paket->harga, 0, ',', '.') }}</td>
                                
                                <td class="text-center">
                                    <a href="{{ route('ruangrapat.edit', $paket->id) }}" class="btn btn-warning btn-sm me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('ruangrapat.destroy', $paket->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus paket ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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

<style>
    /* Style header coklat soft */
    #example1 thead th {
        background-color: #C49A6C !important;
        color: #50200C !important;
        border-color: #DED4C8 !important;
    }

    /* Spasi (padding) di header dan body tabel */
    #example1 tbody td,
    #example1 thead th {
        padding: 12px 10px !important;
        vertical-align: middle;
    }

    /* Atur lebar kolom Aksi */
    #example1 td:last-child {
        width: 120px;
    }
</style>

@endsection
