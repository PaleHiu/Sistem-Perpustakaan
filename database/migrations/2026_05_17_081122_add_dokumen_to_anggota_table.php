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
    Schema::table('anggota', function (Blueprint $table) {
        $table->string('dokumen_identitas')->nullable()->after('alamat');
    });
}

public function down(): void
{
    Schema::table('anggota', function (Blueprint $table) {
        $table->dropColumn('dokumen_identitas');
    });
}
};
