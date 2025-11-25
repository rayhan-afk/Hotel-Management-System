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
    
    // === RELASI UNTUK STATUS DINAMIS ===
    
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
        if ($this->currentTransaction) {
            return 'Occupied';
        }
        
        $lastCheckout = $this->latestCheckoutToday;
        if ($lastCheckout) {
            $checkoutTime = Carbon::parse($lastCheckout->check_out);
            if ($checkoutTime->diffInHours(Carbon::now()) < 1) {
                 return 'Cleaning';
            }
        }
        
        if ($this->futureReservation) {
            return 'Reserved';
        }

        return 'Available';
    }

    // === PERBAIKAN PADA FUNGSI GAMBAR ===
    public function firstImage()
    {
        // Jika ada path gambar di database
        if (!empty($this->main_image_path)) {
            // Bungkus dengan asset() agar menghasilkan URL absolut yang benar
            // Output: http://domain.com/storage/img/rooms/namafile.jpg
            return asset($this->main_image_path);
        }

        // Gambar default jika tidak ada gambar
        return asset('img/default/default-room.png');
    }
}