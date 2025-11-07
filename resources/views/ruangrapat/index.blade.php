@extends('template.master')
@section('title', 'Manajemen Paket Ruang Rapat')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                {{-- Tombol ini dicari oleh JS di bawah dengan ID "add-button" --}}
                <button id="add-button" type="button" class="add-room-btn">
                    <i class="fas fa-plus"></i>
                    Tambah Paket Baru
                </button>
            </div>
        </div>

        <div class="professional-table-container">
            <div class="table-header">
                <h4><i class="fas fa-briefcase me-2"></i>Manajemen Paket Ruang Rapat</h4>
                <p>Kelola paket ruang rapat yang tersedia di hotel Anda</p>
            </div>

            <div class="table-responsive">
                {{-- Tabel ini dicari oleh JS di bawah dengan ID "ruangrapat-table" --}}
                <table id="ruangrapat-table" class="professional-table table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">
                                <i class="fas fa-list-ol me-1"></i>No
                            </th>
                            <th scope="col">
                                <i class="fas fa-box-open me-1"></i>Nama Paket
                            </th>
                            <th scope="col">
                                <i class="fas fa-clipboard-list me-1"></i>Isi Paket
                            </th>
                            <th scope="col">
                                <i class="fas fa-concierge-bell me-1"></i>Fasilitas
                            </th>
                            <th scope="col">
                                <i class="fas fa-dollar-sign me-1"></i>Harga (Rp)
                            </th>
                            <th scope="col">
                                <i class="fas fa-cog me-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>

            <div class="table-footer">
                <h3><i class="fas fa-briefcase me-2"></i>Daftar Paket</h3>
            </div>
        </div>
    </div>
@endsection