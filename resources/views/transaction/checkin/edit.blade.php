<form id="form-edit-checkin" action="{{ route('transaction.checkin.update', $transaction->id) }}">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label class="form-label fw-bold">Nama Tamu</label>
        <input type="text" class="form-control" value="{{ $transaction->customer->name }}" disabled>
    </div>

    <div class="mb-3">
        <label for="room_id" class="form-label fw-bold">Pilih Kamar</label>
        <select class="form-select" name="room_id" id="room_id">
            @foreach($rooms as $room)
                <option value="{{ $room->id }}" {{ $transaction->room_id == $room->id ? 'selected' : '' }}>
                    Room {{ $room->number }} - {{ $room->type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="check_in" class="form-label fw-bold">Check In</label>
            <input type="date" class="form-control" name="check_in" value="{{ $transaction->check_in }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="check_out" class="form-label fw-bold">Check Out</label>
            <input type="date" class="form-control" name="check_out" value="{{ $transaction->check_out }}">
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>