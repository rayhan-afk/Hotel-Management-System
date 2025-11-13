@extends('template.master')
@section('title', 'Reservasi Ruang Rapat - Konfirmasi')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
@endsection

@section('content')
    <div class="container mt-3">
        @include('rapat.reservation._progressbar') 
        
        <div class="row justify-content-md-center mt-4">
            
            <div class="col-md-8 mt-2">
                <div class="card shadow-sm border">
                    <div class="card-header">
                        <h5 class="mb-0">Langkah 4: Konfirmasi & Pembayaran</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row">
                            <h6 class="text-primary">Detail Paket</h6>
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Nama Paket</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $paket->name }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Harga / Jam</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ Helper::convertToRupiah($paket->harga) }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Peserta</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="{{ $paketInfo['jumlah_peserta'] }} Orang" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <h6 class="text-primary">Detail Reservasi & Biaya</h6>
                            <div class="col-sm-12 mt-2">
                                <form method="POST" action="{{ route('rapat.reservation.processPayment') }}">
                                    @csrf
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{ Helper::dateFormat($timeInfo['tanggal_pemakaian']) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Waktu Mulai</label>
                                        <div class="col-sm-9">
                                            <input type="time" class="form-control" value="{{ $timeInfo['waktu_mulai'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Waktu Selesai</label>
                                        <div class="col-sm-9">
                                            <input type="time" class="form-control" value="{{ $timeInfo['waktu_selesai'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label">Total Durasi</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{ $durasiJam }} Jam" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fs-5">Total Biaya</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control fs-5 text-success" 
                                                    value="{{ Helper::convertToRupiah($totalHarga) }} ({{ Helper::convertToRupiah($paket->harga) }} x {{ $durasiJam }} jam)" readonly 
                                                    style="font-weight: bold;">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('rapat.reservation.showStep3') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                                        <button type="submit" class="btn btn-success btn-lg float-end">
                                            <i class="fas fa-shield-alt me-2"></i> Bayar & Konfirmasi
                                        </button>
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
                        <h5>Info Pemesan</h5>
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