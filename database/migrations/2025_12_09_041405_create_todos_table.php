<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();

            // Kolom untuk judul tugas (yang akan ditampilkan)
            $table->string('title');

            // Kolom untuk status penyelesaian tugas
            // Defaultnya 0 (belum selesai)
            $table->boolean('is_completed')->default(false);

            // Kolom timestamps (created_at dan updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
