<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagenes_producto', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('producto_id')
                ->constrained('producto')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('url_imagen');
            $table->integer('estado');
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
        Schema::dropIfExists('imagenes_producto');
    }
};