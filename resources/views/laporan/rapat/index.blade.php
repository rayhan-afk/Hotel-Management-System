@extends('template.master')
@section('title', 'Laporan Ruang Rapat')

@section('content')
<div class="container-fluid">
    
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-history me-2"></i>Laporan Ruang Rapat</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="professional-table-container">
                
                {{-- FILTER SECTION --}}
                <div class="table-header p-3">
                    <form id="filter-form">
                        <div class="row align-items-end">
                            {{-- Input Tanggal Mulai --}}
                            <div class="col-md-5 mb-3">
                                <label for="tanggal_mulai" class="form-label text-dark fs-5 fw-bold">Periode Dari Tanggal</label>
                                <input type="date" id="tanggal_mulai" class="form-control shadow-sm form-control-lg">
                            </div>

                            {{-- Input Tanggal Selesai --}}
                            <div class="col-md-5 mb-3">
                                <label for="tanggal_selesai" class="form-label text-dark fs-5 fw-bold">Sampai Tanggal</label>
                                <input type="date" id="tanggal_selesai" class="form-control shadow-sm form-control-lg">
                            </div>

                            {{-- Tombol Search & Reset --}}
                            <div class="col-md-2 mb-3">
                                <div class="d-flex gap-2">
                                    {{-- Tombol Search (Coklat) --}}
                                    <button type="button" id="btn-filter" class="btn w-100 btn-lg text-white shadow-sm btn-search-custom">
                                        Search
                                    </button>
                                    
                                    {{-- Tombol Reset --}}
                                    <button type="button" id="btn-reset" class="btn btn-secondary w-100 btn-lg shadow-sm" title="Reset Filter">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TABEL DATATABLES --}}
                <div class="table-responsive mt-3">
                    <table id="laporan-rapat-table" class="professional-table table table-hover" style="width: 100%;">
                        <thead style="background-color: #f7f3e8;">
                            <tr>
                                <th>Instansi/Perusahaan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Paket</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data diisi otomatis oleh DataTables via AJAX --}}
                        </tbody>
                    </table>
                </div>
                
                <div class="table-footer">
                   <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Reservasi</h3>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<style>
    /* PERBAIKAN UTAMA: Menghilangkan overlay yang menutupi form */
    .professional-table-container .table-header::before {
        display: none !important;
        content: none !important;
    }

    /* Memastikan form berada di atas segalanya */
    .table-header form {
        position: relative;
        z-index: 10;
    }

    /* Style Khusus Tombol Search Coklat */
    .btn-search-custom {
        background-color: #50200C !important;
        border-color: #50200C !important;
    }
    .btn-search-custom:hover {
        background-color: #3d1909 !important;
        border-color: #3d1909 !important;
    }
</style>
@endsection