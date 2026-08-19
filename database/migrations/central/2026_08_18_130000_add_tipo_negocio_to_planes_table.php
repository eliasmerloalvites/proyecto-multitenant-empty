<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTipoNegocioToPlanesTable extends Migration
{
    /**
     * Los planes (start/basic/plus/empresarial) dejan de ser globales: cada
     * tipo_negocio (tallermoto, generico, y los que se agreguen después)
     * tiene su propio precio/límites/módulos por plan. `key` deja de ser
     * PK única (ya no identifica un plan por sí sola) y pasa a formar,
     * junto con tipo_negocio, un índice único compuesto; el PK pasa a un
     * `id` autoincremental para que las rutas (route model binding) sigan
     * funcionando sin ambigüedad.
     */
    public function up(): void
    {
        Schema::table('planes', function (Blueprint $table) {
            $table->string('tipo_negocio', 30)->default('tallermoto')->after('key');
        });

        Schema::table('planes', function (Blueprint $table) {
            $table->dropPrimary('key');
        });

        Schema::table('planes', function (Blueprint $table) {
            $table->id()->first();
        });

        Schema::table('planes', function (Blueprint $table) {
            $table->unique(['tipo_negocio', 'key']);
        });

        // Filas existentes (creadas antes de este cambio) quedaron con
        // tipo_negocio='tallermoto' por el default de arriba. Se clonan
        // para 'generico' con los mismos valores base, para que el panel
        // ya tenga algo editable en ese vertical sin esperar un seeder.
        $planesTallermoto = DB::table('planes')->where('tipo_negocio', 'tallermoto')->get();

        foreach ($planesTallermoto as $plan) {
            $existeGenerico = DB::table('planes')
                ->where('tipo_negocio', 'generico')
                ->where('key', $plan->key)
                ->exists();

            if ($existeGenerico) {
                continue;
            }

            DB::table('planes')->insert([
                'tipo_negocio' => 'generico',
                'key' => $plan->key,
                'nombre' => $plan->nombre,
                'price' => $plan->price,
                'max_users' => $plan->max_users,
                'max_images' => $plan->max_images,
                'storage_limit_mb' => $plan->storage_limit_mb,
                'custom_domain_enabled' => $plan->custom_domain_enabled,
                'custom_branding' => $plan->custom_branding,
                'customizable' => $plan->customizable,
                'modules' => $plan->modules,
                'limits' => $plan->limits,
                'branding' => $plan->branding,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('planes', function (Blueprint $table) {
            $table->dropUnique(['tipo_negocio', 'key']);
        });

        DB::table('planes')->where('tipo_negocio', 'generico')->delete();

        Schema::table('planes', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('tipo_negocio');
        });

        Schema::table('planes', function (Blueprint $table) {
            $table->primary('key');
        });
    }
}
