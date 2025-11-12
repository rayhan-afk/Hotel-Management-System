@extends('template.master')
@section('title', 'Ruang Rapat Management')
@section('content')
<div class="container-fluid">
    <!-- Add Button -->
    <div class="row mb-4">
        <div class="col-12">
            <button id="add-button" type="button" class="add-room-btn">
                <i class="fas fa-plus"></i>
                Tambah Paket Ruang Rapat
            </button>
        </div>
    </div>

    <!-- Table Container -->
    <div class="professional-table-container">
        <!-- Table Header -->
        <div class="table-header">
            <h4><i class="fas fa-handshake me-2"></i>Manajemen Ruang Rapat</h4>
            <p>Kelola daftar paket ruang rapat yang tersedia di hotel</p>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="ruangrapat-table" class="professional-table table" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Paket</th>
                        <th scope="col">Isi Paket</th>
                        <th scope="col">Fasilitas</th>
                        <th scope="col">Harga</th>
                        <th scope="col"><i class="fas fa-cog me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTable auto populate -->
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="table-footer">
            <h3><i class="fas fa-handshake me-2"></i>Daftar Ruang Rapat</h3>
        </div>
    </div>
</div>
@endsection
