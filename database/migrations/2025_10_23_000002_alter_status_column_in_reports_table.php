<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('status', 10)->nullable()->change(); // Remettre à la taille d'origine si besoin
        });
    }
};
