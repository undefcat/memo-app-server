<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fileable_id')->unsigned()->nullable();
            $table->string('fileable_type')->nullable();
            $table->bigInteger('size')->unsigned()->comment('파일크기(Octet)');
            $table->string('tag')->default('file');
            $table->string('mime_type', 30);
            $table->string('mime_subtype');
            $table->string('original_name')->comment('원본파일명');
            $table->string('path', 1024)->comment('저장경로');
            $table->timestamps();

            $table->index('fileable_id');
            $table->index('fileable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
