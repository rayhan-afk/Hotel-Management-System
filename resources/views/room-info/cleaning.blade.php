@extends('template.master')
@section('title', 'Kamar Dibersihkan')

@section('content')
    <div class="container-fluid">
        
        <div class="professional-table-container">
            
            <div class="table-header">
                <h4><i class="fas fa-broom me-2"></i>Kamar Sedang Dibersihkan</h4>
                <p>Daftar kamar yang baru saja Check-out hari ini dan perlu dibersihkan.</p>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="cleaning-room-table" class="professional-table table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 5%;"><i class="fas fa-hashtag me-1"></i>No</th>
                            <th scope="col" style="width: 20%;"><i class="fas fa-user me-1"></i>Tamu</th>
                            <th scope="col" style="width: 15%;"><i class="fas fa-bed me-1"></i>Kamar</th>
                            <th scope="col" style="width: 10%;"><i class="fas fa-calendar-check me-1"></i>Check-In</th>
                            <th scope="col" style="width: 10%;"><i class="fas fa-calendar-times me-1"></i>Check-Out</th>
                            <th scope="col" style="width: 10%;" class="text-center"><i class="fas fa-utensils me-1"></i>Sarapan</th>
                            <th scope="col" style="width: 15%;" class="text-end"><i class="fas fa-dollar-sign me-1"></i>Total Harga</th>
                            <th scope="col" style="width: 15%;" class="text-center"><i class="fas fa-info-circle me-1"></i>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="table-footer">
                <h3><i class="fas fa-clipboard-check me-2"></i>Status Housekeeping</h3>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <style>
        .professional-table-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-top: 20px;
        }
        .table-header { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .professional-table thead th { background-color: #f7f3e8; color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; padding: 12px; }
        .table-footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; color: #6c757d; }
    </style>
@endsection