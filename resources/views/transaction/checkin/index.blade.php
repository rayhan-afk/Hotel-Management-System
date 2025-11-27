@extends('template.master')
@section('title', 'Check In')

@section('content')
<div class="container-fluid">
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-luggage-cart me-2"></i>Check In Tamu</h2>
            <p class="text-muted">Kelola data tamu yang sudah reservasi atau sedang menginap.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="checkin-table" class="table table-hover w-100">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Tamu</th>
                                    <th>Kamar</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editCheckinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Reservasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editCheckinBody">
                {{-- Content loaded via AJAX --}}
                <div class="text-center py-3"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    @vite('resources/js/pages/checkin.js')
@endsection