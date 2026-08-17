<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoTemaToEmpresaFacturacionTable extends Migration
{
    /**
     * La migración original de empresa_facturacion para 'generico' nunca
     * incluyó tipo_tema (a diferencia de la de 'tallermoto'), así que el
     * panel de este vertical no podía alternar modo claro/oscuro. Se agrega
     * acá, y también los campos de portada/color que 'tallermoto' sí tiene,
     * para que ambos verticales queden alineados en su esquema de branding.
     */
    public function up(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            // Sin ->after(...): algunos tenants de prueba antiguos tienen el
            // esquema desactualizado (les falta incluso logo_pdf), así que no
            // se puede depender de la posición de una columna en particular.
            $table->string('logo_portada1')->nullable();
            $table->string('logo_portada2')->nullable();

            $table->enum('tipo_tema', ['dark', 'light'])->default('light');
            $table->string('color_main', 7)->default('#3b82f6');
            $table->string('color_light', 7)->default('#60a5fa');
            $table->string('color_bg', 7)->default('#f8fafc');
            $table->string('color_card', 7)->default('#ffffff');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->dropColumn(['logo_portada1', 'logo_portada2', 'tipo_tema', 'color_main', 'color_light', 'color_bg', 'color_card']);
        });
    }
}
