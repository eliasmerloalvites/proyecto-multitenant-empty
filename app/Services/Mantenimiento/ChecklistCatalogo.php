<?php

namespace App\Services\Mantenimiento;

/**
 * Catalogo fijo de items de checklist por tipo de mantenimiento, tal como
 * existen hoy como columnas en cada tabla (MGC_Det1, MGI_Det1, etc.). No es
 * editable por el usuario: los "planes" (mantenimiento_plan) simplemente
 * eligen un subconjunto de estos codigos para mostrar en el formulario.
 *
 * El codigo (ej. "Det8") es el sufijo que se le pega al prefijo del tipo
 * para armar el nombre real de la columna/input (MGC_Det8, MGI_Det8, ...).
 */
class ChecklistCatalogo
{
    const TIPOS = [
        'MGC' => 'Mantenimiento General (Carburada)',
        'MGI' => 'Mantenimiento General (Inyectada)',
        'MPC' => 'Mantenimiento Preventivo (Carburada)',
        'MPI' => 'Mantenimiento Preventivo (Inyectada)',
    ];

    public static function items(string $tipo): array
    {
        return match ($tipo) {
            'MGC' => [
                ['codigo' => 'Det1', 'etiqueta' => 'Cambio de aceite'],
                ['codigo' => 'Det2', 'etiqueta' => 'Cambio de filtro de aceite'],
                ['codigo' => 'Det3', 'etiqueta' => 'Limpieza de chasis con aire comp.'],
                ['codigo' => 'Det4', 'etiqueta' => 'Limpieza de cables elec. con aire comp.'],
                ['codigo' => 'Det5', 'etiqueta' => 'Desengrase interno de la unidad'],
                ['codigo' => 'Det6', 'etiqueta' => 'Mantenimiento de filtro de aire'],
                ['codigo' => 'Det7', 'etiqueta' => 'Mantenimiento de carburador'],
                ['codigo' => 'Det8', 'etiqueta' => 'Ajuste de válvulas'],
                ['codigo' => 'Det9', 'etiqueta' => 'Revisión y calibración de bujía'],
                ['codigo' => 'Det10', 'etiqueta' => 'Ajuste de la brida del tubo de escape'],
                ['codigo' => 'Det11', 'etiqueta' => 'Lavado y ajuste del sistema de arrastre'],
                ['codigo' => 'Det12', 'etiqueta' => 'Mantenimiento de freno delantero'],
                ['codigo' => 'Det13', 'etiqueta' => 'Mantenimiento de freno posterior'],
                ['codigo' => 'Det14', 'etiqueta' => 'Ajuste de pernos de chasis'],
                ['codigo' => 'Det15', 'etiqueta' => 'Limpieza de conectores eléctricos'],
                ['codigo' => 'Det16', 'etiqueta' => 'Presión de neumático delantero'],
                ['codigo' => 'Det17', 'etiqueta' => 'Presión de neumático posterior'],
                ['codigo' => 'Det18', 'etiqueta' => 'Revisión y test de líquido de frenos'],
                ['codigo' => 'Det19', 'etiqueta' => 'Revisión del sistema de enfriamiento'],
                ['codigo' => 'Det20', 'etiqueta' => 'Lubricación del sistema de arrastre'],
                ['codigo' => 'Det21', 'etiqueta' => 'Test de batería'],
            ],
            'MGI' => [
                ['codigo' => 'Det1', 'etiqueta' => 'Cambio de aceite'],
                ['codigo' => 'Det2', 'etiqueta' => 'Cambio de filtro de aceite'],
                ['codigo' => 'Det3', 'etiqueta' => 'Limpieza de chasis con aire comp.'],
                ['codigo' => 'Det4', 'etiqueta' => 'Limpieza de cables elec. con aire comp.'],
                ['codigo' => 'Det5', 'etiqueta' => 'Desengrase interno de la unidad'],
                ['codigo' => 'Det6', 'etiqueta' => 'Mantenimiento de filtro de aire'],
                ['codigo' => 'Det7', 'etiqueta' => 'Limpieza del cuerpo de aceleración'],
                ['codigo' => 'Det8', 'etiqueta' => 'Lavado de inyector en ultrasonido'],
                ['codigo' => 'Det9', 'etiqueta' => 'Ajuste de válvulas'],
                ['codigo' => 'Det10', 'etiqueta' => 'Revisión y calibración de bujía'],
                ['codigo' => 'Det11', 'etiqueta' => 'Medición de compresión del motor'],
                ['codigo' => 'Det12', 'etiqueta' => 'Ajuste de la brida del tubo de escape'],
                ['codigo' => 'Det13', 'etiqueta' => 'Lavado y ajuste del sistema de arrastre'],
                ['codigo' => 'Det14', 'etiqueta' => 'Mantenimiento de freno delantero'],
                ['codigo' => 'Det15', 'etiqueta' => 'Mantenimiento de freno posterior'],
                ['codigo' => 'Det16', 'etiqueta' => 'Ajuste de pernos de chasis'],
                ['codigo' => 'Det17', 'etiqueta' => 'Limpieza de conectores eléctricos'],
                ['codigo' => 'Det18', 'etiqueta' => 'Presión de neumático delantero'],
                ['codigo' => 'Det19', 'etiqueta' => 'Presión de neumático posterior'],
                ['codigo' => 'Det20', 'etiqueta' => 'Revisión y test de líquido de frenos'],
                ['codigo' => 'Det21', 'etiqueta' => 'Revisión y test de líquido refrigerante'],
                ['codigo' => 'Det22', 'etiqueta' => 'Revisión del sistema de enfriamiento'],
                ['codigo' => 'Det23', 'etiqueta' => 'Lubricación del sistema de arrastre'],
                ['codigo' => 'Det24', 'etiqueta' => 'Test de batería'],
                ['codigo' => 'Det25', 'etiqueta' => 'Limpieza del sensor de oxígeno'],
                ['codigo' => 'Det26', 'etiqueta' => 'Escaneo'],
                ['codigo' => 'Det27', 'etiqueta' => 'Verificación del sistema de luces'],
            ],
            'MPC' => [
                ['codigo' => 'Det1', 'etiqueta' => 'Cambio de aceite'],
                ['codigo' => 'Det2', 'etiqueta' => 'Cambio de filtro de aceite'],
                ['codigo' => 'Det3', 'etiqueta' => 'Limpieza de chasis con aire comp.'],
                ['codigo' => 'Det4', 'etiqueta' => 'Limpieza de cables elec. con aire comp.'],
                ['codigo' => 'Det5', 'etiqueta' => 'Mantenimiento de filtro de aire'],
                ['codigo' => 'Det6', 'etiqueta' => 'Mantenimiento de carburador'],
                ['codigo' => 'Det7', 'etiqueta' => 'Ajuste de válvulas'],
                ['codigo' => 'Det8', 'etiqueta' => 'Revisión y calibración de bujía'],
                ['codigo' => 'Det9', 'etiqueta' => 'Presión de neumático delantero'],
                ['codigo' => 'Det10', 'etiqueta' => 'Presión de neumático posterior'],
                ['codigo' => 'Det11', 'etiqueta' => 'Test de batería'],
            ],
            'MPI' => [
                ['codigo' => 'Det1', 'etiqueta' => 'Cambio de aceite'],
                ['codigo' => 'Det2', 'etiqueta' => 'Cambio de filtro de aceite'],
                ['codigo' => 'Det3', 'etiqueta' => 'Limpieza de chasis con aire comp.'],
                ['codigo' => 'Det4', 'etiqueta' => 'Limpieza de cables elec. con aire comp.'],
                ['codigo' => 'Det5', 'etiqueta' => 'Desengrase interno de la unidad'],
                ['codigo' => 'Det6', 'etiqueta' => 'Mantenimiento de filtro de aire'],
                ['codigo' => 'Det7', 'etiqueta' => 'Ajuste de válvulas'],
                ['codigo' => 'Det8', 'etiqueta' => 'Revisión y calibración de bujía'],
                ['codigo' => 'Det9', 'etiqueta' => 'Ajuste de la brida del tubo de escape'],
                ['codigo' => 'Det10', 'etiqueta' => 'Lavado y ajuste del sistema de arrastre'],
                ['codigo' => 'Det11', 'etiqueta' => 'Mantenimiento de freno delantero'],
                ['codigo' => 'Det12', 'etiqueta' => 'Mantenimiento de freno posterior'],
                ['codigo' => 'Det13', 'etiqueta' => 'Ajuste de pernos de chasis'],
                ['codigo' => 'Det14', 'etiqueta' => 'Limpieza de conectores eléctricos'],
                ['codigo' => 'Det15', 'etiqueta' => 'Presión de neumático delantero'],
                ['codigo' => 'Det16', 'etiqueta' => 'Presión de neumático posterior'],
                ['codigo' => 'Det17', 'etiqueta' => 'Revisión del sistema de enfriamiento'],
                ['codigo' => 'Det18', 'etiqueta' => 'Lubricación del sistema de arrastre'],
                ['codigo' => 'Det19', 'etiqueta' => 'Medición de voltaje de carga de batería'],
                ['codigo' => 'Det20', 'etiqueta' => 'Verificación del sistema de luces'],
            ],
            default => [],
        };
    }
}
