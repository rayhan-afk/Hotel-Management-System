@extends('template.master')
@section('title', 'Reservasi Ruang Rapat - Waktu')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
@endsection

@section('content')
    <div class="container mt-3">
        @include('rapat.reservation._progressbar') <div class="row justify-content-md-center mt-4">
            
            <div class="col-md-8 mt-2">
                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Langkah 2: Waktu Reservasi</h5>
                            </div>
                            <div class="card-body">
                                <form class="row g-3" method="POST" action="{{ route('rapat.reservation.storeStep2') }}">
                                    @csrf
                                    <div class="col-md-12">
                                        <label for="tanggal_pemakaian" class="form-label">Tanggal Pemakaian</label>
                                        <input type="date" class="form-control @error('tanggal_pemakaian') is-invalid @enderror"
                                            id="tanggal_pemakaian" name="tanggal_pemakaian" value="{{ old('tanggal_pemakaian', $timeInfo['tanggal_pemakaian'] ?? now()->format('Y-m-d')) }}">
                                        @error('tanggal_pemakaian')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                                        <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror"
                                            id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', $timeInfo['waktu_mulai'] ?? '') }}">
                                        @error('waktu_mulai')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                                        <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror"
                                            id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', $timeInfo['waktu_selesai'] ?? '') }}">
                                        @error('waktu_selesai')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a href="{{ route('rapat.reservation.showStep1') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                                        <button type="submit" class="btn btn-primary shadow-sm">Lanjut ke Paket <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mt-2">
                <div class="card shadow-sm">
                    <img src="{{ asset('img/default/default-user.jpg') }}"
                        style="border-top-right-radius: 0.5rem; border-top-left-radius: 0.5rem; object-fit: cover; height: 200px;">
                    <div class="card-body">
                        <h5>Info Pemesanan</h5>
                        <table>
                            <tr>
                                <td style="text-align: center; width:50px"><i class="fas fa-user"></i></td>
                                <td>{{ $customer['nama'] }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;"><i class="fas fa-envelope"></i></td>
                                <td>{{ $customer['email'] }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;"><i class="fas fa-phone"></i></td>
                                <td>{{ $customer['no_hp'] }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;"><i class="fas fa-building"></i></td>
                                <td>{{ $customer['instansi'] ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection