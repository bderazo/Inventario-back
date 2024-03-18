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
        Schema::create('puntos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('cantidad');
            $table->foreignUuid('user_tarjeta_id')
                ->constrained('user_tarjeta')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('user_registra')
                ->constrained('user_tarjeta')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('detalle')->nullable();
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
        Schema::dropIfExists('puntos');
    }
};
