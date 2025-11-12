@extends('template.master')
@section('title', 'Reservasi Ruang Rapat')

@section('content')
    <div class="row mt-2 mb-2">
        <div class="col-lg-6 mb-2">
            <div class="d-grid gap-2 d-md-block">
                
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Tambah Reservasi Rapat">
                    <a href="{{ route('rapat.reservation.showStep1') }}" class="btn btn-sm shadow-sm myBtn border rounded">
                        <i class="fas fa-plus"></i>
                    </a>
                </span>
                
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Riwayat Pembayaran Rapat">
                    <a href="{{ route('rapat.payment.index') }}" class="btn btn-sm shadow-sm myBtn border rounded">
                        <i class="fas fa-history"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <form class="d-flex" method="GET" action="{{ route('rapat.transaction.index') }}">
                <input class="form-control me-2" type="search" placeholder="Search by ID or Nama" aria-label="Search"
                    id="search-user" name="search" value="{{ request()->input('search') }}">
                <button class="btn btn-outline-dark" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h5>Reservasi Aktif & Mendatang: </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card p-0">
                <div class="card-body">
                    <div class="table-responsive" style="max-width: calc(100vw - 50px)">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Pemesan</th>
                                    <th>Instansi/Perusahaan</th> 
                                    <th>Tanggal</th>             
                                    <th>Waktu</th>               
                                    <th>Paket</th>               
                                    <th>Status</th>              
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rapatTransactions as $transaction)
                                    <tr>
                                        <th>{{ ($rapatTransactions->currentpage() - 1) * $rapatTransactions->perpage() + $loop->index + 1 }}</th>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $transaction->rapatCustomer->nama }}</td>
                                        <td>{{ $transaction->rapatCustomer->instansi ?? '-' }}</td> <td>{{ Helper::dateFormat($transaction->tanggal_pemakaian) }}</td>
                                        <td>{{ $transaction->waktu_mulai }} - {{ $transaction->waktu_selesai }}</td>
                                        <td>{{ $transaction->ruangRapatPaket->name }}</td>
                                        <td>
                                            <span class="badge {{ $transaction->status_reservasi == 'Confirmed' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $transaction->status_reservasi }}
                                            </span>
                                            <span class="badge {{ $transaction->status_pembayaran == 'Paid' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $transaction->status_pembayaran }}
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-light btn-sm rounded shadow-sm border p-1 m-0 {{ $transaction->status_pembayaran == 'Paid' ? 'disabled' : '' }}"
                                                href="#" {{-- href="{{ route('rapat.transaction.payment.create', ['rapatTransaction' => $transaction->id]) }}" --}}
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Pay">
                                                <i class="fas fa-money-bill-wave-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            There's no data in this table
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $rapatTransactions->appends(['expired_page' => $rapatTransactionsExpired->currentPage(), 'search' => request('search')])->links('template.paginationlinks') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h5>Reservasi Selesai: </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card p-0">
                <div class="card-body">
                    <div class="table-responsive" style="max-width: calc(100vw - 50px)">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Pemesan</th>
                                    <th>Instansi/Perusahaan</th> <th>Tanggal</th>             <th>Waktu</th>               <th>Paket</th>               <th>Status</th>              </tr>
                            </thead>
                            <tbody>
                                @forelse ($rapatTransactionsExpired as $transaction)
                                <tr>
                                    <th>{{ ($rapatTransactionsExpired->currentpage() - 1) * $rapatTransactionsExpired->perpage() + $loop->index + 1 }}</th>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->rapatCustomer->nama }}</td>
                                    <td>{{ $transaction->rapatCustomer->instansi ?? '-' }}</td> <td>{{ Helper::dateFormat($transaction->tanggal_pemakaian) }}</td>
                                    <td>{{ $transaction->waktu_mulai }} - {{ $transaction->waktu_selesai }}</td>
                                    <td>{{ $transaction->ruangRapatPaket->name }}</td>
                                    <td>
                                        <span class="badge {{ $transaction->status_reservasi == 'Confirmed' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $transaction->status_reservasi }}
                                        </span>
                                        <span class="badge {{ $transaction->status_pembayaran == 'Paid' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $transaction->status_pembayaran }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            There's no data in this table
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $rapatTransactionsExpired->appends(['active_page' => $rapatTransactions->currentPage(), 'search' => request('search')])->links('template.paginationlinks') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection