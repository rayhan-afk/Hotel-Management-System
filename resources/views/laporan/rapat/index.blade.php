@extends('template.master')
@section('title', 'Laporan Ruang Rapat Selesai')

@section('content')
<div class="container-fluid">
    
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h2><i class="fas fa-history me-2"></i>Laporan Ruang Rapat</h2>
            </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            
            <div class="professional-table-container">
                
                <div class="table-header">
                    
                    <form method="GET" action="{{ route('laporan.rapat.index') }}" style="position: relative; z-index: 2;">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="tanggal_mulai" class="form-label text-black fs-4">Periode Dari Tanggal</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-5">
                                <label for="tanggal_selesai" class="form-label text-black fs-4">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn w-100 fs-5" id="laporan-search-btn">Search</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="professional-table table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Instansi/Perusahaan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Paket</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rapatTransactionsExpired as $transaction)
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Tidak ada data laporan untuk periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="table-footer">
                   <div class="d-flex justify-content-between align-items-center">
                        
                        <div></div>

                        <h3 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Reservasi</h3>
                        
                        <div>
                            {{ $rapatTransactionsExpired->appends(request()->query())->links('template.paginationlinks') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<style>
    #laporan-search-btn {
        background-color: #50200C;
        color: white;
        border-color: #50200C;
    }
    #laporan-search-btn:hover,
    #laporan-search-btn:focus {
        background-color: #3d1909; /* Warna coklat lebih gelap untuk hover/focus */
        border-color: #3d1909;
        color: white;
    }
</style>
@endsection