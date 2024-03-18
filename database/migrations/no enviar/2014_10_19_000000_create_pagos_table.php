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
        Schema::create('pagos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_tarjeta_id')
                ->constrained('user_tarjeta')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('estado');
            $table->string('detalle');
            $table->string('informacion_bancaria');
            $table->double('cantidad');
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
        Schema::dropIfExists('sociales_tarjeta');
    }
};
