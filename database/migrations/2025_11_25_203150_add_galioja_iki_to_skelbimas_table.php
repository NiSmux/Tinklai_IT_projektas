<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('skelbimas', function (Blueprint $table) {
            $table->date('galioja_iki')->nullable()->after('sukurimo_data');
            $table->boolean('aktyvus')->default(1)->after('galioja_iki');
        });
    }

    public function down()
    {
        Schema::table('skelbimas', function (Blueprint $table) {
            $table->dropColumn(['galioja_iki', 'aktyvus']);
        });
    }
};
