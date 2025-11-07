<form id="form-save-ruangrapat" action="{{ route('ruangrapat.update', $ruangrapat->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="name" class="form-label">Nama Paket</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $ruangrapat->name }}" required>
        <div class="invalid-feedback" data-field="name"></div>
    </div>
    
    <div class="mb-3">
        <label for="harga" class="form-label">Harga (Rp)</label>
        <input type="number" class="form-control" id="harga" name="harga" value="{{ $ruangrapat->harga }}" required>
        <div class="invalid-feedback" data-field="harga"></div>
    </div>
    <div class="mb-3">
        <label for="isi_paket" class="form-label">Isi Paket</label>
        <textarea class="form-control" id="isi_paket" name="isi_paket" rows="3">{{ $ruangrapat->isi_paket }}</textarea>
        <div class="invalid-feedback" data-field="isi_paket"></div>
    </div>
    <div class="mb-3">
        <label for="fasilitas" class="form-label">Fasilitas</label>
        <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3">{{ $ruangrapat->fasilitas }}</textarea>
        <div class="invalid-feedback" data-field="fasilitas"></div>
    </div>
</form>