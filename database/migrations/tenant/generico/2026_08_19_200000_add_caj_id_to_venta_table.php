<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->unsignedInteger('CAJ_Id')->nullable()->after('ALM_Id');
            $table->foreign('CAJ_Id', 'VEN_KFR4')->references('CAJ_Id')->on('caja')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropForeign('VEN_KFR4');
            $table->dropColumn('CAJ_Id');
        });
    }
};
