<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Pedidos que los clientes hacen desde la app (catalogo + carrito). Se pagan y
 * se recogen en el taller: el pedido llega PENDIENTE, el taller lo CONFIRMA
 * (desde ahi el stock queda apartado para el cliente), lo marca LISTO para
 * recoger y al cobrarlo en el punto de venta queda ENTREGADO (VEN_Id). El
 * precio de cada item se guarda al momento de pedir.
 */
return new class extends Migration
{
    private const GUARD = 'tenant';

    public function up(): void
    {
        Schema::create('pedido_movil', function (Blueprint $table) {
            $table->id('PED_Id');
            $table->unsignedBigInteger('CMV_Id');
            $table->unsignedInteger('ALM_Id');
            $table->string('PED_Estado', 20)->default('PENDIENTE'); // PENDIENTE / CONFIRMADO / LISTO / ENTREGADO / CANCELADO
            $table->decimal('PED_Total', 10, 2);
            $table->string('PED_Nota', 250)->nullable();
            $table->string('PED_CanceladoPor', 10)->nullable(); // CLIENTE / TALLER
            $table->string('PED_MotivoCancelacion', 250)->nullable();
            $table->unsignedInteger('VEN_Id')->nullable();
            $table->unsignedBigInteger('USU_Id_Atiende')->nullable();
            $table->dateTime('PED_ConfirmadoEn')->nullable();
            $table->dateTime('PED_ListoEn')->nullable();
            $table->dateTime('PED_EntregadoEn')->nullable();
            $table->dateTime('PED_CanceladoEn')->nullable();
            $table->timestamps();

            $table->index(['CMV_Id', 'PED_Estado'], 'PED_IDX_CUENTA');
            $table->index(['ALM_Id', 'PED_Estado'], 'PED_IDX_SEDE');

            $table->foreign('CMV_Id', 'PED_FK_CUENTA')->references('CMV_Id')->on('cuenta_movil')->cascadeOnDelete();
            $table->foreign('ALM_Id', 'PED_FK_SEDE')->references('ALM_Id')->on('almacen')->restrictOnDelete();
            $table->foreign('VEN_Id', 'PED_FK_VENTA')->references('VEN_Id')->on('venta')->nullOnDelete();
        });

        Schema::create('pedido_movil_item', function (Blueprint $table) {
            $table->id('PDI_Id');
            $table->unsignedBigInteger('PED_Id');
            $table->unsignedInteger('PRO_Id');
            $table->string('PDI_Nombre', 255);
            $table->decimal('PDI_Precio', 10, 2);
            $table->decimal('PDI_Cantidad', 10, 2);
            $table->decimal('PDI_Subtotal', 10, 2);
            $table->timestamps();

            $table->index('PRO_Id', 'PDI_IDX_PRODUCTO');
            $table->foreign('PED_Id', 'PDI_FK_PEDIDO')->references('PED_Id')->on('pedido_movil')->cascadeOnDelete();
            $table->foreign('PRO_Id', 'PDI_FK_PRODUCTO')->references('PRO_Id')->on('producto')->restrictOnDelete();
        });

        Permission::firstOrCreate(
            ['name' => 'tenant.appmovil.pedidos', 'guard_name' => self::GUARD],
            [
                'group_name' => 'App movil',
                'nombre' => 'Pedidos de la app',
                'description' => 'Ver, confirmar, preparar, cancelar y cobrar los pedidos que hacen los clientes desde la app',
                'estado' => true,
            ]
        );

        foreach (['Admin', 'Gerente', 'Recepcion', 'Ventas'] as $rolNombre) {
            Role::where('name', $rolNombre)->where('guard_name', self::GUARD)->first()?->givePermissionTo('tenant.appmovil.pedidos');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_movil_item');
        Schema::dropIfExists('pedido_movil');
        Permission::where('name', 'tenant.appmovil.pedidos')->where('guard_name', self::GUARD)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
