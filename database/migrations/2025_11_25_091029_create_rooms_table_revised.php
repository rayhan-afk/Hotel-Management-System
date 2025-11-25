<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTableRevised extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            
            // KOLOM ASLI (Dipertahankan)
            $table->foreignId('type_id')->constrained(); // Tipe Kamar
            $table->string('number'); // Nomor Kamar
            $table->bigInteger('capacity'); // Kapasitas
            $table->double('price'); // Harga
            
            // KOLOM BARU YANG DIMINTA
            $table->string('name'); // Nama Kamar
            $table->double('area_sqm')->nullable(); // Luas Kamar
            $table->longText('room_facilities')->nullable(); // Fasilitas Kamar
            $table->longText('bathroom_facilities')->nullable(); // Fasilitas Kamar Mandi
            $table->string('main_image_path')->nullable(); // Gambar Kamar (path utama)
            
            // Kolom 'room_status_id' dan 'view' dihilangkan
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}