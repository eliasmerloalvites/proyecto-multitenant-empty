<?php

namespace Database\Seeders\Tenant\tallermoto;

use App\Models\TenantTallerMotos\RecepcionCategoria;
use App\Models\TenantTallerMotos\RecepcionItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Punto de partida editable del "Estado de Recepcion de la Motocicleta":
 * categorias e items tal como los pidio el usuario (basado en su ficha de
 * recepcion fisica). Todo esto se puede renombrar/reordenar/desactivar o
 * ampliar despues desde Configuracion > Estado de Recepcion sin tocar
 * codigo — este seeder no es la fuente de verdad, solo la carga inicial.
 *
 * Usa firstOrCreate (no ids fijos): correrlo de nuevo no duplica ni
 * rompe nada si ya existen las categorias/items.
 */
class RecepcionCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $grupos = [
            [
                'grupo' => RecepcionCategoria::GRUPO_INVENTARIO,
                'categorias' => [
                    [
                        'nombre' => 'Elementos Generales',
                        'items' => $this->booleanos([
                            'Arranca',
                            'Luz de Check',
                            'Asiento(s)',
                            'Faro Delantero',
                            'Luz Trasera',
                            'Tapa Tanque Combustible',
                            'Direccionales',
                            'Parabrisas',
                            'Espejos',
                            'Palancas',
                            'Llaves',
                            'Herramienta',
                            'Cuadro de Instrumentos',
                            'Manual de Usuario',
                            'Manual de Garantía',
                            'Vehículo Sucio',
                        ]),
                    ],
                    [
                        'nombre' => 'Datos de Ingreso',
                        'items' => [
                            ['etiqueta' => 'Nivel de Combustible', 'tipo' => RecepcionItem::TIPO_SELECT, 'opciones' => ['1/4', '1/2', '3/4', 'Full']],
                            ['etiqueta' => 'Nivel de Aceite', 'tipo' => RecepcionItem::TIPO_SELECT, 'opciones' => ['Mínimo', 'Máximo']],
                        ],
                    ],
                    [
                        'nombre' => 'Parte Delantera',
                        'items' => $this->status([
                            'Tablero',
                            'Retrovisor',
                            'Tapa Tanque Combustible (Estado)',
                            'Luces Delanteras',
                            'Direccionales Delanteras',
                            'Strino',
                            'Guardabarro Delantero',
                            'Llanta Delantera',
                            'Rin Delantero',
                            'Freno Delantero',
                            'Suspensión Delantera',
                        ]),
                    ],
                    [
                        'nombre' => 'Parte Trasera',
                        'items' => $this->status([
                            'Parrilla',
                            'Luces de Freno',
                            'Direccionales Traseras',
                            'Guardabarro Trasero',
                            'Stop',
                            'Llanta Trasera',
                            'Rin Trasero',
                            'Kit de Arrastre',
                            'Suspensión Trasera',
                            'Freno Trasero',
                        ]),
                    ],
                    [
                        'nombre' => 'Motor y Transmisión (Inventario)',
                        'items' => $this->status([
                            'Aceite Motor',
                            'Batería',
                            'Caja de Cambios',
                            'Cadena / Catón',
                            'Gato',
                        ]),
                    ],
                ],
            ],
            [
                'grupo' => RecepcionCategoria::GRUPO_INSPECCION,
                'categorias' => [
                    [
                        'nombre' => 'Motor',
                        'items' => $this->inspeccion([
                            'Nivel de Aceite',
                            'Compresión',
                            'Fugas de Aceite',
                            'Filtro de Aceite',
                            'Filtro de Aire',
                            'Bujía',
                            'Carburador / Inyección',
                            'Refrigeración',
                            'Empaque',
                            'O-ring',
                            'Retén',
                        ]),
                    ],
                    [
                        'nombre' => 'Transmisión',
                        'items' => $this->inspeccion([
                            'Cadena / Correa',
                            'Piñón de Salida',
                            'Catalina / Corona',
                            'Tensor de Cadena',
                            'Embrague',
                            'Caja de Cambios',
                            'Faja',
                            'Polines',
                            'Fugas de Aceite',
                        ]),
                    ],
                    [
                        'nombre' => 'Suspensión',
                        'items' => $this->inspeccion([
                            'Suspensión Delantera',
                            'Suspensión Posterior',
                            'Retenes',
                            'Fugas de Aceite',
                        ]),
                    ],
                    [
                        'nombre' => 'Frenos',
                        'items' => $this->inspeccion([
                            'Pastillas Delanteras',
                            'Pastillas Traseras',
                            'Disco Delantero',
                            'Disco Trasero',
                            'Zapata Delantera',
                            'Zapata Trasera',
                            'Líquido de Frenos',
                            'Mangueras / Tuberías',
                            'Freno de Parqueo',
                        ]),
                    ],
                    [
                        'nombre' => 'Eléctrico',
                        'items' => $this->inspeccion([
                            'Batería',
                            'Sistema de Carga',
                            'Arranque Eléctrico',
                            'Luces Alta / Baja',
                            'Direccionales',
                            'Luz Trasera / Stop',
                            'Bocina',
                            'Tablero / Testigos',
                        ]),
                    ],
                    [
                        'nombre' => 'Chasis',
                        'items' => $this->inspeccion([
                            'Dirección',
                            'Rodamientos',
                            'Llantas',
                            'Rayos / Rines',
                            'Presión de Llantas',
                            'Soportes / Parrilla',
                            'Fugas de Combustible',
                        ]),
                    ],
                ],
            ],
        ];

        foreach ($grupos as $grupoDef) {
            foreach ($grupoDef['categorias'] as $ordenCat => $categoriaDef) {
                $categoria = RecepcionCategoria::firstOrCreate(
                    ['RCT_Nombre' => $categoriaDef['nombre']],
                    ['RCT_Grupo' => $grupoDef['grupo'], 'RCT_Orden' => $ordenCat, 'RCT_Activo' => true]
                );

                foreach ($categoriaDef['items'] as $ordenItem => $itemDef) {
                    $codigo = Str::slug($categoriaDef['nombre'] . ' ' . $itemDef['etiqueta'], '_');

                    RecepcionItem::firstOrCreate(
                        ['RIT_Codigo' => $codigo],
                        [
                            'RCT_Id' => $categoria->RCT_Id,
                            'RIT_Etiqueta' => $itemDef['etiqueta'],
                            'RIT_TipoCampo' => $itemDef['tipo'],
                            'RIT_Opciones' => $itemDef['opciones'] ?? null,
                            'RIT_Orden' => $ordenItem,
                            'RIT_Activo' => true,
                        ]
                    );
                }
            }
        }
    }

    private function booleanos(array $etiquetas): array
    {
        return array_map(fn ($e) => ['etiqueta' => $e, 'tipo' => RecepcionItem::TIPO_BOOLEAN], $etiquetas);
    }

    private function status(array $etiquetas): array
    {
        return array_map(fn ($e) => ['etiqueta' => $e, 'tipo' => RecepcionItem::TIPO_STATUS], $etiquetas);
    }

    private function inspeccion(array $etiquetas): array
    {
        return array_map(fn ($e) => ['etiqueta' => $e, 'tipo' => RecepcionItem::TIPO_INSPECTION], $etiquetas);
    }
}
