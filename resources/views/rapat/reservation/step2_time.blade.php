@extends('template.master')
@section('title', 'Pilih Waktu Reservasi')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
@endsection

@section('content')
    <div class="container mt-3">
        @include('rapat.reservation._progressbar') 
        
        <div class="row justify-content-md-center mt-4">
            <div class="col-md-8 mt-2">
                <div class="card shadow-sm border">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Langkah 2: Tentukan Waktu</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('rapat.reservation.storeStep2') }}">
                            @csrf
                            
                            {{-- Tanggal --}}
                            <div class="mb-3">
                                <label for="tanggal_pemakaian" class="form-label fw-bold">Tanggal Pemakaian</label>
                                <input type="date" class="form-control @error('tanggal_pemakaian') is-invalid @enderror" 
                                       id="tanggal_pemakaian" name="tanggal_pemakaian" 
                                       value="{{ old('tanggal_pemakaian', $timeInfo['tanggal_pemakaian'] ?? '') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('tanggal_pemakaian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Waktu Mulai --}}
                                <div class="col-md-4 mb-3">
                                    <label for="waktu_mulai" class="form-label fw-bold">Waktu Mulai</label>
                                    <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                           id="waktu_mulai" name="waktu_mulai" 
                                           value="{{ old('waktu_mulai', $timeInfo['waktu_mulai'] ?? '') }}" required>
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Waktu Selesai --}}
                                <div class="col-md-4 mb-3">
                                    <label for="waktu_selesai" class="form-label fw-bold">Waktu Selesai</label>
                                    <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                           id="waktu_selesai" name="waktu_selesai" 
                                           value="{{ old('waktu_selesai', $timeInfo['waktu_selesai'] ?? '') }}" required>
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- INPUT BARU: Durasi (Jam) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="durasi_jam" class="form-label fw-bold text-primary">Durasi Bayar (Jam)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control border-primary @error('durasi_jam') is-invalid @enderror" 
                                               id="durasi_jam" name="durasi_jam" 
                                               value="{{ old('durasi_jam', $timeInfo['durasi_jam'] ?? '1') }}" 
                                               min="1" max="24" required placeholder="Jam">
                                        <span class="input-group-text bg-primary text-white">Jam</span>
                                    </div>
                                    <div class="form-text text-muted small">
                                        *Hitungan sewa: Rp 100rb/jam.
                                    </div>
                                    @error('durasi_jam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Info Pemesan --}}
                            <div class="alert alert-light border mt-2">
                                <small class="text-muted d-block mb-1">Pemesan:</small>
                                <strong>{{ $customer['nama'] }}</strong> ({{ $customer['instansi'] ?? 'Perorangan' }})
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('rapat.reservation.showStep1') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Lanjut <i class="fas fa-arrow-right me-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection