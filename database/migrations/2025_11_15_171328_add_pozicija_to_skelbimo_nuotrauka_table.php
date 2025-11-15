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
        Schema::table('skelbimo_nuotrauka', function (Blueprint $table) {
            $table->integer('pozicija')->default(0)->after('failo_kelias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('skelbimo_nuotrauka', function (Blueprint $table) {
            $table->dropColumn('pozicija');
        });
    }
};
