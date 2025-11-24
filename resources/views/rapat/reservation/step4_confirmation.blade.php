@extends('template.master')
@section('title', 'Konfirmasi Reservasi')
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
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Langkah 4: Konfirmasi & Pembayaran</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        {{-- INFO PAKET --}}
                        <div class="alert alert-light border">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Paket:</strong> {{ $paket->name }}
                                </div>
                                <div class="col-md-6 text-end">
                                    <strong>Peserta:</strong> {{ $jumlahOrang }} Orang
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- RINCIAN BIAYA --}}
                        <h6 class="text-primary fw-bold mb-3">Rincian Biaya</h6>
                        <table class="table table-sm bg-light rounded border">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Deskripsi</th>
                                    <th class="text-end">Perhitungan</th>
                                    <th class="text-end pe-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 1. BIAYA PAKET --}}
                                <tr>
                                    <td class="ps-3">Biaya Paket (Per Orang)</td>
                                    <td class="text-end">{{ Helper::convertToRupiah($paket->harga) }} x {{ $jumlahOrang }} org</td>
                                    <td class="text-end pe-3 fw-bold">{{ Helper::convertToRupiah($biayaPaketTotal) }}</td>
                                </tr>
                                
                                {{-- 2. BIAYA SEWA RUANG (DURASI) --}}
                                <tr>
                                    <td class="ps-3">Sewa Ruang ({{ $durasiJam }} Jam)</td>
                                    <td class="text-end">Rp 100.000 x {{ $durasiJam }} jam</td>
                                    <td class="text-end pe-3 fw-bold">{{ Helper::convertToRupiah($biayaSewaRuangTotal) }}</td>
                                </tr>
                                
                                {{-- TOTAL TAGIHAN --}}
                                <tr class="border-top border-secondary bg-white">
                                    <td class="ps-3 pt-3 fs-5 fw-bold text-dark">TOTAL TAGIHAN</td>
                                    <td></td>
                                    <td class="text-end pe-3 pt-3 fs-4 fw-bold text-primary">{{ Helper::convertToRupiah($totalHarga) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- TOMBOL --}}
                        <form method="POST" action="{{ route('rapat.reservation.processPayment') }}" class="mt-4">
                            @csrf
                            
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle fs-4 me-3"></i>
                                <div>
                                    Klik tombol di bawah untuk konfirmasi. Status transaksi akan otomatis <strong>Lunas (Paid)</strong>.
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('rapat.reservation.showStep3') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-success btn-lg px-4 shadow">
                                    <i class="fas fa-save me-2"></i> Bayar & Simpan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            {{-- SIDEBAR INFO --}}
            <div class="col-md-4 mt-2">
                <div class="card shadow-sm">
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