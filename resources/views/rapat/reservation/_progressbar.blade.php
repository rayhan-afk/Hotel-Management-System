@php
    // Definisikan 4 langkah baru kita
    $steps = [
        'rapat.reservation.showStep1' => 'Info Pemesan',
        'rapat.reservation.showStep2' => 'Waktu Reservasi',
        'rapat.reservation.showStep3' => 'Pilih Paket',
        'rapat.reservation.showStep4' => 'Konfirmasi & Bayar',
    ];
    $currentRoute = Route::currentRouteName();
@endphp

<div class="row justify-content-center">
    <div class="col-lg-10"> <div class="card shadow-sm">
            <div class="card-body">
                <ul class="progress-indicator m-4">
                    
                    <li class="{{ 
                        ( $currentRoute == 'rapat.reservation.showStep1' ||
                          $currentRoute == 'rapat.reservation.showStep2' ||
                          $currentRoute == 'rapat.reservation.showStep3' ||
                          $currentRoute == 'rapat.reservation.showStep4' ) ? 'completed' : ''
                    }}">
                        <span class="bubble"></span> {{ $steps['rapat.reservation.showStep1'] }}
                    </li>
                    
                    <li class="{{ 
                        ( $currentRoute == 'rapat.reservation.showStep2' ||
                          $currentRoute == 'rapat.reservation.showStep3' ||
                          $currentRoute == 'rapat.reservation.showStep4' ) ? 'completed' : ''
                    }}">
                        <span class="bubble"></span> {{ $steps['rapat.reservation.showStep2'] }}
                    </li>
                    
                    <li class="{{ 
                        ( $currentRoute == 'rapat.reservation.showStep3' ||
                          $currentRoute == 'rapat.reservation.showStep4' ) ? 'completed' : ''
                    }}">
                        <span class="bubble"></span> {{ $steps['rapat.reservation.showStep3'] }}
                    </li>
                    
                    <li class="{{ 
                        ( $currentRoute == 'rapat.reservation.showStep4' ) ? 'completed' : ''
                    }}">
                        <span class="bubble"></span> {{ $steps['rapat.reservation.showStep4'] }}
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
</div>