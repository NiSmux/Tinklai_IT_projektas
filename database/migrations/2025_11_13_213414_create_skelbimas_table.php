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
        Schema::create('skelbimas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vartotojas_id')
                ->constrained('vartotojas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('pavadinimas', 255);
            $table->text('aprasymas'); // tavo SQL = NOT NULL
            $table->integer('perziuros')->default(0);
            $table->date('sukurimo_data');
            $table->date('redagavimo_data')->nullable();
            $table->enum('busena', ['parduotas','neparduotas'])->default('neparduotas');
            $table->integer('kaina'); // tavo SQL = int(11)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skelbimas');
    }
};
