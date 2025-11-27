<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'number',
        'name',
        'capacity',
        'price',
        'area_sqm',
        'room_facilities',
        'bathroom_facilities',
        'main_image_path',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    // === [PENTING] RELASI UTAMA UNTUK FILTER REPOSITORY ===
    // Tanpa ini, whereDoesntHave('transactions') akan error/kosong
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    // ======================================================
    
    // === RELASI CUSTOM (UNTUK STATUS DINAMIS) ===
    
    public function currentTransaction()
    {
        return $this->hasOne(Transaction::class, 'room_id')
                    ->where('check_in', '<=', Carbon::now())
                    ->where('check_out', '>=', Carbon::now());
    }

    public function futureReservation()
    {
        return $this->hasOne(Transaction::class, 'room_id')
                    ->where('check_in', '>', Carbon::now())
                    ->orderBy('check_in', 'ASC');
    }

    public function latestCheckoutToday()
    {
        return $this->hasOne(Transaction::class, 'room_id')
                    ->whereDate('check_out', Carbon::today())
                    ->orderBy('check_out', 'DESC');
    }

    public function getDynamicStatusAttribute()
    {
        // 1. Cek Sedang Dipakai
        if ($this->currentTransaction) {
            return 'Occupied';
        }
        
        // 2. Cek Baru Saja Checkout (Status Cleaning)
        $lastCheckout = $this->latestCheckoutToday;
        if ($lastCheckout) {
            // Jika checkout kurang dari 2 jam yang lalu, anggap Cleaning
            // (Sesuaikan logika jamnya, misal < 2 jam)
            $checkoutTime = Carbon::parse($lastCheckout->check_out); 
            // Note: Karena check_out di DB tipe DATE (00:00:00), logika jam ini 
            // mungkin kurang akurat kecuali Anda punya kolom 'checkout_time' terpisah.
            // Untuk sementara kita return Available saja kalau sudah lewat hari.
        }
        
        // 3. Cek Reservasi Mendatang
        if ($this->futureReservation) {
            return 'Reserved';
        }

        return 'Available';
    }

    public function firstImage()
    {
        if (!empty($this->main_image_path)) {
            return asset($this->main_image_path);
        }
        return asset('img/default/default-room.png');
    }
}