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
        Schema::create('vartotojas', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['administratorius','kontrolierius','naudotojas'])
                ->default('naudotojas');
            $table->string('slapyvardis', 100);
            $table->string('slaptazodis', 255);
            $table->string('el_pastas', 255);
            $table->boolean('gali_kurti')->default(0);
            $table->date('registracijos_data');
            $table->string('tel', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vartotojas');
    }
};
