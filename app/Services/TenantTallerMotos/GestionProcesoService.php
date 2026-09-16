<?php

namespace App\Services\TenantTallerMotos;

use App\Models\TenantTallerMotos\MantenimientoActividadVariada;
use App\Models\TenantTallerMotos\MantenimientoGeneralCarburada;
use App\Models\TenantTallerMotos\MantenimientoGeneralInyectada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoCarburada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoInyectada;
use App\Models\TenantTallerMotos\RecepcionCategoria;
use App\Models\TenantTallerMotos\RecepcionItem;
use App\Models\TenantTallerMotos\RecepcionObservacion;
use App\Models\TenantTallerMotos\RecepcionRespuesta;
use App\Models\TenantTallerMotos\Reservacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * "Gestion de Proceso": el tablero del dia a dia donde recepcion hace el
 * check-in de una reserva (llena el detalle real de trabajo y asigna
 * mecanico) y el mecanico ve lo suyo. Junta los 5 tipos de mantenimiento
 * (igual que MotoController::queryUnificada, pero orientado a "hoy" en vez
 * de al historial por placa) y centraliza la logica de crear/actualizar el
 * registro de mantenimiento ligado a una reserva.
 */
class GestionProcesoService
{
    /**
     * Metadata de cada tipo: tabla, prefijo de columnas, modelo Eloquent y
     * la ruta .edit del formulario detallado (checklist/repuestos/fotos)
     * al que el tablero enlaza para "ver todo".
     */
    public const TIPOS = [
        'MANTENIMIENTO GENERAL INYECTADA' => [
            'tabla' => 'mantenimiento_general_inyectada',
            'prefijo' => 'MGI',
            'modelo' => MantenimientoGeneralInyectada::class,
            'ruta_edit' => 'tenant.mantenimientos.generalinyectada.edit',
            'ruta_param' => 'generalinyectada',
            'etiqueta' => 'General Inyectada',
        ],
        'MANTENIMIENTO GENERAL CARBURADA' => [
            'tabla' => 'mantenimiento_general_carburada',
            'prefijo' => 'MGC',
            'modelo' => MantenimientoGeneralCarburada::class,
            'ruta_edit' => 'tenant.mantenimientos.generalcarburada.edit',
            'ruta_param' => 'generalcarburada',
            'etiqueta' => 'General Carburada',
        ],
        'MANTENIMIENTO PREVENTIVO INYECTADA' => [
            'tabla' => 'mantenimiento_preventivo_inyectada',
            'prefijo' => 'MPI',
            'modelo' => MantenimientoPreventivoInyectada::class,
            'ruta_edit' => 'tenant.mantenimientos.preventivoinyectada.edit',
            'ruta_param' => 'preventivoinyectada',
            'etiqueta' => 'Preventivo Inyectada',
        ],
        'MANTENIMIENTO PREVENTIVO CARBURADA' => [
            'tabla' => 'mantenimiento_preventivo_carburada',
            'prefijo' => 'MPC',
            'modelo' => MantenimientoPreventivoCarburada::class,
            'ruta_edit' => 'tenant.mantenimientos.preventivocarburada.edit',
            'ruta_param' => 'preventivocarburada',
            'etiqueta' => 'Preventivo Carburada',
        ],
        'ACTIVIDAD VARIADA' => [
            'tabla' => 'mantenimiento_actividad_variadas',
            'prefijo' => 'MAV',
            'modelo' => MantenimientoActividadVariada::class,
            'ruta_edit' => 'tenant.actividades.mantenimientoactividadvariada.edit',
            'ruta_param' => 'mantenimientoactividadvariada',
            'etiqueta' => 'Actividad Variada',
        ],
    ];

    /**
     * Busca si una reserva ya tiene un registro de mantenimiento (en
     * cualquiera de los 5 tipos). Devuelve la fila cruda + su metadata de
     * tipo, o null si la reserva todavia no tiene nada (solo agendada).
     */
    public static function mantenimientoDeReserva(int $resId): ?array
    {
        foreach (self::TIPOS as $tipo => $meta) {
            $registro = DB::table($meta['tabla'])->where('RES_Id', $resId)->first();

            if ($registro) {
                return array_merge($meta, ['tipo' => $tipo, 'registro' => $registro]);
            }
        }

        return null;
    }

    /**
     * Check-in de recepcion: crea el registro de mantenimiento si la
     * reserva todavia no tenia uno, o lo actualiza si ya existia (por
     * ejemplo, si se eligio el tipo al reservar con anticipacion). Marca
     * RES_CheckIn la primera vez (la moto ya esta fisicamente en el
     * taller) y activa el aviso al mecanico si recepcion cambia el detalle
     * DESPUES de que el mecanico ya tenia el trabajo asignado.
     *
     * @throws Exception si la reserva ya tiene un mantenimiento de un tipo distinto.
     */
    public static function checkIn(Reservacion $reserva, string $tipoMantenimiento, string $detalleIngreso, int $mecanicoId): array
    {
        if (!array_key_exists($tipoMantenimiento, self::TIPOS)) {
            throw new Exception('Tipo de mantenimiento no valido.');
        }

        $meta = self::TIPOS[$tipoMantenimiento];
        $existente = self::mantenimientoDeReserva($reserva->RES_Id);
        $eraPrimerCheckIn = $reserva->RES_CheckIn === null;

        if ($existente && $existente['tipo'] !== $tipoMantenimiento) {
            throw new Exception('Esta reserva ya tiene un mantenimiento de tipo "' . $existente['etiqueta'] . '" asignado; no se puede cambiar el tipo desde aqui.');
        }

        if ($existente) {
            $registro = $existente['registro'];
            $prefijo = $meta['prefijo'];
            $idColumna = $prefijo . '_Id';
            $detalleAnterior = trim((string) $registro->{$prefijo . '_DetalleIngreso'});
            $yaEstabaAsignado = $registro->PER_Id !== null && !$eraPrimerCheckIn;
            $cambioDetalle = $detalleAnterior !== trim($detalleIngreso);

            DB::table($meta['tabla'])->where($idColumna, $registro->{$idColumna})->update([
                $prefijo . '_DetalleIngreso' => $detalleIngreso,
                $prefijo . '_FechaEdicion' => now(),
                $prefijo . '_UsuarioEditado' => Auth::id(),
                'PER_Id' => $mecanicoId,
                $prefijo . '_AvisoMecanico' => ($cambioDetalle && $yaEstabaAsignado) ? 1 : $registro->{$prefijo . '_AvisoMecanico'},
            ]);
        } else {
            self::crearDesdeReserva($tipoMantenimiento, $reserva, $detalleIngreso, $mecanicoId);
        }

        if ($eraPrimerCheckIn) {
            $reserva->update(['RES_CheckIn' => now()]);
        }

        return self::mantenimientoDeReserva($reserva->RES_Id);
    }

    /**
     * El mecanico confirma que vio el cambio que recepcion agrego despues
     * de la asignacion. Accion explicita (boton "Entendido"), no se apaga
     * solo con abrir el registro: asi queda constancia de que si se
     * entero, no que el sistema lo dio por hecho.
     */
    public static function marcarEntendido(string $tabla, int $id): void
    {
        $meta = collect(self::TIPOS)->firstWhere('tabla', $tabla);

        if (!$meta) {
            throw new Exception('Tipo de mantenimiento no valido.');
        }

        $idColumna = $meta['prefijo'] . '_Id';

        DB::table($tabla)->where($idColumna, $id)->update([
            $meta['prefijo'] . '_AvisoMecanico' => 0,
        ]);
    }

    /**
     * Crea el registro de mantenimiento del tipo elegido, con el checklist
     * en blanco (se completa cuando se atiende la moto de verdad). Cubre
     * los 5 tipos existentes. $detalleIngreso es el texto real de "que hay
     * que hacer" (lo escribe recepcion al check-in, o el resumen armado al
     * reservar con anticipacion si se eligio tipo desde ahi).
     */
    public static function crearDesdeReserva(string $tipoMantenimiento, Reservacion $reserva, string $detalleIngreso, ?int $mecanicoId = null): void
    {
        $mytime = Carbon::now('America/Lima');
        $idusu = Auth::id();
        $idper = $mecanicoId ?? Auth::id();

        $placa = $reserva->RES_Placa;
        $propietario = $reserva->RES_Cliente;
        $celular = $reserva->RES_Celular;
        $unidad = $reserva->RES_Moto;
        $detalleObservacion = $reserva->RES_Detalle;

        if ($tipoMantenimiento === 'MANTENIMIENTO GENERAL INYECTADA') {
            $mtto = new MantenimientoGeneralInyectada;
            $mtto->MGI_Placa = $placa;
            $mtto->MGI_Propietario = $propietario;
            $mtto->MGI_celular = $celular;
            $mtto->MGI_Unidad = $unidad;
            $mtto->MGI_KMEntrada = "";
            $mtto->MGI_DetalleIngreso = $detalleIngreso;
            $mtto->MGI_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 27) as $n) {
                $mtto->{"MGI_Det{$n}"} = "NO";
            }
            $mtto->MGI_Det1Informacion = "";
            $mtto->MGI_Det9Admision = "";
            $mtto->MGI_Det9Escape = "";
            $mtto->MGI_Det10Medida = "";
            $mtto->MGI_Det11Medida = "";
            $mtto->MGI_Det18 = "";
            $mtto->MGI_Det19 = "";
            $mtto->MGI_Det20Humedad = "";
            $mtto->MGI_Det22Ventilador = "";
            $mtto->MGI_Det24Vida = "";
            $mtto->MGI_Det24Carga = "";
            $mtto->MGI_Det24Arranque = "";
            $mtto->MGI_DetalleRealizado = "";
            $mtto->MGI_CorrecionObservacion = "";
            $mtto->MGI_ProximoCambioAceite = "";
            $mtto->MGI_ProximoServicio = "";
            $mtto->MGI_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MGI_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MGI_UsuarioCreacion = $idusu;
            $mtto->MGI_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'MANTENIMIENTO PREVENTIVO INYECTADA') {
            $mtto = new MantenimientoPreventivoInyectada;
            $mtto->MPI_Placa = $placa;
            $mtto->MPI_Propietario = $propietario;
            $mtto->MPI_celular = $celular;
            $mtto->MPI_Unidad = $unidad;
            $mtto->MPI_KMEntrada = "";
            $mtto->MPI_DetalleIngreso = $detalleIngreso;
            $mtto->MPI_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 20) as $n) {
                $mtto->{"MPI_Det{$n}"} = "NO";
            }
            $mtto->MPI_Det1Informacion = "";
            $mtto->MPI_Det7Admision = "";
            $mtto->MPI_Det7Escape = "";
            $mtto->MPI_Det8Medida = "";
            $mtto->MPI_Det15 = "";
            $mtto->MPI_Det16 = "";
            $mtto->MPI_Det17Ventilador = "";
            $mtto->MPI_Det19Vida = "";
            $mtto->MPI_Det19Carga = "";
            $mtto->MPI_Det19Arranque = "";
            $mtto->MPI_DetalleRealizado = "";
            $mtto->MPI_CorrecionObservacion = "";
            $mtto->MPI_ProximoCambioAceite = "";
            $mtto->MPI_ProximoServicio = "";
            $mtto->MPI_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MPI_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MPI_UsuarioCreacion = $idusu;
            $mtto->MPI_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'MANTENIMIENTO GENERAL CARBURADA') {
            $mtto = new MantenimientoGeneralCarburada;
            $mtto->MGC_Placa = $placa;
            $mtto->MGC_Propietario = $propietario;
            $mtto->MGC_celular = $celular;
            $mtto->MGC_Unidad = $unidad;
            $mtto->MGC_KMEntrada = "";
            $mtto->MGC_DetalleIngreso = $detalleIngreso;
            $mtto->MGC_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 21) as $n) {
                $mtto->{"MGC_Det{$n}"} = "NO";
            }
            $mtto->MGC_Det1Informacion = "";
            $mtto->MGC_Det8Admision = "";
            $mtto->MGC_Det8Escape = "";
            $mtto->MGC_Det9Medida = "";
            $mtto->MGC_Det16 = "";
            $mtto->MGC_Det17 = "";
            $mtto->MGC_Det18Humedad = "";
            $mtto->MGC_Det19Ventilador = "";
            $mtto->MGC_Det21Vida = "";
            $mtto->MGC_Det21Carga = "";
            $mtto->MGC_Det21Arranque = "";
            $mtto->MGC_DetalleRealizado = "";
            $mtto->MGC_CorrecionObservacion = "";
            $mtto->MGC_ProximoCambioAceite = "";
            $mtto->MGC_ProximoServicio = "";
            $mtto->MGC_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MGC_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MGC_UsuarioCreacion = $idusu;
            $mtto->MGC_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'MANTENIMIENTO PREVENTIVO CARBURADA') {
            $mtto = new MantenimientoPreventivoCarburada;
            $mtto->MPC_Placa = $placa;
            $mtto->MPC_Propietario = $propietario;
            $mtto->MPC_celular = $celular;
            $mtto->MPC_Unidad = $unidad;
            $mtto->MPC_KMEntrada = "";
            $mtto->MPC_DetalleIngreso = $detalleIngreso;
            $mtto->MPC_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 11) as $n) {
                $mtto->{"MPC_Det{$n}"} = "NO";
            }
            $mtto->MPC_Det1Informacion = "";
            $mtto->MPC_Det7Admision = "";
            $mtto->MPC_Det7Escape = "";
            $mtto->MPC_Det8Medida = "";
            $mtto->MPC_Det11Vida = "";
            $mtto->MPC_Det11Carga = "";
            $mtto->MPC_Det11Arranque = "";
            $mtto->MPC_DetalleRealizado = "";
            $mtto->MPC_CorrecionObservacion = "";
            $mtto->MPC_ProximoCambioAceite = "";
            $mtto->MPC_ProximoServicio = "";
            $mtto->MPC_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MPC_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MPC_UsuarioCreacion = $idusu;
            $mtto->MPC_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'ACTIVIDAD VARIADA') {
            $mtto = new MantenimientoActividadVariada;
            $mtto->MAV_Placa = $placa;
            $mtto->MAV_Propietario = $propietario;
            $mtto->MAV_celular = $celular;
            $mtto->MAV_Unidad = $unidad;
            $mtto->MAV_KMEntrada = "";
            $mtto->MAV_DetalleIngreso = $detalleIngreso;
            $mtto->MAV_DetalleObservacion = $detalleObservacion;
            $mtto->MAV_DetalleRealizado = "";
            $mtto->MAV_CorrecionObservacion = "";
            $mtto->MAV_ProximoCambioAceite = "";
            $mtto->MAV_ProximoServicio = "";
            $mtto->MAV_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MAV_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MAV_UsuarioCreacion = $idusu;
            $mtto->MAV_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        }
    }

    /**
     * Valida que ($tabla, $id) sea un mantenimiento real de alguno de los
     * 5 tipos. Se usa antes de leer/guardar su Estado de Recepcion.
     *
     * @throws Exception si la tabla no es una de las 5 validas o el id no existe ahi.
     */
    public static function validarMantenimiento(string $tabla, int $id): array
    {
        $meta = collect(self::TIPOS)->firstWhere('tabla', $tabla);

        if (!$meta) {
            throw new Exception('Tipo de mantenimiento no valido.');
        }

        $idColumna = $meta['prefijo'] . '_Id';
        $registro = DB::table($tabla)->where($idColumna, $id)->first();

        if (!$registro) {
            throw new Exception('El mantenimiento indicado no existe.');
        }

        return array_merge($meta, ['registro' => $registro]);
    }

    /**
     * Estado de Recepcion de un mantenimiento puntual: categorias/items
     * activos (agrupados por RCT_Grupo/categoria) + las respuestas y
     * observaciones ya guardadas para ese ($tabla, $id), si las hay. Los
     * items ya inactivos no aparecen aqui aunque el mantenimiento tenga
     * una respuesta vieja guardada para ellos (esta funcion es para
     * pintar el formulario editable, no el historico crudo).
     */
    public static function estadoRecepcion(string $tabla, int $id): array
    {
        self::validarMantenimiento($tabla, $id);

        $categorias = RecepcionCategoria::activas()->with(['items' => fn ($q) => $q->activos()])->get();

        $respuestas = RecepcionRespuesta::deMantenimiento($tabla, $id)->pluck('RRP_Valor', 'RIT_Id');
        $observaciones = RecepcionObservacion::deMantenimiento($tabla, $id)->get()->keyBy(fn ($o) => $o->RCT_Id ?? 'general');

        return [
            'categorias' => $categorias,
            'respuestas' => $respuestas,
            'observaciones' => $observaciones,
        ];
    }

    /**
     * Guarda el Estado de Recepcion de un mantenimiento puntual. No
     * depende de que haya existido un check-in ni de una reserva: sirve
     * igual para un mantenimiento creado por check-in, por aprobacion de
     * reserva sin check-in, o creado directo (walk-in, sin reserva).
     *
     * $respuestas: [RIT_Id => valor, ...]
     * $observaciones: [RCT_Id o 'general' => texto, ...]
     *
     * @throws Exception si $tabla/$id no son un mantenimiento real, o si
     *                    algun RIT_Id/RCT_Id no existe o esta inactivo.
     */
    public static function guardarEstadoRecepcion(string $tabla, int $id, array $respuestas, array $observaciones): void
    {
        self::validarMantenimiento($tabla, $id);

        $itemsActivos = RecepcionItem::activos()->pluck('RIT_Id')->flip();
        $categoriasActivas = RecepcionCategoria::activas()->pluck('RCT_Id')->flip();

        DB::transaction(function () use ($tabla, $id, $respuestas, $observaciones, $itemsActivos, $categoriasActivas) {
            foreach ($respuestas as $itemId => $valor) {
                if (!isset($itemsActivos[$itemId])) {
                    throw new Exception("El item de recepcion #{$itemId} no existe o esta inactivo.");
                }

                RecepcionRespuesta::updateOrCreate(
                    ['MTO_Tabla' => $tabla, 'MTO_Id' => $id, 'RIT_Id' => $itemId],
                    ['RRP_Valor' => $valor]
                );
            }

            foreach ($observaciones as $categoriaId => $texto) {
                $esGeneral = $categoriaId === 'general' || $categoriaId === null;

                if (!$esGeneral && !isset($categoriasActivas[$categoriaId])) {
                    throw new Exception("La categoria de recepcion #{$categoriaId} no existe o esta inactiva.");
                }

                RecepcionObservacion::updateOrCreate(
                    ['MTO_Tabla' => $tabla, 'MTO_Id' => $id, 'RCT_Id' => $esGeneral ? null : $categoriaId],
                    ['ROB_Texto' => $texto]
                );
            }
        });
    }

    /**
     * Datos de facturacion (documento del cliente + forma de pago) de la
     * venta real asociada a la reserva de este mantenimiento, si es que
     * llegaron a cobrar la bahia. Cadena: reservacion -> bahia_cuenta
     * (solo se llena VEN_Id al "Cobrar" desde Ventas por Bahia) -> venta ->
     * cliente/metodo_pago.
     *
     * Devuelve null (nunca datos a medias o inventados) cuando el
     * mantenimiento no vino de una reserva, cuando esa reserva no llego a
     * cobrarse por bahia, o cuando la cuenta se cerro sin cobro.
     */
    public static function datosVentaAsociada(?int $resId): ?array
    {
        if (!$resId) {
            return null;
        }

        $venta = DB::table('bahia_cuenta as bc')
            ->join('venta as v', 'v.VEN_Id', '=', 'bc.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->leftJoin('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
            ->where('bc.RES_Id', $resId)
            ->whereNotNull('bc.VEN_Id')
            ->orderByDesc('bc.BCT_Id')
            ->select(
                'c.CLI_TipoDocumento',
                'c.CLI_NumDocumento',
                'c.CLI_Nombre',
                'mp.MEP_Pago'
            )
            ->first();

        return $venta ? (array) $venta : null;
    }
}
