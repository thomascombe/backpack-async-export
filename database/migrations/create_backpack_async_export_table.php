<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackpackAsyncExportTable extends Migration
{
    public function up()
    {
        Schema::create('backpack_async_export_table', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->string('export_type');
            $table->string('filename');
            $table->string('status');
            $table->text('error')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id', 'exports_ibfk_1')
                ->references('id')
                ->on('users');
        });
    }
}
