<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBreakfastToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menambah kolom breakfast
            $table->string('breakfast')->default('No')->after('room_id');
            
            // Menambah kolom total_price (WAJIB ADA untuk menyimpan hasil hitungan server)
            $table->double('total_price')->after('breakfast');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('breakfast');
            $table->dropColumn('total_price');
        });
    }
}