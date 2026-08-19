<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compra', function (Blueprint $table) {
            $table->unsignedInteger('CAJ_Id')->nullable()->after('PROV_Id');
            $table->foreign('CAJ_Id', 'COM_KFR4')->references('CAJ_Id')->on('caja')->nullOnDelete();
        });

        Schema::table('gasto', function (Blueprint $table) {
            $table->unsignedInteger('CAJ_Id')->nullable()->after('ALM_Id');
            $table->foreign('CAJ_Id', 'GAS_KFR6')->references('CAJ_Id')->on('caja')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('compra', function (Blueprint $table) {
            $table->dropForeign('COM_KFR4');
            $table->dropColumn('CAJ_Id');
        });

        Schema::table('gasto', function (Blueprint $table) {
            $table->dropForeign('GAS_KFR6');
            $table->dropColumn('CAJ_Id');
        });
    }
};
