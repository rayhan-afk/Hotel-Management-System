@extends('template.master')
@section('title', 'Edit Data Amenity')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header" style="background-color: #f7f3e8;">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Data Amenity</h5>
                </div>
                <form action="{{ route('amenity.update', $amenity->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Penting untuk method update --}}
                    
                    <div class="card-body">
                        
                        {{-- Nama Barang --}}
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                                   id="nama_barang" name="nama_barang" 
                                   value="{{ old('nama_barang', $amenity->nama_barang) }}" required>
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Satuan --}}
                            <div class="col-md-6 mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <input type="text" class="form-control @error('satuan') is-invalid @enderror" 
                                       id="satuan" name="satuan" 
                                       value="{{ old('satuan', $amenity->satuan) }}" placeholder="cth: pcs, botol, set" required>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Stok --}}
                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label">Stok Saat Ini</label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror" 
                                       id="stok" name="stok" 
                                       value="{{ old('stok', $amenity->stok) }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $amenity->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="card-footer border-0" style="background-color: #f7f3e8;">
                        <button type="submit" class="btn btn-hotel-primary">
                            <i class="fas fa-save me-1"></i> Update
                        </button>
                        <a href="{{ route('amenity.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection