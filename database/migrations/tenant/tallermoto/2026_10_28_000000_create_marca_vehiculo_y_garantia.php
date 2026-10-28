<?php

use Database\Seeders\Tenant\tallermoto\MarcaVehiculoSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Marca del vehiculo + mantenimiento "por garantia".
 *
 * - marca_vehiculo: catalogo de marcas (precargado con MarcaVehiculoSeeder;
 *   se administra en Mantenimientos > Marcas de Vehiculo).
 * - MAR_Id (opcional) en reservacion y en los 7 tipos de mantenimiento: la
 *   marca elegida al reservar pasa al mantenimiento en el check-in.
 * - {PREFIJO}_EsGarantia en los 7 mantenimientos: lo marca quien edita el
 *   mantenimiento. Garantia Hero/Lifan ya son de garantia: quedan en 1.
 * - Permiso tenant.mantenimientos.marcas.index para los roles que ya
 *   gestionan los Planes de mantenimiento.
 */
return new class extends Migration
{
    private const GUARD = 'tenant';

    private const MANTENIMIENTOS = [
        'mantenimiento_general_carburada' => 'MGC',
        'mantenimiento_general_inyectada' => 'MGI',
        'mantenimiento_preventivo_carburada' => 'MPC',
        'mantenimiento_preventivo_inyectada' => 'MPI',
        'mantenimiento_actividad_variadas' => 'MAV',
        'mantenimiento_garantia_hero' => 'MGH',
        'mantenimiento_garantia_lifan' => 'MGL',
    ];

    public function up(): void
    {
        Schema::create('marca_vehiculo', function (Blueprint $table) {
            $table->increments('MAR_Id');
            $table->string('MAR_Nombre', 60)->unique();
            $table->boolean('MAR_Estado')->default(true);
            $table->timestamps();
        });

        Schema::table('reservacion', function (Blueprint $table) {
            $table->unsignedInteger('MAR_Id')->nullable()->after('RES_Moto');
            $table->foreign('MAR_Id', 'RES_FK_MARCA')->references('MAR_Id')->on('marca_vehiculo')->nullOnDelete();
        });

        foreach (self::MANTENIMIENTOS as $tabla => $p) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }
            Schema::table($tabla, function (Blueprint $table) use ($p) {
                $table->unsignedInteger('MAR_Id')->nullable()->after("{$p}_Unidad");
                $table->boolean("{$p}_EsGarantia")->default(false)->after('MAR_Id');
                $table->foreign('MAR_Id', "{$p}_FK_MARCA")->references('MAR_Id')->on('marca_vehiculo')->nullOnDelete();
            });
        }

        foreach (['mantenimiento_garantia_hero' => 'MGH', 'mantenimiento_garantia_lifan' => 'MGL'] as $tabla => $p) {
            if (Schema::hasTable($tabla)) {
                DB::table($tabla)->update(["{$p}_EsGarantia" => 1]);
            }
        }

        (new MarcaVehiculoSeeder())->run();

        Permission::firstOrCreate(
            ['name' => 'tenant.mantenimientos.marcas.index', 'guard_name' => self::GUARD],
            [
                'group_name' => 'Mantenimientos',
                'nombre' => 'Gestionar Marcas de Vehículo',
                'description' => 'Agregar, editar y desactivar las marcas de vehículo que se eligen en reservas y mantenimientos',
                'estado' => true,
            ]
        );

        Role::where('guard_name', self::GUARD)->get()
            ->filter(fn ($rol) => $rol->name === 'Admin' || $rol->hasPermissionTo('tenant.mantenimientos.planes.index'))
            ->each(fn ($rol) => $rol->givePermissionTo('tenant.mantenimientos.marcas.index'));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        foreach (self::MANTENIMIENTOS as $tabla => $p) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'MAR_Id')) {
                Schema::table($tabla, function (Blueprint $table) use ($p) {
                    $table->dropForeign("{$p}_FK_MARCA");
                    $table->dropColumn(['MAR_Id', "{$p}_EsGarantia"]);
                });
            }
        }

        Schema::table('reservacion', function (Blueprint $table) {
            $table->dropForeign('RES_FK_MARCA');
            $table->dropColumn('MAR_Id');
        });

        Schema::dropIfExists('marca_vehiculo');
        Permission::where('name', 'tenant.mantenimientos.marcas.index')->where('guard_name', self::GUARD)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
