@extends('template.master')
@section('title', 'Manajemen Amenities')

@section('content')
<div class="container-fluid">

    <div class="row mt-2 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
                {{-- Tombol Tambah Data --}}
                <a href="{{ route('amenity.create') }}" class="btn btn-hotel-primary add-room-btn">
                    <i class="fas fa-plus me-1"></i>
                    Tambah Data Amenity
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{-- Menggunakan style tabel yang sama dengan Ruang Rapat --}}
            <div class="professional-table-container">
                <div class="table-header">
                    <h4><i class="fas fa-soap me-2"></i>Manajemen Persediaan Amenities</h4>
                    <p>Kelola daftar amenities (sabun, sampo, handuk, dll.)</p>
                </div>
                <div class="table-responsive">
                    <table id="amenity-table" class="professional-table table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Status Stok</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col"><i class="fas fa-cog me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh DataTables --}}
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <h3><i class="fas fa-soap me-2"></i>Daftar Amenities</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
{{-- Script untuk inisialisasi DataTables --}}
<script>
    $(document).ready(function() {
        $('#amenity-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('amenity.datatable') }}", // Ambil data dari rute yang kita buat
            columns: [
                // Kolom No.
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                // Kolom Nama Barang
                { data: 'nama_barang', name: 'nama_barang' },
                // Kolom Satuan
                { data: 'satuan', name: 'satuan' },
                // Kolom Status Stok (dibuat di controller)
                { data: 'status_stok', name: 'status_stok', orderable: true, searchable: false },
                // Kolom Keterangan
                { data: 'keterangan', name: 'keterangan' },
                // Kolom Action (dibuat di controller)
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection