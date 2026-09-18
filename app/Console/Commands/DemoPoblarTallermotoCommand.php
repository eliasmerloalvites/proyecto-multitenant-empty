<?php

namespace App\Console\Commands;

use App\Models\Tenant as CentralTenant;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\Caja;
use App\Models\Tenant\CajaSesion;
use App\Models\Tenant\Categoria;
use App\Models\Tenant\Clase;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Compra;
use App\Models\Tenant\Gasto;
use App\Models\Tenant\Lote;
use App\Models\Tenant\MetodoPago;
use App\Models\Tenant\Personal;
use App\Models\Tenant\Producto;
use App\Models\Tenant\Proveedor;
use App\Models\Tenant\TipoGasto;
use App\Models\Tenant\User;
use App\Models\Tenant\Venta;
use App\Models\TenantTallerMotos\Bahia;
use App\Models\TenantTallerMotos\MantenimientoActividadVariada;
use App\Models\TenantTallerMotos\MantenimientoGeneralCarburada;
use App\Models\TenantTallerMotos\MantenimientoGeneralInyectada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoCarburada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoInyectada;
use App\Models\TenantTallerMotos\RecepcionItem;
use App\Models\TenantTallerMotos\RecepcionRespuesta;
use App\Models\TenantTallerMotos\Reservacion;
use App\Models\TenantTallerMotos\Turno;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Puebla un tenant tallermoto con ~1 año de actividad realista (categorías,
 * productos, proveedores, clientes, compras, gastos, sesiones de caja,
 * reservas, mantenimientos de los 5 tipos con Estado de Recepción, ventas),
 * usando los mismos modelos Eloquent que los controladores reales en vez de
 * INSERTs crudos. Pensado para "vestir" un tenant demo casi vacío.
 *
 *   php artisan demo:poblar-tallermoto tallermoto_demo --confirmar
 */
class DemoPoblarTallermotoCommand extends Command
{
    protected $signature = 'demo:poblar-tallermoto {tenant} {--confirmar}';

    protected $description = 'Puebla un tenant tallermoto con un año de datos demo realistas (reservas, mantenimientos, compras, ventas, caja).';

    private const CATEGORIAS = [
        'Motor', 'Frenos', 'Transmisión', 'Suspensión', 'Iluminación',
        'Batería y Carga', 'Aceites y Lubricantes', 'Limpieza y Químicos',
        'Carrocería y Plásticos', 'Accesorios', 'Insumos de Taller',
        'Mano de Obra', 'Varios',
    ];

    private const PROVEEDORES = [
        ['Proveedor Genérico', '00000000'],
        ['Repuestos Lima Motos SAC', '20601234561'],
        ['Importadora Andina Motor Parts SAC', '20601234562'],
        ['Distribuidora El Rayo E.I.R.L.', '20601234563'],
        ['Lubricantes y Aceites del Sur SAC', '20601234564'],
    ];

    private const TIPOS_GASTO = [
        'Servicios Básicos', 'Alquiler', 'Transporte', 'Publicidad', 'Sueldos',
        'Mantenimiento', 'Compras Varias', 'Impuestos', 'Software', 'Otros Gastos',
    ];

    private const PRODUCTOS = [
        ['Disco de embrague Honda CG150', 'Honda', 'Motor', 30, 42],
        ['Kit de arrastre 428H', 'Genérico', 'Transmisión', 68, 95],
        ['Empaque completo de motor 150cc', 'Genérico', 'Motor', 18, 28],
        ['Juego de anillos de pistón STD 57mm', 'Genérico', 'Motor', 22, 32],
        ['Cable de embrague universal', 'Genérico', 'Transmisión', 8, 14],
        ['Cadena de transmisión 428H x120', 'Genérico', 'Transmisión', 48, 68],
        ['Pastillas de freno delantero universal', 'Genérico', 'Frenos', 16, 25],
        ['Pastillas de freno posterior universal', 'Genérico', 'Frenos', 16, 25],
        ['Disco de freno delantero 220mm', 'Genérico', 'Frenos', 58, 85],
        ['Líquido de frenos DOT 4 250ml', 'Genérico', 'Frenos', 9, 15],
        ['Bujía de encendido NGK estándar', 'NGK', 'Motor', 7, 12],
        ['Filtro de aceite Honda CG150', 'Honda', 'Aceites y Lubricantes', 6, 11],
        ['Aceite de motor 20W50 mineral 1L', 'Genérico', 'Aceites y Lubricantes', 12, 20],
        ['Aceite de motor semisintético 10W40 1L', 'Genérico', 'Aceites y Lubricantes', 18, 29],
        ['Filtro de aire Yamaha YBR125', 'Yamaha', 'Motor', 14, 22],
        ['Amortiguador trasero universal', 'Genérico', 'Suspensión', 45, 68],
        ['Horquilla delantera - juego de retenes', 'Genérico', 'Suspensión', 12, 20],
        ['Resortes de suspensión trasera', 'Genérico', 'Suspensión', 35, 52],
        ['Batería 12V 5Ah sellada', 'Genérico', 'Batería y Carga', 55, 82],
        ['Regulador de voltaje universal', 'Genérico', 'Batería y Carga', 28, 45],
        ['Bobina de encendido CDI universal', 'Genérico', 'Batería y Carga', 32, 50],
        ['Foco H4 12V 35/35W', 'Genérico', 'Iluminación', 6, 10],
        ['Direccional universal LED', 'Genérico', 'Iluminación', 8, 14],
        ['Foco de posición LED', 'Genérico', 'Iluminación', 4, 8],
        ['Silicona en spray abrillantador', 'Genérico', 'Limpieza y Químicos', 5, 9],
        ['Desengrasante de cadena 500ml', 'Genérico', 'Limpieza y Químicos', 8, 14],
        ['Shampoo para moto 1L', 'Genérico', 'Limpieza y Químicos', 6, 11],
        ['Espejo retrovisor universal (par)', 'Genérico', 'Carrocería y Plásticos', 15, 25],
        ['Guardafango delantero universal', 'Genérico', 'Carrocería y Plásticos', 22, 35],
        ['Manubrio cromado universal', 'Genérico', 'Carrocería y Plásticos', 18, 30],
        ['Funda de asiento universal', 'Genérico', 'Accesorios', 12, 20],
        ['Casco abierto certificado', 'Genérico', 'Accesorios', 45, 75],
        ['Candado de disco antirrobo', 'Genérico', 'Accesorios', 20, 35],
        ['Grasa multipropósito 500g', 'Genérico', 'Insumos de Taller', 8, 14],
        ['Trapo industrial (paquete)', 'Genérico', 'Insumos de Taller', 5, 9],
        ['Cinta aislante', 'Genérico', 'Insumos de Taller', 2, 4],
        ['Mano de obra - Cambio de aceite', 'Genérico', 'Mano de Obra', 0, 15],
        ['Mano de obra - Mantenimiento general', 'Genérico', 'Mano de Obra', 0, 45],
        ['Mano de obra - Cambio de pastillas', 'Genérico', 'Mano de Obra', 0, 20],
        ['Válvula de neumático', 'Genérico', 'Varios', 2, 5],
        ['Parche para neumático (juego)', 'Genérico', 'Varios', 6, 11],
    ];

    private const NOMBRES = ['María Fernanda', 'Luis Alberto', 'Rosa Elena', 'Jorge Luis', 'Karen Sofía', 'Carlos Eduardo', 'Ana Lucía', 'Miguel Ángel', 'Diana Carolina', 'José Manuel', 'Patricia Isabel', 'Fernando Javier', 'Gabriela Alexandra', 'Ricardo Andrés', 'Claudia Marisol', 'Víctor Hugo', 'Elizabeth Pilar', 'Daniel Alonso', 'Milagros Yesenia', 'Raúl Antonio', 'Cynthia Pamela', 'Jhonny Edgar', 'Vanessa Rocío', 'Marco Antonio', 'Katherine Soledad', 'Omar Iván', 'Rocío del Pilar', 'Alex Fabricio', 'Yesenia Mabel', 'Brian Anthony'];
    private const APELLIDOS = ['Gómez Ruiz', 'Torres Vega', 'Paredes Ríos', 'Huamán Castro', 'Flores Díaz', 'Rojas Quispe', 'Mendoza Salazar', 'Chávez Ramos', 'Vargas Cruz', 'Sánchez Vilca', 'Ramírez Ochoa', 'Castillo Nina', 'Reyes Mamani', 'Guerrero Apaza', 'Aguilar Ccoya', 'Espinoza Choque', 'Cárdenas Flores', 'Zúñiga Puma', 'Palomino Quenta', 'Bautista Yupanqui'];
    private const MOTOS = [
        ['Honda', 'CG150'], ['Honda', 'XR150'], ['Honda', 'Wave 110'],
        ['Yamaha', 'YBR125'], ['Yamaha', 'FZ150'], ['Yamaha', 'Crypton 110'],
        ['Bajaj', 'Pulsar NS200'], ['Bajaj', 'Boxer 150'], ['Bajaj', 'Discover 125'],
        ['Suzuki', 'AX100'], ['Suzuki', 'GN125'],
        ['TVS', 'Apache RTR 160'], ['Honda', 'CB1'],
    ];

    private array $mecanicos = [];
    private int $usuId;
    private int $almId;
    private int $cajId;
    private int $efectivoId;
    private array $metodoPagoIds = [];
    private array $categoriaIds = [];
    private array $productos = [];
    private array $proveedorIds = [];
    private array $tipoGastoIds = [];
    private array $bahiaIds = [];
    private array $turnoIds = [];
    private array $recepcionItems = [];
    private array $lotesPorProducto = [];
    private array $loteStock = [];
    private array $lotePrecioVenta = [];
    private array $clientes = [];
    private array $motosPorCliente = [];
    private array $reservasPendientes = [];

    private array $resumen = [
        'clientes' => 0, 'productos' => 0, 'compras' => 0, 'gastos' => 0,
        'sesiones_caja' => 0, 'reservaciones' => 0, 'mantenimientos' => 0,
        'recepcion_respuestas' => 0, 'ventas' => 0,
    ];

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $tenant = CentralTenant::find($tenantId);

        if (!$tenant) {
            $this->error("No existe ningún tenant con id \"{$tenantId}\".");

            return self::FAILURE;
        }

        if ($tenant->tipo_negocio !== 'tallermoto') {
            $this->error("El tenant \"{$tenantId}\" no es de tipo tallermoto (tipo_negocio={$tenant->tipo_negocio}).");

            return self::FAILURE;
        }

        $confirmar = (bool) $this->option('confirmar');

        $resultado = self::SUCCESS;

        $tenant->run(function () use ($tenantId, $confirmar, &$resultado) {
            $reservasExistentes = Reservacion::count();

            if ($reservasExistentes > 15) {
                $this->error("El tenant \"{$tenantId}\" ya tiene {$reservasExistentes} reservaciones. Por seguridad este comando solo corre en tenants prácticamente vacíos (máx. 15). Abortando.");
                $resultado = self::FAILURE;

                return;
            }

            if (!$confirmar) {
                $this->warn("MODO SIMULACIÓN (sin --confirmar). No se escribió nada en la base de datos.");
                $this->line("Tenant destino: {$tenantId}");
                $this->line('Se generarán aproximadamente: 30 clientes, 40 productos, ~180 sesiones de caja, ~250 compras, ~150 gastos, ~500 reservaciones, ~350 mantenimientos, ~800 ventas, repartidos en los últimos 12 meses.');
                $this->line('Vuelve a correr el comando agregando --confirmar para ejecutar de verdad.');

                return;
            }

            $this->info("Poblando tenant \"{$tenantId}\" con datos demo (esto puede tardar varios minutos)...");

            DB::transaction(function () {
                $this->cargarBase();
                $this->seedCatalogo();
                $this->seedClientesYMotos();
                $this->simularAnio();
            });

            $this->info('Listo. Resumen de lo creado:');
            foreach ($this->resumen as $clave => $valor) {
                $this->line(" - {$clave}: {$valor}");
            }
        });

        return $resultado;
    }

    private function cargarBase(): void
    {
        $this->usuId = User::query()->value('id');

        if (!$this->usuId) {
            throw new \RuntimeException('El tenant no tiene ningún usuario; no se puede atribuir la actividad demo a nadie.');
        }

        $this->mecanicos = Personal::query()->pluck('PER_Id')->all();

        $caja = Caja::where('CAJ_Status', 1)->first();

        if (!$caja) {
            throw new \RuntimeException('El tenant no tiene ninguna caja activa configurada.');
        }

        $this->cajId = $caja->CAJ_Id;
        $this->almId = $caja->ALM_Id ?? Almacen::query()->value('ALM_Id');

        if (!$this->almId) {
            throw new \RuntimeException('El tenant no tiene ningún almacén configurado.');
        }

        foreach (MetodoPago::where('MEP_Status', 1)->get() as $mp) {
            $this->metodoPagoIds[] = $mp->MEP_Id;

            if ($mp->MEP_Pago === 'Efectivo') {
                $this->efectivoId = $mp->MEP_Id;
            }
        }

        if (empty($this->metodoPagoIds)) {
            throw new \RuntimeException('El tenant no tiene métodos de pago activos.');
        }

        $this->bahiaIds = Bahia::where('BAH_Estado', 'ACT')->pluck('BAH_Id')->all();

        if (empty($this->bahiaIds)) {
            $this->bahiaIds[] = Bahia::create(['USU_Id' => $this->usuId, 'ALM_Id' => $this->almId, 'BAH_Nombre' => 'Bahía 1', 'BAH_Estado' => 'ACT'])->BAH_Id;
        }

        $this->turnoIds = Turno::where('TUR_Estado', 'ACT')->pluck('TUR_Id')->all();

        if (empty($this->turnoIds)) {
            $this->turnoIds[] = Turno::create(['TUR_Nombre' => 'Mañana', 'TUR_Descripcion' => 'Turno mañana', 'TUR_Estado' => 'ACT'])->TUR_Id;
        }

        $this->recepcionItems = RecepcionItem::activos()->get(['RIT_Id', 'RIT_TipoCampo', 'RIT_Opciones'])->all();
    }

    private function seedCatalogo(): void
    {
        $claId = Clase::firstOrCreate(['CLA_Nombre' => 'Repuestos y Accesorios'])->CLA_Id;

        foreach (self::CATEGORIAS as $nombre) {
            $cat = Categoria::firstOrCreate(['CAT_Nombre' => $nombre], ['CLA_Id' => $claId]);
            $this->categoriaIds[$nombre] = $cat->CAT_Id;
        }

        foreach (self::PROVEEDORES as [$razon, $doc]) {
            $prov = Proveedor::firstOrCreate(
                ['PROV_RazonSocial' => $razon],
                ['PROV_TipoDocumento' => 'RUC', 'PROV_NumDocumento' => $doc, 'PROV_Status' => 1]
            );
            $this->proveedorIds[] = $prov->PROV_Id;
        }

        foreach (self::TIPOS_GASTO as $desc) {
            $tg = TipoGasto::firstOrCreate(['TG_Descripcion' => $desc]);
            $this->tipoGastoIds[] = $tg->TG_Id;
        }

        foreach (self::PRODUCTOS as [$nombre, $marca, $catNombre, $precioCompra, $precioVenta]) {
            $producto = Producto::firstOrCreate(
                ['PRO_Nombre' => $nombre],
                [
                    'PRO_Marca' => $marca,
                    'CAT_Id' => $this->categoriaIds[$catNombre],
                    'PRO_PrecioCompra' => $precioCompra,
                    'PRO_PrecioVenta' => $precioVenta,
                    'PRO_StockMinimo' => 5,
                    'PRO_Status' => 1,
                    'PRO_MostrarCatalogo' => 1,
                ]
            );
            $this->productos[] = ['id' => $producto->PRO_Id, 'compra' => (float) $precioCompra, 'venta' => (float) $precioVenta];
            $this->resumen['productos']++;
        }
    }

    private function seedClientesYMotos(): void
    {
        $doc = 71234502;

        for ($i = 0; $i < 30; $i++) {
            $nombre = self::NOMBRES[$i % count(self::NOMBRES)] . ' ' . self::APELLIDOS[($i * 3) % count(self::APELLIDOS)];

            $cliente = Cliente::create([
                'CLI_TipoDocumento' => 'DNI',
                'CLI_NumDocumento' => (string) ($doc + $i),
                'CLI_Nombre' => $nombre,
                'CLI_Celular' => '9' . str_pad((string) random_int(1000000, 9999999), 8, '0', STR_PAD_LEFT),
                'CLI_Correo' => null,
                'CLI_Status' => 1,
            ]);

            $this->clientes[] = $cliente->CLI_Id;
            $this->resumen['clientes']++;

            $cantidadMotos = random_int(1, 2) === 2 ? 2 : 1;
            $motos = [];

            for ($m = 0; $m < $cantidadMotos; $m++) {
                [$marca, $modelo] = self::MOTOS[array_rand(self::MOTOS)];
                $motos[] = [
                    'placa' => 'ABC-' . str_pad((string) (100 + $i * 3 + $m), 3, '0', STR_PAD_LEFT),
                    'moto' => "{$marca} {$modelo}",
                ];
            }

            $this->motosPorCliente[$cliente->CLI_Id] = ['nombre' => $nombre, 'celular' => $cliente->CLI_Celular, 'motos' => $motos];
        }
    }

    private function simularAnio(): void
    {
        $inicio = Carbon::now('America/Lima')->subYear();
        $hoy = Carbon::now('America/Lima');
        $diaIndex = 0;

        for ($fecha = $inicio->copy(); $fecha->lte($hoy); $fecha->addDay()) {
            if ($fecha->isSunday()) {
                continue;
            }

            if (random_int(1, 100) <= 45) {
                continue;
            }

            $this->simularDia($fecha->copy(), $diaIndex);
            $diaIndex++;
        }
    }

    private function simularDia(Carbon $fecha, int $diaIndex): void
    {
        $apertura = $fecha->copy()->setTime(8, random_int(0, 30));
        $cierreEsperado = $fecha->copy()->setTime(19, random_int(0, 30));

        $montoApertura = random_int(100, 300);

        $sesion = CajaSesion::create([
            'CAJ_Id' => $this->cajId,
            'USU_Id_Apertura' => $this->usuId,
            'CS_MontoApertura' => $montoApertura,
            'CS_FechaApertura' => $apertura,
            'CS_Estado' => 'abierta',
        ]);
        $this->resumen['sesiones_caja']++;

        $efectivoVentas = 0.0;
        $efectivoCompras = 0.0;
        $efectivoGastos = 0.0;

        foreach (range(1, random_int(0, 2)) as $_) {
            $efectivoCompras += $this->crearCompra($sesion, $fecha);
        }

        if (random_int(1, 100) <= 60) {
            $efectivoGastos += $this->crearGasto($sesion, $fecha);
        }

        $this->crearReservasDelDia($fecha, $diaIndex);
        $this->procesarCheckInsPendientes($fecha, $diaIndex);

        foreach (range(1, random_int(3, 6)) as $_) {
            $venta = $this->crearVenta($sesion, $fecha);
            if ($venta !== null) {
                $efectivoVentas += $venta;
            }
        }

        $montoEsperado = $montoApertura + $efectivoVentas - $efectivoCompras - $efectivoGastos;
        $montoReal = round($montoEsperado + (random_int(-500, 500) / 100), 2);

        $sesion->update([
            'USU_Id_Cierre' => $this->usuId,
            'CS_MontoEsperado' => round($montoEsperado, 2),
            'CS_MontoReal' => $montoReal,
            'CS_Diferencia' => round($montoReal - $montoEsperado, 2),
            'CS_FechaCierre' => $cierreEsperado,
            'CS_Estado' => 'cerrada',
            'CS_TipoCierre' => 'manual',
        ]);
    }

    private function crearCompra(CajaSesion $sesion, Carbon $fecha): float
    {
        $mepId = random_int(1, 100) <= 70 ? $this->efectivoId : $this->metodoPagoIds[array_rand($this->metodoPagoIds)];

        $compra = Compra::create([
            'COM_TipoDocumento' => 'Factura',
            'COM_NumDocumento' => 'F001-' . str_pad((string) random_int(1, 9999), 5, '0', STR_PAD_LEFT),
            'COM_TipoPago' => 'Contado',
            'MEP_Id' => $mepId,
            'PROV_Id' => $this->proveedorIds[array_rand($this->proveedorIds)],
            'CAJ_Id' => $this->cajId,
            'CS_Id' => $sesion->CS_Id,
            'USU_Id' => $this->usuId,
            'COM_Status' => 1,
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ]);

        $total = 0.0;
        $item = 1;

        foreach (range(1, random_int(2, 5)) as $_) {
            $producto = $this->productos[array_rand($this->productos)];
            $cantidad = random_int(5, 30);
            $precioCompra = $producto['compra'] > 0 ? $producto['compra'] : 5;
            $precioVenta = $producto['venta'];

            DB::table('detalle_compra')->insert([
                'COM_Id' => $compra->COM_Id,
                'ALM_Id' => $this->almId,
                'PRO_Id' => $producto['id'],
                'DCOM_Item' => $item++,
                'DCOM_Cantidad' => $cantidad,
                'DCOM_PrecioCompra' => $precioCompra,
                'DCOM_PrecioVenta' => $precioVenta,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            $lote = Lote::create([
                'ALM_Id' => $this->almId,
                'PRO_Id' => $producto['id'],
                'LOT_TipoIngreso' => 'COMPRA',
                'LOT_IdIngreso' => $compra->COM_Id,
                'LOT_CantidadReal' => $cantidad,
                'LOT_CantidadIngreso' => $cantidad,
                'LOT_PrecioCompra' => $precioCompra,
                'LOT_PrecioVenta' => $precioVenta,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            $this->lotesPorProducto[$producto['id']][] = $lote->LOT_Id;
            $this->loteStock[$lote->LOT_Id] = (float) $cantidad;
            $this->lotePrecioVenta[$lote->LOT_Id] = (float) $precioVenta;

            Producto::where('PRO_Id', $producto['id'])->update([
                'PRO_PrecioCompra' => $precioCompra,
                'PRO_PrecioVenta' => $precioVenta,
            ]);

            $total += $cantidad * $precioCompra;
        }

        $this->resumen['compras']++;

        return $mepId === $this->efectivoId ? $total : 0.0;
    }

    private function crearGasto(CajaSesion $sesion, Carbon $fecha): float
    {
        $mepId = random_int(1, 100) <= 70 ? $this->efectivoId : $this->metodoPagoIds[array_rand($this->metodoPagoIds)];
        $monto = random_int(20, 250);
        $esEfectivo = $mepId === $this->efectivoId;

        Gasto::create([
            'PROV_Id' => $this->proveedorIds[array_rand($this->proveedorIds)],
            'MEP_Id' => $mepId,
            'TG_Id' => $this->tipoGastoIds[array_rand($this->tipoGastoIds)],
            'ALM_Id' => $this->almId,
            'CAJ_Id' => $this->cajId,
            'CS_Id' => $sesion->CS_Id,
            'USU_Id' => $this->usuId,
            'GAS_Descripcion' => 'Gasto operativo del taller',
            'GAS_Monto' => $monto,
            'GAS_Fecha' => $fecha,
            'TG_Comprobante' => 'Boleta',
            'TG_ComprobanteNum' => 'B001-' . str_pad((string) random_int(1, 9999), 5, '0', STR_PAD_LEFT),
            'GAS_Afecta' => $esEfectivo ? 'SI' : 'NO',
            'GAS_Status' => 1,
        ]);

        $this->resumen['gastos']++;

        return $esEfectivo ? (float) $monto : 0.0;
    }

    private function crearReservasDelDia(Carbon $fecha, int $diaIndex): void
    {
        $combos = [];

        foreach ($this->bahiaIds as $bahId) {
            foreach ($this->turnoIds as $turId) {
                $combos[] = [$bahId, $turId];
            }
        }

        shuffle($combos);

        $deseadas = random_int(1, 4);
        $clienteIds = array_keys($this->motosPorCliente);

        foreach (array_slice($combos, 0, min($deseadas, count($combos))) as [$bahId, $turId]) {
            $fechaProgramada = $fecha->format('Y-m-d');

            if (Reservacion::slotEstaOcupado($bahId, $turId, $fechaProgramada)) {
                continue;
            }

            $cliId = $clienteIds[array_rand($clienteIds)];
            $datosCliente = $this->motosPorCliente[$cliId];
            $moto = $datosCliente['motos'][array_rand($datosCliente['motos'])];

            try {
                $reserva = Reservacion::create([
                    'TUR_Id' => $turId,
                    'ALM_Id' => $this->almId,
                    'BAH_Id' => $bahId,
                    'RES_Placa' => $moto['placa'],
                    'RES_Moto' => $moto['moto'],
                    'RES_Cliente' => $datosCliente['nombre'],
                    'RES_Celular' => $datosCliente['celular'],
                    'RES_Detalle' => 'Revisión y mantenimiento general',
                    'RES_FechaProgramada' => $fechaProgramada,
                    'RES_State' => 'PENDIENTE',
                    'RES_Estado' => 'ACT',
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                if (Reservacion::esConflictoDeSlot($e)) {
                    continue;
                }

                throw $e;
            }

            $this->reservasPendientes[] = ['res_id' => $reserva->RES_Id, 'dia' => $diaIndex];
            $this->resumen['reservaciones']++;
        }
    }

    private function procesarCheckInsPendientes(Carbon $fecha, int $diaIndex): void
    {
        $tipos = array_keys(\App\Services\TenantTallerMotos\GestionProcesoService::TIPOS);
        $restantes = [];

        foreach ($this->reservasPendientes as $pendiente) {
            if ($pendiente['dia'] >= $diaIndex) {
                $restantes[] = $pendiente;
                continue;
            }

            if (random_int(1, 100) > 70) {
                $restantes[] = $pendiente;
                continue;
            }

            $reserva = Reservacion::find($pendiente['res_id']);

            if (!$reserva) {
                continue;
            }

            $tipo = $tipos[array_rand($tipos)];
            $perId = !empty($this->mecanicos) ? $this->mecanicos[array_rand($this->mecanicos)] : null;

            $mtto = $this->crearMantenimientoDesdeReserva($tipo, $reserva, $fecha, $perId);
            $reserva->update(['RES_CheckIn' => $fecha]);
            $this->resumen['mantenimientos']++;

            if (random_int(1, 100) <= 80) {
                $this->aprobarMantenimiento($tipo, $mtto, $fecha);
            }
        }

        $this->reservasPendientes = $restantes;
    }

    private function crearMantenimientoDesdeReserva(string $tipo, Reservacion $reserva, Carbon $fecha, ?int $perId): object
    {
        $meta = \App\Services\TenantTallerMotos\GestionProcesoService::TIPOS[$tipo];
        $prefijo = $meta['prefijo'];
        $modeloClase = $meta['modelo'];

        $base = [
            "{$prefijo}_Placa" => $reserva->RES_Placa,
            "{$prefijo}_Propietario" => $reserva->RES_Cliente,
            "{$prefijo}_celular" => $reserva->RES_Celular,
            "{$prefijo}_Unidad" => $reserva->RES_Moto,
            "{$prefijo}_KMEntrada" => (string) random_int(5000, 45000),
            "{$prefijo}_DetalleIngreso" => 'Revisión y mantenimiento general según checklist',
            "{$prefijo}_DetalleObservacion" => $reserva->RES_Detalle,
            "{$prefijo}_FechaCreacion" => $fecha,
            "{$prefijo}_FechaEdicion" => $fecha,
            "{$prefijo}_UsuarioCreacion" => $this->usuId,
            "{$prefijo}_UsuarioEditado" => $this->usuId,
            'PER_Id' => $perId,
            'RES_Id' => $reserva->RES_Id,
        ];

        $rangosChecklist = [
            'MGI' => 27, 'MGC' => 21, 'MPI' => 20, 'MPC' => 11, 'MAV' => 0,
        ];

        if ($rangosChecklist[$prefijo] > 0) {
            foreach (range(1, $rangosChecklist[$prefijo]) as $n) {
                $base["{$prefijo}_Det{$n}"] = 'NO';
            }
        }

        /** @var \Illuminate\Database\Eloquent\Model $mtto */
        $mtto = new $modeloClase();
        $mtto->forceFill($base);
        $mtto->save();

        return $mtto;
    }

    private function aprobarMantenimiento(string $tipo, object $mtto, Carbon $fecha): void
    {
        $meta = \App\Services\TenantTallerMotos\GestionProcesoService::TIPOS[$tipo];
        $prefijo = $meta['prefijo'];
        $tabla = $meta['tabla'];
        $idColumna = "{$prefijo}_Id";
        $id = $mtto->{$idColumna};

        $update = [
            "{$prefijo}_Estado" => 'APROBADO',
            "{$prefijo}_FechaTermino" => $fecha->copy()->addHours(random_int(1, 4)),
            "{$prefijo}_VerifConforme" => true,
        ];

        DB::table($tabla)->where($idColumna, $id)->update($update);

        foreach ($this->recepcionItems as $item) {
            if ($item->RIT_TipoCampo === RecepcionItem::TIPO_TEXT) {
                continue;
            }

            if (random_int(1, 100) > 75) {
                continue;
            }

            $valor = $this->valorAleatorioParaItem($item);

            RecepcionRespuesta::create([
                'MTO_Tabla' => $tabla,
                'MTO_Id' => $id,
                'RIT_Id' => $item->RIT_Id,
                'RRP_Valor' => $valor,
            ]);

            $this->resumen['recepcion_respuestas']++;
        }
    }

    private function valorAleatorioParaItem(object $item): string
    {
        if ($item->RIT_TipoCampo === RecepcionItem::TIPO_BOOLEAN) {
            return random_int(1, 100) <= 85 ? 'SI' : 'NO';
        }

        if (in_array($item->RIT_TipoCampo, [RecepcionItem::TIPO_SELECT, RecepcionItem::TIPO_STATUS, RecepcionItem::TIPO_INSPECTION], true)) {
            $opciones = $item->RIT_Opciones;

            if (is_array($opciones) && !empty($opciones)) {
                return (string) $opciones[array_rand($opciones)];
            }

            return 'BUENO';
        }

        return 'OK';
    }

    private function crearVenta(CajaSesion $sesion, Carbon $fecha): ?float
    {
        $disponibles = array_filter($this->lotesPorProducto, function ($lotes) {
            foreach ($lotes as $lotId) {
                if (($this->loteStock[$lotId] ?? 0) > 0) {
                    return true;
                }
            }

            return false;
        });

        if (empty($disponibles)) {
            return null;
        }

        $proIds = array_keys($disponibles);
        shuffle($proIds);
        $proIds = array_slice($proIds, 0, random_int(1, min(4, count($proIds))));

        $mepId = random_int(1, 100) <= 75 ? $this->efectivoId : $this->metodoPagoIds[array_rand($this->metodoPagoIds)];
        $cliId = random_int(1, 100) <= 60 ? $this->clientes[array_rand($this->clientes)] : 1;

        $venta = Venta::create([
            'VEN_TipoPago' => 1,
            'MEP_Id' => $mepId,
            'USU_Id' => $this->usuId,
            'CLI_Id' => $cliId,
            'ALM_Id' => $this->almId,
            'CAJ_Id' => $this->cajId,
            'CS_Id' => $sesion->CS_Id,
            'VEN_Status' => 1,
            'VEN_FechaEnvio' => $fecha,
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ]);

        $total = 0.0;
        $item = 1;

        foreach ($proIds as $proId) {
            $lotId = $this->lotesPorProducto[$proId][0];

            foreach ($this->lotesPorProducto[$proId] as $candidato) {
                if (($this->loteStock[$candidato] ?? 0) > 0) {
                    $lotId = $candidato;
                    break;
                }
            }

            $disponible = $this->loteStock[$lotId] ?? 0;

            if ($disponible <= 0) {
                continue;
            }

            $cantidad = min($disponible, random_int(1, 3));
            $precioUnitario = $this->lotePrecioVenta[$lotId];

            DB::table('detalle_venta')->insert([
                'VEN_Id' => $venta->VEN_Id,
                'PRO_Id' => $proId,
                'DEV_Cantidad' => $cantidad,
                'DEV_Item' => $item++,
                'DEV_PrecioUnitario' => $precioUnitario,
                'LOT_Id' => $lotId,
                'DEV_Descuento' => 0,
            ]);

            $this->loteStock[$lotId] = $disponible - $cantidad;
            $total += $cantidad * $precioUnitario;
        }

        if ($item === 1) {
            $venta->delete();

            return null;
        }

        $venta->update(['VEN_Pagado' => $total, 'VEN_Vuelto' => 0]);
        $this->resumen['ventas']++;

        return $mepId === $this->efectivoId ? $total : 0.0;
    }
}
