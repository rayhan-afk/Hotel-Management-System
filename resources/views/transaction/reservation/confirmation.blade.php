@extends('template.master')
@section('title', 'Konfirmasi Reservasi')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
    <style>
        .invoice-card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        .invoice-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .invoice-body {
            padding: 30px;
        }
        .table-invoice th {
            font-weight: 600;
            color: #555;
        }
        .table-invoice td {
            vertical-align: middle;
        }
        .total-row {
            background-color: #eef2f7;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .breakfast-badge {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
@endsection
@section('content')
    @include('transaction.reservation.progressbar')
    
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            {{-- Kolom Kiri: Invoice Tagihan --}}
            <div class="col-lg-8">
                <div class="card invoice-card mb-4">
                    <div class="invoice-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-file-invoice me-2"></i>Rincian Tagihan</h4>
                    </div>
                    <div class="invoice-body">
                        
                        {{-- Detail Reservasi --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Info Kamar</p>
                                <h5 class="fw-bold">{{ $room->number }} - {{ $room->type->name }}</h5>
                                <p class="mb-0 text-muted"><i class="fas fa-user me-1"></i> {{ $room->capacity }} Orang</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="text-muted mb-1 small text-uppercase fw-bold">Tanggal Menginap</p>
                                <p class="mb-0 fw-bold">{{ Helper::dateFormat($stayFrom) }} — {{ Helper::dateFormat($stayUntil) }}</p>
                                <p class="mb-0 text-primary">{{ $dayDifference }} Malam</p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <form method="POST" 
                              id="reservation-form"
                              action="{{ route('transaction.reservation.payDownPayment', ['customer' => $customer->id, 'room' => $room->id]) }}">
                            @csrf
                            
                            <input type="hidden" name="check_in" value="{{ $stayFrom }}">
                            <input type="hidden" name="check_out" value="{{ $stayUntil }}">
                            <input type="hidden" name="total_price" id="input_total_price" value="{{ $downPayment }}"> 

                            {{-- Tabel Rincian Biaya --}}
                            <div class="table-responsive">
                                <table class="table table-invoice">
                                    <thead>
                                        <tr>
                                            <th>Deskripsi Item</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Item 1: Sewa Kamar --}}
                                        <tr>
                                            <td>
                                                <span class="fw-bold">Sewa Kamar</span>
                                                <div class="small text-muted">Harga per malam</div>
                                            </td>
                                            <td class="text-end">{{ Helper::convertToRupiah($room->price) }}</td>
                                            <td class="text-center">{{ $dayDifference }} Malam</td>
                                            <td class="text-end fw-bold">{{ Helper::convertToRupiah($room->price * $dayDifference) }}</td>
                                        </tr>

                                        {{-- Item 2: Sarapan (Dinamis) --}}
                                        <tr id="row_breakfast" style="display: none;">
                                            <td>
                                                <span class="fw-bold text-success">Paket Sarapan</span>
                                                <div class="small text-muted">
                                                    <span class="breakfast-badge"><i class="fas fa-utensils me-1"></i> Max 2 Orang</span>
                                                </div>
                                            </td>
                                            <td class="text-end">Rp 140.000</td>
                                            <td class="text-center">{{ $dayDifference }} Malam</td>
                                            <td class="text-end fw-bold text-success" id="display_breakfast_total">Rp 0</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        {{-- Opsi Tambahan --}}
                                        <tr>
                                            <td colspan="4" class="bg-light p-3">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <label for="breakfast_select" class="fw-bold me-3 mb-0 text-dark">
                                                        <i class="fas fa-coffee me-1 text-warning"></i> Tambah Sarapan?
                                                    </label>
                                                    {{-- ID diganti jadi 'breakfast_select' untuk menghindari konflik JS lama --}}
                                                    <select class="form-select w-auto border-primary" id="breakfast_select" name="breakfast">
                                                        <option value="No" selected>Tidak</option>
                                                        <option value="Yes">Ya, Tambahkan (+Rp 140.000/malam)</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        {{-- Total Bayar (Lunas) --}}
                                        <tr class="total-row text-primary">
                                            <td colspan="3" class="text-end">Total yang Harus Dibayar</td>
                                            <td class="text-end" id="display_total_price">
                                                {{ Helper::convertToRupiah($room->price * $dayDifference) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('transaction.reservation.chooseRoom', ['customer' => $customer->id]) }}?check_in={{$stayFrom}}&check_out={{$stayUntil}}" 
                                   class="btn btn-outline-secondary px-4 py-2">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                                    Konfirmasi & Bayar Lunas <i class="fas fa-money-bill-wave ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Data Pelanggan --}}
            <div class="col-lg-4">
                <div class="card invoice-card border-0 sticky-top" style="top: 20px; z-index: 1;">
                    <div class="card-header bg-white text-center py-4 border-0">
                        <img src="{{ $customer->user->getAvatar() }}"
                            class="rounded-circle shadow border p-1" 
                            style="width: 100px; height: 100px; object-fit: cover;">
                        <h5 class="mt-3 mb-0 fw-bold">{{ $customer->name }}</h5>
                        <p class="text-muted small mb-0">{{ $customer->job }}</p>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-4 py-3 d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-venus-mars me-2"></i> Gender</span>
                                <span class="fw-medium">{{ $customer->gender }}</span>
                            </li>
                            <li class="list-group-item px-4 py-3 d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-birthday-cake me-2"></i> Lahir</span>
                                <span class="fw-medium">{{ $customer->birthdate }}</span>
                            </li>
                            <li class="list-group-item px-4 py-3">
                                <div class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i> Alamat</div>
                                <p class="mb-0 fw-medium small">{{ $customer->address }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
<script>
    // Ambil variabel PHP langsung (Lebih aman daripada parsing atribut data HTML)
    const roomPricePerNight = {{ $room->price }};
    const dayCount = {{ $dayDifference }};
    const breakfastPricePerDay = 140000; 
    const baseTotal = roomPricePerNight * dayCount;

    // Elemen DOM
    const breakfastSelect = document.getElementById('breakfast_select');
    const rowBreakfast = document.getElementById('row_breakfast');
    const displayBreakfastTotal = document.getElementById('display_breakfast_total');
    const displayTotalPrice = document.getElementById('display_total_price');
    const inputTotalPrice = document.getElementById('input_total_price');

    // Fungsi Format Rupiah
    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0,
            maximumFractionDigits: 0 
        }).format(number);
    };

    // Event Listener
    breakfastSelect.addEventListener('change', function() {
        let currentTotal = baseTotal;
        
        if (this.value === 'Yes') {
            // Jika pilih sarapan
            const breakfastTotal = breakfastPricePerDay * dayCount;
            currentTotal += breakfastTotal;
            
            // Tampilkan baris sarapan & update harga
            rowBreakfast.style.display = 'table-row';
            displayBreakfastTotal.innerText = formatRupiah(breakfastTotal);
        } else {
            // Jika tidak pilih sarapan
            rowBreakfast.style.display = 'none';
        }

        // Update Tampilan Total Akhir & Input Hidden
        displayTotalPrice.innerText = formatRupiah(currentTotal);
        if(inputTotalPrice) {
            inputTotalPrice.value = currentTotal;
        }
    });
</script>
@endsection