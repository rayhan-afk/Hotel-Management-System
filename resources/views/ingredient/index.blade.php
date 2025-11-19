@extends('template.master')
@section('title', 'Persediaan Bahan Baku')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <button id="add-button" type="button" class="add-room-btn">
                <i class="fas fa-plus me-2"></i>Tambah Bahan Baku
            </button>
        </div>
    </div>

    <div class="professional-table-container">
        <div class="table-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4><i class="fas fa-carrot me-2"></i>Persediaan Bahan Baku</h4>
                <p>Kelola stok bahan dapur (Sayuran, Daging, Bumbu, dll)</p>
            </div>
            <div>
                <select id="category_filter" class="form-select shadow-sm" style="min-width: 200px; cursor: pointer;">
                    <option value="All">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="ingredient-table" class="professional-table table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th><i class="fas fa-box me-1"></i>Nama Bahan</th>
                        <th><i class="fas fa-tag me-1"></i>Kategori</th>
                        <th><i class="fas fa-cubes me-1"></i>Stok</th>
                        <th><i class="fas fa-ruler me-1"></i>Satuan</th>
                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                        <th><i class="fas fa-align-left me-1"></i>Keterangan</th>
                        <th><i class="fas fa-cog me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="table-footer">
            <h3><i class="fas fa-carrot me-2"></i>Daftar Bahan Baku</h3>
        </div>
    </div>
</div>

<div class="modal fade" id="main-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mainModalLabel">Judul Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-modal-save">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection