<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('komentaras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('skelbimas_id')
                ->constrained('skelbimas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('vartotojas_id')
                ->constrained('vartotojas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->text('zinute');  // NOT NULL
            $table->date('data');    // NOT NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentaras');
    }
};
