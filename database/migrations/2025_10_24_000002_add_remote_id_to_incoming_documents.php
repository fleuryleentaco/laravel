<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incoming_documents', function (Blueprint $table) {
            $table->bigInteger('remote_id')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('incoming_documents', function (Blueprint $table) {
            $table->dropColumn('remote_id');
        });
    }
};
