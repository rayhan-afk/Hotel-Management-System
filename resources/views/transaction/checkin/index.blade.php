@extends('template.master')
@section('title', 'Check In Tamu')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER --}}
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-sign-in-alt me-2"></i> Check In Tamu</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="professional-table-container">

                {{-- TABLE HEADER --}}
                <div class="table-header">
                    <h4><i class="fas fa-bed me-2"></i>Data Tamu Menginap (Active)</h4>
                    <p>Daftar tamu yang saat ini sedang menginap di hotel.</p>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table id="checkin-table" class="professional-table table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%;"><i class="fas fa-hashtag me-1"></i>No</th>
                                <th scope="col" style="width: 20%;"><i class="fas fa-user me-1"></i>Tamu</th>
                                <th scope="col" style="width: 15%;"><i class="fas fa-bed me-1"></i>Kamar</th>
                                <th scope="col" style="width: 10%;"><i class="fas fa-calendar-check me-1"></i>Check-In</th>
                                <th scope="col" style="width: 10%;"><i class="fas fa-calendar-times me-1"></i>Check-Out</th>
                                <th scope="col" style="width: 10%;" class="text-center"><i class="fas fa-utensils me-1"></i>Sarapan</th>
                                <th scope="col" style="width: 15%;" class="text-end"><i class="fas fa-dollar-sign me-1"></i>Total Harga</th>
                                <th scope="col" style="width: 10%;" class="text-center"><i class="fas fa-info-circle me-1"></i>Status</th>
                                <th scope="col" style="width: 5%;" class="text-center"><i class="fas fa-cog me-1"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- FOOTER --}}
                <div class="table-footer">
                    <h3><i class="fas fa-user-check me-2"></i>Total Tamu Aktif</h3>
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
    {{-- Javascript Load --}}
    @vite('resources/js/pages/checkin.js')

    {{-- Styles --}}
    <style>
        .professional-table-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-top: 20px;
        }
        .table-header { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .professional-table thead th { background-color: #f7f3e8; color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; padding: 12px; }
        .table-footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; color: #6c757d; }
        
        /* Hilangkan override CSS lama yang mungkin mengganggu */
        .professional-table-container .table-header::before { display: none !important; }
    </style>
@endsection