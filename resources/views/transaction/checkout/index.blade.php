@extends('template.master')
@section('title', 'Check Out Tamu')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER --}}
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-sign-out-alt me-2"></i> Check Out Tamu</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="professional-table-container">

                {{-- HEADER CARD --}}
                <div class="table-header p-3" style="position: relative; z-index: 2;">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fas fa-bed me-2"></i>Data Tamu Siap Check-Out
                    </h4>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive mt-3">
                    <table id="checkout-table" class="professional-table table table-hover" style="width: 100%;">
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
                    <h3 class="mb-0"><i class="fas fa-sign-out-alt me-2"></i>Daftar Tamu Melakukan Check-Out</h3>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection

@section('footer')
@vite('resources/js/pages/checkout.js')

<style>
    .btn-brown {
        background-color: #50200C !important;
        border-color: #50200C !important;
    }
    .btn-brown:hover {
        background-color: #3d1909 !important;
        border-color: #3d1909 !important;
    }

    .professional-table-container .table-header::before { 
        display: none !important; 
    }
    .table-header { position: relative; z-index: 10; }
</style>
@endsection
