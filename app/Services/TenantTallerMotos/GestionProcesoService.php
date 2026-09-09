<?php

namespace App\Services\TenantTallerMotos;

use App\Models\TenantTallerMotos\MantenimientoActividadVariada;
use App\Models\TenantTallerMotos\MantenimientoGeneralCarburada;
use App\Models\TenantTallerMotos\MantenimientoGeneralInyectada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoCarburada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoInyectada;
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
}
