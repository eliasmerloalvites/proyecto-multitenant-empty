<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un "turno" de caja: desde que se aperturó (con un monto inicial)
     * hasta que se cerró (con el monto contado y la diferencia contra lo
     * esperado). Ventas/compras/gastos quedan ligados a la sesión, no solo
     * a la caja, para poder reconstruir el arqueo exacto de cada turno.
     */
    public function up(): void
    {
        Schema::create('caja_sesion', function (Blueprint $table) {
            $table->increments('CS_Id');

            $table->unsignedInteger('CAJ_Id');
            // Nullable: una apertura/cierre automático por programación
            // horaria (ver CajaProgramacionCommand) no tiene un usuario
            // detrás que lo haya hecho manualmente.
            $table->unsignedBigInteger('USU_Id_Apertura')->nullable();
            $table->unsignedBigInteger('USU_Id_Cierre')->nullable();

            $table->decimal('CS_MontoApertura', 10, 2)->default(0);
            $table->decimal('CS_MontoEsperado', 10, 2)->nullable();
            $table->decimal('CS_MontoReal', 10, 2)->nullable();
            $table->decimal('CS_Diferencia', 10, 2)->nullable();

            $table->dateTime('CS_FechaApertura');
            $table->dateTime('CS_FechaCierre')->nullable();

            $table->enum('CS_Estado', ['abierta', 'cerrada'])->default('abierta');
            $table->enum('CS_TipoCierre', ['manual', 'automatico'])->nullable();
            $table->string('CS_Observacion', 500)->nullable();

            $table->timestamps();

            $table->foreign('CAJ_Id')->references('CAJ_Id')->on('caja')->cascadeOnDelete();
            $table->foreign('USU_Id_Apertura')->references('id')->on('users')->nullOnDelete();
            $table->foreign('USU_Id_Cierre')->references('id')->on('users')->nullOnDelete();

            $table->index(['CAJ_Id', 'CS_Estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_sesion');
    }
};
