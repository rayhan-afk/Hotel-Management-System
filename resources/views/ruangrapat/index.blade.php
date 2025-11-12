@extends('template.master')
@section('title', 'Ruang Rapat Management')

@section('content')
<div class="container-fluid">

    <div class="row mt-2 mb-2">
        <div class="col-lg-6 mb-2">
            </div>
        <div class="col-lg-6 mb-2">
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2"> 
                <button id="add-button" type="button" class="add-room-btn">
                    <i class="fas fa-plus"></i>
                    Tambah Paket Ruang Rapat
                </button>
                <a href="{{ route('rapat.reservation.showStep1') }}" class="btn btn-hotel-primary add-room-btn" style="height: auto; line-height: 1.5;"> 
                    <i class="fas fa-calendar-plus me-1"></i>
                    Buat Reservasi Ruang Rapat
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-6">
            <div class="row my-2 mt-4 ms-1">
                <div class="col-lg-12">
                    <h5><i class="fas fa-calendar-check me-2"></i>Jadwal Reservasi</h5>
                </div>
            </div>
            <div class="card p-0">
                <div class="card-body">
                    <div class="table-responsive" style="max-width: calc(100vw - 50px)">
                        <table class="table table-sm table-hover">
                            <thead style="background-color: #f7f3e8;">
                                <tr>
                                    <th>Instansi/Perusahaan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Paket</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rapatTransactionsJadwal as $transaction)
                                    <tr>
                                        <td>{{ $transaction->rapatCustomer->instansi ?? '-' }}</td>
                                        <td>{{ Helper::dateFormat($transaction->tanggal_pemakaian) }}</td>
                                        <td>{{ $transaction->waktu_mulai }} - {{ $transaction->waktu_selesai }}</td>
                                        <td>{{ $transaction->ruangRapatPaket->name }}</td>
                                        <td>
                                            <span class="badge {{ $transaction->status_pembayaran == 'Paid' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $transaction->status_pembayaran == 'Paid' ? 'Lunas' : $transaction->status_pembayaran }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('rapat.transaction.cancel', $transaction->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan reservasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded shadow-sm border m-0"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel Reservasi">
                                                    Batal
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center"> Tidak ada jadwal reservasi.
                                        </td>
                                    </tr>
                                @endforelse </tbody>
                        </table>
                        {{ $rapatTransactionsJadwal->appends([
                            'berlangsung_page' => $rapatTransactionsBerlangsung->currentPage(),
                            'expired_page' => $rapatTransactionsExpired->currentPage(), 
                            'search' => request('search')
                        ])->links('template.paginationlinks') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row my-2 mt-4 ms-1">
                <div class="col-lg-12">
                    <h5 class="text-danger"><i class="fas fa-play-circle me-2"></i>Reservasi Berlangsung</h5>
                </div>
            </div>
            <div class="card p-0 border-danger">
                <div class="card-body">
                    <div class="table-responsive" style="max-width: calc(100vw - 50px)">
                        <table class="table table-sm table-hover">
                            <thead class="bg-danger text-white">
                                <tr>
                                    <th>Instansi/Perusahaan</th>
                                    <th>Waktu Selesai</th>
                                    <th>Paket</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rapatTransactionsBerlangsung as $transaction)
                                <tr>
                                    <td>{{ $transaction->rapatCustomer->instansi ?? '-' }}</td>
                                    <td>{{ $transaction->waktu_selesai }}</td>
                                    <td>{{ $transaction->ruangRapatPaket->name }}</td>
                                    <td>
                                        <span class="badge {{ $transaction->status_pembayaran == 'Paid' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $transaction->status_pembayaran == 'Paid' ? 'Lunas' : $transaction->status_pembayaran }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center"> Tidak ada reservasi berlangsung.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $rapatTransactionsBerlangsung->appends([
                            'jadwal_page' => $rapatTransactionsJadwal->currentPage(),
                            'expired_page' => $rapatTransactionsExpired->currentPage(), 
                            'search' => request('search')
                        ])->links('template.paginationlinks') }}
                    </div>
                </div>
            </div>
        </div>
    </div> <hr class="my-2"> 

    <div class="row">
        <div class="col-12">
    <div class="professional-table-container">
        <div class="table-header">
            <h4><i class="fas fa-handshake me-2"></i>Manajemen Paket Ruang Rapat</h4>
            <p>Kelola daftar paket ruang rapat yang tersedia di hotel</p>
        </div>
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
                    </tbody>
            </table>
        </div>
        <div class="table-footer">
            <h3><i class="fas fa-handshake me-2"></i>Daftar Ruang Rapat</h3>
        </div>
    </div>
</div>
@endsection