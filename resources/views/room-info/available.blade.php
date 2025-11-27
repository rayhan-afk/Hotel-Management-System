@extends('template.master')
@section('title', 'Kamar Tersedia')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER TITLE --}}
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-check-circle me-2"></i> Info Kamar Tersedia</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="professional-table-container">

                {{-- HEADER CARD --}}
                <div class="table-header p-3" style="position: relative; z-index: 2;">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fas fa-door-open me-2"></i>Daftar Kamar Kosong (Ready)
                    </h4>
                </div>

                {{-- TABEL DATATABLES --}}
                <div class="table-responsive mt-3">
                    <table id="available-room-table" class="professional-table table table-hover" style="width: 100%;">
                        <thead style="background-color: #f7f3e8;">
                            <tr>
                                <th>#</th>
                                <th>Kamar</th> {{-- No & Tipe --}}
                                <th class="text-center">Luas (m²)</th>
                                <th class="text-end">Harga / Malam</th>
                                <th class="text-center">Status</th>
                                <th>Fasilitas</th> {{-- Deskripsi Fasilitas --}}
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- FOOTER --}}
                <div class="table-footer d-flex justify-content-between align-items-center p-4">
                    <h3 class="mb-0"><i class="fas fa-search-location me-2"></i>Siap untuk Check-In</h3>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@section('footer')
{{-- Load JS khusus Kamar Tersedia --}}
@vite('resources/js/pages/kamar-tersedia.js')

<style>
    .professional-table-container .table-header::before { 
        display: none !important; 
        content: none !important; 
    }
    .table-header { position: relative; z-index: 10; }
</style>
@endsection