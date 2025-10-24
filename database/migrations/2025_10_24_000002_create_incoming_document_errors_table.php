<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incoming_document_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incoming_document_id');
            $table->string('uploader_id')->nullable();
            $table->string('error_type');
            $table->text('message');
            $table->timestamps();

            $table->foreign('incoming_document_id')->references('id')->on('incoming_documents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('incoming_document_errors');
    }
};
