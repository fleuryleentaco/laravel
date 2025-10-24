<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incoming_documents', function (Blueprint $table) {
            $table->id();
            $table->string('uploader_id')->nullable();
            $table->string('callback_url')->nullable();
            $table->string('filename');
            $table->string('path');
            $table->string('mime')->nullable();
            $table->bigInteger('size')->nullable();
            $table->text('content')->nullable();
            $table->json('minhash')->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incoming_documents');
    }
};
