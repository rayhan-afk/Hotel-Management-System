@extends('template.master')
@section('title', 'Ruang Rapat Management')
@section('content')
    <div class="container-fluid">

        <!-- Add Ruang Rapat Button -->
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
                <h4><i class="fas fa-users me-2"></i>Manajemen Ruang Rapat</h4>
                <p>Kelola paket ruang rapat yang tersedia di hotel Anda</p>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="ruangrapat-table" class="professional-table table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">
                                <i class="fas fa-hashtag me-1"></i>No
                            </th>
                            <th scope="col">
                                <i class="fas fa-box me-1"></i>Nama Paket
                            </th>
                            <th scope="col">
                                <i class="fas fa-list me-1"></i>Isi Paket
                            </th>
                            <th scope="col">
                                <i class="fas fa-star me-1"></i>Fasilitas
                            </th>
                            <th scope="col">
                                <i class="fas fa-dollar-sign me-1"></i>Harga
                            </th>
                            <th scope="col">
                                <i class="fas fa-cog me-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTable will populate this -->
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="table-footer">
                <h3><i class="fas fa-users me-2"></i>Daftar Paket Ruang Rapat</h3>
            </div>
        </div>
    </div>
@endsection
