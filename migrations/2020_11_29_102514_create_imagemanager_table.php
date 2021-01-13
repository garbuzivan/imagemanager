<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_manager', function (Blueprint $table) {
            $table->id();
            $table->string('hash');
            $table->string('title')->nullable();
            $table->string('name');
            $table->string('path');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('type')->nullable();
            $table->integer('size')->nullable();
            $table->json('cache')->nullable();
            $table->integer('use')->default(0);
            $table->timestamps();
        });

        Schema::create('image_manager_use', function (Blueprint $table) {
            $table->id();
            $table->integer('image_id');
            $table->integer('item_id');
            $table->string('component');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_manager');
        Schema::dropIfExists('image_manager_use');
    }
}
