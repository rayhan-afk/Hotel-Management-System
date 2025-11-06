{{-- resources/views/ruangrapat/create.blade.php --}}

@extends('template.master')

@section('title', 'Tambah Paket Ruang Rapat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus me-2"></i> Formulir Tambah Paket
                </h3>
            </div>
            <form action="{{ route('ruangrapat.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Paket (Cth: Paket 1, Paket Half Day)</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="isi_paket" class="form-label">Isi Paket (Satu baris per item)</label>
                        <textarea class="form-control @error('isi_paket') is-invalid @enderror" id="isi_paket" name="isi_paket" rows="4">{{ old('isi_paket') }}</textarea>
                        @error('isi_paket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="fasilitas" class="form-label">Fasilitas (Satu baris per item)</label>
                        <textarea class="form-control @error('fasilitas') is-invalid @enderror" id="fasilitas" name="fasilitas" rows="4">{{ old('fasilitas') }}</textarea>
                        @error('fasilitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga') }}" required>
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('ruangrapat.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection