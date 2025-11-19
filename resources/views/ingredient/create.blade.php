<form id="form-save-ingredient" action="{{ route('ingredient.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold">Nama Bahan</label>
        <input type="text" class="form-control" name="name" placeholder="Contoh: Wortel, Ayam">
        <div id="error_name" class="text-danger error"></div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Kategori</label>
            <input type="text" class="form-control" name="category" list="cat_options" placeholder="Pilih/Ketik...">
            <datalist id="cat_options">
                <option value="Sayuran">
                <option value="Daging">
                <option value="Bumbu">
                <option value="Sembako">
            </datalist>
            <div id="error_category" class="text-danger error"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Satuan</label>
            <input type="text" class="form-control" name="unit" placeholder="Kg, Gram, Pcs">
            <div id="error_unit" class="text-danger error"></div>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Stok Awal</label>
        <input type="number" class="form-control" name="stock" value="0" step="0.01">
        <div id="error_stock" class="text-danger error"></div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Keterangan</label>
        <textarea class="form-control" name="description" rows="2"></textarea>
        <div id="error_description" class="text-danger error"></div>
    </div>
</form>