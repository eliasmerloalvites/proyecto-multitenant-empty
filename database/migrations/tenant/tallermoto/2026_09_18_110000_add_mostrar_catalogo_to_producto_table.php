<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Controla si un producto aparece en el catálogo público de la web
     * (landing/pagina catalogo, ambas exclusivas de tallermoto — ver
     * HomeController::inicio()/catalogo()). Default true: los productos
     * existentes siguen mostrandose igual que hoy, sin desaparecer del
     * catalogo al aplicar esta migracion.
     */
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->boolean('PRO_MostrarCatalogo')->default(1)->after('PRO_Status');
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('PRO_MostrarCatalogo');
        });
    }
};
