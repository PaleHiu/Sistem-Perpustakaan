<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_peminjamen', function (Blueprint $table) {
            $table->id('id_detail');

            $table->unsignedBigInteger('id_peminjaman');
            $table->unsignedBigInteger('id_buku');

            $table->integer('jumlah');

            $table->timestamps();

            $table->foreign('id_peminjaman')
                ->references('id_peminjaman')
                ->on('peminjamen')
                ->onDelete('cascade');

            $table->foreign('id_buku')
                ->references('id_buku')
                ->on('bukus')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_peminjamen');
    }
};
