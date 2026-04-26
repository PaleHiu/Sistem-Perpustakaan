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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->foreignId('petugas_id')->nullable()->constrained('petugas')->onDelete('set null'); 
            $table->string('kode_otp')->unique();
            $table->timestamp('waktu_booking');
            $table->timestamp('otp_expired_at');
            $table->date('tanggal_pinjam')->nullable();
            $table->date('batas_pengembalian')->nullable();
            $table->date('tanggal_dikembalikan')->nullable();
            $table->integer('total_denda')->default(0);
            $table->enum('status_transaksi', ['Menunggu OTP', 'Dipinjam', 'Selesai', 'Batal'])->default('Menunggu OTP');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
