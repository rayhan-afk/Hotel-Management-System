@extends('template.master')
@section('title', 'Reservasi Kamar')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER TITLE --}}
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-calendar-alt me-2"></i> Reservasi Kamar</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="professional-table-container">

                {{-- HEADER CARD FILTER / TITLE --}}
                <div class="table-header p-3" style="position: relative; z-index: 2;">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fas fa-clock me-2"></i>Daftar Reservasi Mendatang
                    </h4>
                </div>

                {{-- TABEL DATATABLES --}}
                <div class="table-responsive mt-3">
                    <table id="reservation-table" class="professional-table table table-hover" style="width: 100%;">
                        <thead style="background-color: #f7f3e8;">
                            <tr>
                                <th>#</th>
                                <th>Tamu</th>
                                <th>Kamar</th> {{-- Nanti di JS digabung: Nomor + Tipe --}}
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th class="text-center">Sarapan</th>
                                <th class="text-end">Total Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- FOOTER --}}
                <div class="table-footer d-flex justify-content-between align-items-center p-4">
                    <h3 class="mb-0"><i class="fas fa-list-alt me-2"></i>Data Reservasi Belum Check-In</h3>
                </div>

            </div>

        </div>
    </div>
</div>

{{-- MODAL DETAIL (Opsional, jika ingin lihat detail sebelum batal) --}}
<div class="modal fade" id="detailReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Reservasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailReservationBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
{{-- 1. Load SweetAlert2 (Wajib agar alertnya cantik) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- 2. Load JS Logic --}}
@vite('resources/js/pages/reservasi-kamar.js')

<style>
    .btn-brown {
        background-color: #50200C !important;
        border-color: #50200C !important;
        color: white;
    }
    .btn-brown:hover {
        background-color: #3d1909 !important;
        border-color: #3d1909 !important;
        color: white;
    }
    .professional-table-container .table-header::before { 
        display: none !important; 
        content: none !important; 
    }
    .table-header { position: relative; z-index: 10; }
</style>
@endsection