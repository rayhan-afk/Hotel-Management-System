<?php
// app/Models/RuangRapatPaket.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangRapatPaket extends Model
{
    use HasFactory;

    // Tentukan kolom mana yang boleh diisi
    protected $fillable = [
        'name',
        'isi_paket',
        'fasilitas',
        'harga',
    ];
}