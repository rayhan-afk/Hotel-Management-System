<form id="form-save-ingredient" action="{{ route('ingredient.update', $ingredient->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label fw-bold">Nama Bahan</label>
        <input type="text" class="form-control" name="name" value="{{ $ingredient->name }}">
        <div id="error_name" class="text-danger error"></div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Kategori</label>
            <input type="text" class="form-control" name="category" list="cat_options" value="{{ $ingredient->category }}">
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
            <input type="text" class="form-control" name="unit" value="{{ $ingredient->unit }}">
            <div id="error_unit" class="text-danger error"></div>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Stok Saat Ini</label>
        <input type="number" class="form-control" name="stock" value="{{ $ingredient->stock }}" step="0.01">
        <div id="error_stock" class="text-danger error"></div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Keterangan</label>
        <textarea class="form-control" name="description" rows="2">{{ $ingredient->description }}</textarea>
        <div id="error_description" class="text-danger error"></div>
    </div>
</form>