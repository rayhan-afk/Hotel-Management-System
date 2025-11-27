@extends('template.master')
@section('title', 'Check In Tamu')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER TITLE ala Laporan Kamar --}}
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-sign-in-alt me-2"></i> Check In Tamu</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="professional-table-container">

                {{-- HEADER CARD FILTER / TITLE --}}
                <div class="table-header p-3" style="position: relative; z-index: 2;">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fas fa-bed me-2"></i>Data Tamu Menginap (Active)
                    </h4>
                </div>

                {{-- TABEL DATATABLES --}}
                <div class="table-responsive mt-3">
                    <table id="checkin-table" class="professional-table table table-hover" style="width: 100%;">
                        <thead style="background-color: #f7f3e8;">
                            <tr>
                                <th>#</th>
                                <th>Tamu</th>
                                <th>Kamar</th>
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
                    <h3 class="mb-0"><i class="fas fa-user-check me-2"></i>Daftar Check-In Aktif</h3>
                </div>

            </div>

        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="editCheckinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Reservasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editCheckinBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
@vite('resources/js/pages/checkin.js')

<style>
    /* Warna Coklat Sama Seperti Laporan Kamar */
    .btn-brown {
        background-color: #50200C !important;
        border-color: #50200C !important;
    }
    .btn-brown:hover {
        background-color: #3d1909 !important;
        border-color: #3d1909 !important;
    }

    /* Hilangkan overlay form */
    .professional-table-container .table-header::before { 
        display: none !important; 
        content: none !important; 
    }
    .table-header { position: relative; z-index: 10; }
</style>
@endsection
