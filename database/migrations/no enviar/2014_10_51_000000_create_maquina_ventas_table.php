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
        Schema::create('maquina_ventas', function (Blueprint $table) {
            $table->foreignUuid('id_venta')->nullable()
                ->constrained('ventas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('id_maquina')->nullable()
                ->constrained('maquinas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->double('cantidad')->nullable();
            $table->double('precio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maquina_ventas');
    }
};