@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Centro de Ayuda')

@section('head')
<style>
    /* ===== Centro de Ayuda — usa las variables de tema del panel (kael-light/kael-dark)
       para verse nativo en ambos temas sin definir una paleta propia. ===== */
    .ayuda-hero {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        border-radius: 14px;
        padding: 28px 28px 26px;
        color: #fff;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px -8px rgba(0,0,0,.25);
    }
    .ayuda-hero h1 {
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ayuda-hero p {
        margin: 0 0 18px;
        opacity: .92;
        font-size: .95rem;
        max-width: 640px;
    }
    .ayuda-search-wrap {
        position: relative;
        max-width: 480px;
    }
    .ayuda-search-wrap i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }
    #ayudaBuscar {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 12px 16px 12px 42px;
        font-size: .95rem;
        box-shadow: 0 4px 14px -4px rgba(0,0,0,.35);
        color: var(--text-main);
        background: var(--bg-card);
    }
    #ayudaBuscar:focus {
        outline: 3px solid rgba(255,255,255,.35);
    }
    #ayudaSinResultados {
        display: none;
        background: var(--bg-card);
        border: 1px dashed var(--text-muted);
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        color: var(--text-muted);
    }

    .ayuda-layout {
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }
    .ayuda-toc {
        flex: 0 0 260px;
        position: sticky;
        top: 12px;
        max-height: calc(100vh - 24px);
        overflow-y: auto;
        background: var(--bg-card);
        border-radius: 12px;
        padding: 14px 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .ayuda-toc .toc-titulo {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 4px 10px 8px;
    }
    .ayuda-toc a {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 7px 10px;
        border-radius: 8px;
        color: var(--text-main);
        font-size: .84rem;
        font-weight: 600;
        text-decoration: none;
        line-height: 1.25;
    }
    .ayuda-toc a i { width: 16px; text-align: center; color: var(--primary); flex-shrink: 0; }
    .ayuda-toc a:hover { background: rgba(37,99,235,.08); }
    .ayuda-toc a.activo { background: var(--primary); color: #fff; }
    .ayuda-toc a.activo i { color: #fff; }
    .ayuda-toc hr { margin: 8px 0; opacity: .35; }

    .ayuda-content { flex: 1 1 auto; min-width: 0; }

    .ayuda-toolbar {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-bottom: 12px;
    }

    .ayuda-modulo {
        background: var(--bg-card);
        border-radius: 12px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        overflow: hidden;
        scroll-margin-top: 12px;
    }
    .ayuda-modulo-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        cursor: pointer;
        user-select: none;
    }
    .ayuda-modulo-icono {
        flex: 0 0 42px;
        height: 42px;
        width: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #fff;
    }
    .ayuda-modulo-header h2 {
        font-size: 1.02rem;
        font-weight: 800;
        margin: 0;
        color: var(--text-main);
    }
    .ayuda-modulo-header p {
        font-size: .82rem;
        color: var(--text-muted);
        margin: 2px 0 0;
    }
    .ayuda-modulo-header .chev {
        margin-left: auto;
        color: var(--text-muted);
        transition: transform .2s ease;
        flex-shrink: 0;
    }
    .ayuda-modulo.abierto .chev { transform: rotate(180deg); }
    .ayuda-modulo-body {
        display: none;
        padding: 4px 22px 22px;
        border-top: 1px solid rgba(128,128,128,.15);
    }
    .ayuda-modulo.abierto .ayuda-modulo-body { display: block; }

    .ayuda-sub { margin-top: 18px; }
    .ayuda-sub:first-child { margin-top: 16px; }
    .ayuda-sub h3 {
        font-size: .92rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ayuda-sub h3 i { color: var(--primary); font-size: .85rem; }
    .ayuda-sub p, .ayuda-sub li { color: var(--text-main); font-size: .87rem; line-height: 1.55; }
    .ayuda-sub ol, .ayuda-sub ul { padding-left: 1.3rem; margin-bottom: .6rem; }
    .ayuda-sub table { font-size: .84rem; }

    .ayuda-nota {
        border-radius: 8px;
        padding: 10px 14px;
        font-size: .83rem;
        margin: 10px 0;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        line-height: 1.5;
    }
    .ayuda-nota i { margin-top: 2px; flex-shrink: 0; }
    .ayuda-nota.tip { background: rgba(34,197,94,.10); color: #15803d; }
    .tema-dark .ayuda-nota.tip { color: #4ade80; }
    .ayuda-nota.warn { background: rgba(245,158,11,.12); color: #b45309; }
    .tema-dark .ayuda-nota.warn { color: #fbbf24; }
    .ayuda-nota.info { background: rgba(37,99,235,.10); color: #1d4ed8; }
    .tema-dark .ayuda-nota.info { color: #60a5fa; }

    .ayuda-roles { display: flex; flex-wrap: wrap; gap: 5px; margin: 6px 0 2px; }
    .ayuda-roles .badge { font-weight: 600; font-size: .72rem; padding: 4px 9px; }

    .ayuda-ruta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(128,128,128,.12);
        color: var(--text-muted);
        border-radius: 6px;
        padding: 3px 9px;
        font-size: .76rem;
        font-weight: 600;
        margin: 2px 4px 2px 0;
    }

    mark.ayuda-hl { background: #fde68a; color: #1e293b; border-radius: 3px; padding: 0 2px; }

    @media (max-width: 991px) {
        .ayuda-layout { flex-direction: column; }
        .ayuda-toc {
            position: static;
            width: 100%;
            max-height: none;
            display: none;
        }
        .ayuda-toc.visible-mobile { display: block; }
        #btnTocMovil { display: inline-flex !important; }
    }
</style>
@endsection

@section('contenido')
<div class="col-12">

    <div class="ayuda-hero">
        <h1><i class="fas fa-graduation-cap"></i> Centro de Ayuda</h1>
        <p>Guía completa del sistema, paso a paso. Encuentra cómo usar cada módulo: desde recibir una moto en el taller hasta cerrar la caja al final del día.</p>
        <div class="ayuda-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="ayudaBuscar" placeholder="Buscar un tema... ej. &quot;abrir caja&quot;, &quot;nota de crédito&quot;, &quot;checklist&quot;">
        </div>
    </div>

    <div class="ayuda-toolbar">
        <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none" id="btnTocMovil">
            <i class="fas fa-list mr-1"></i> Índice
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnExpandirTodo">
            <i class="fas fa-angles-down mr-1"></i> Expandir todo
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnColapsarTodo">
            <i class="fas fa-angles-up mr-1"></i> Colapsar todo
        </button>
    </div>

    <div class="ayuda-layout">

        {{-- ===================== TOC / ÍNDICE ===================== --}}
        <nav class="ayuda-toc" id="ayudaToc">
            <div class="toc-titulo">Primeros pasos</div>
            <a href="#mod-intro"><i class="fas fa-play-circle"></i> Introducción</a>
            <a href="#mod-roles"><i class="fas fa-user-shield"></i> Roles y permisos</a>

            <hr>
            <div class="toc-titulo">Operación diaria del taller</div>
            @if (tenant_has_module('mantenimientos') && auth()->user()->can('tenant.procesos.index'))
            <a href="#mod-proceso"><i class="fas fa-clipboard-list"></i> Gestión de Proceso</a>
            @endif
            @if (tenant_has_module('mantenimientos') && auth()->user()->hasAnyPermission([
                'tenant.reservaciones.administracion.index',
                'tenant.reservaciones.administracion.create',
                'tenant.reservaciones.administracion.notificar',
            ]))
            <a href="#mod-reservas"><i class="fas fa-calendar-check"></i> Reservas</a>
            @endif
            @if (tenant_has_module('mantenimientos') && auth()->user()->canAny([
                'tenant.mantenimientos.generalinyectada.index',
                'tenant.mantenimientos.generalcarburada.index',
                'tenant.mantenimientos.preventivoinyectada.index',
                'tenant.mantenimientos.preventivocarburada.index',
            ]))
            <a href="#mod-mantenimientos"><i class="fas fa-tools"></i> Mantenimientos</a>
            @endif
            @if (tenant_has_module('mantenimientos') && auth()->user()->can('tenant.actividades.mantenimientoactividadvariada.index'))
            <a href="#mod-actividades"><i class="fas fa-list-check"></i> Actividades Variadas</a>
            @endif
            @if (tenant_has_module('mantenimientos') && auth()->user()->can('tenant.motos.index'))
            <a href="#mod-motos"><i class="fas fa-motorcycle"></i> Motos Atendidas</a>
            @endif

            <hr>
            <div class="toc-titulo">Ventas y caja</div>
            @if (tenant_has_module('ventas') && auth()->user()->canAny([
                'tenant.ventas.venta.index',
                'tenant.ventas.cliente.index',
                'tenant.ventas.metodopago.index',
            ]))
            <a href="#mod-ventas"><i class="fas fa-cash-register"></i> Ventas</a>
            @endif
            @if (tenant_has_module('ventas') && auth()->user()->can('tenant.ventas.caja.index'))
            <a href="#mod-caja"><i class="fas fa-vault"></i> Caja</a>
            @endif
            @if (tenant_has_module('ventas') && auth()->user()->can('tenant.ventas.venta.index'))
            <a href="#mod-anulaciones"><i class="fas fa-ban"></i> Anulaciones</a>
            @endif

            <hr>
            <div class="toc-titulo">Compras e inventario</div>
            @if (tenant_has_module('compras') && auth()->user()->canAny([
                'tenant.compras.compra.index',
                'tenant.compras.proveedor.index',
                'tenant.compras.gasto.index',
                'tenant.compras.tipogasto.index',
            ]))
            <a href="#mod-compras"><i class="fas fa-shopping-cart"></i> Compras</a>
            @endif
            @if ((tenant_has_module('inventario') || tenant_has_module('productos')) && auth()->user()->canAny([
                'tenant.inventario.clase.index',
                'tenant.inventario.categoria.index',
                'tenant.inventario.almacen.index',
                'tenant.inventario.producto.index',
            ]))
            <a href="#mod-inventario"><i class="fas fa-boxes"></i> Inventario</a>
            @endif

            <hr>
            <div class="toc-titulo">Análisis</div>
            @if (auth()->user()->canAny([
                'tenant.reportes.listageneral',
                'tenant.reportes.rendimientomecanicos',
                'tenant.reportes.rentabilidad',
                'tenant.reportes.inventario',
                'tenant.reportes.comprasGastos',
                'tenant.reportes.clientes',
                'tenant.reportes.caja',
                'tenant.reportes.operacionTaller',
            ]))
            <a href="#mod-reportes"><i class="fas fa-chart-bar"></i> Reportes</a>
            @endif

            <hr>
            <div class="toc-titulo">Administración</div>
            @if (auth()->user()->canAny([
                'tenant.configuracion.empresa.index',
                'tenant.configuracion.sede.index',
                'tenant.configuracion.turno.index',
                'tenant.configuracion.bahia.index',
                'tenant.configuracion.horario.index',
                'tenant.configuracion.recepcion.index',
            ]))
            <a href="#mod-configuracion"><i class="fas fa-cogs"></i> Configuración</a>
            @endif
            @if (auth()->user()->canAny([
                'tenant.seguridad.permiso.index',
                'tenant.seguridad.role.index',
                'tenant.seguridad.users.index',
            ]))
            <a href="#mod-seguridad"><i class="fas fa-lock"></i> Seguridad</a>
            @endif
            @hasanyrole('Admin|Gerente')
            <a href="#mod-facturacion"><i class="fas fa-file-invoice-dollar"></i> Mi Facturación</a>
            @endhasanyrole
        </nav>

        {{-- ===================== CONTENIDO ===================== --}}
        <div class="ayuda-content" id="ayudaContent">

            {{-- ========================================================= --}}
            {{-- INTRODUCCIÓN --}}
            {{-- ========================================================= --}}
            <section class="ayuda-modulo abierto" id="mod-intro" data-buscar="introduccion bienvenida primeros pasos que es el sistema como funciona menu principal panel sidebar barra lateral rol usuario contraseña iniciar sesion login">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:var(--primary);"><i class="fas fa-play-circle"></i></div>
                    <div>
                        <h2>Introducción</h2>
                        <p>Qué es el sistema y cómo moverte por él</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <div class="ayuda-sub">
                        <h3><i class="fas fa-info-circle"></i> ¿Qué es este sistema?</h3>
                        <p>
                            Es el sistema con el que se administra todo el día a día del taller: desde que un cliente
                            reserva una cita o llega con su moto, pasando por el checklist de mantenimiento que le hace
                            el mecánico, hasta el cobro en caja y el control del stock de repuestos. Todo lo que ves en
                            el menú de la izquierda es un módulo distinto, y esta página te explica qué hace cada uno.
                        </p>
                    </div>
                    <div class="ayuda-sub">
                        <h3><i class="fas fa-compass"></i> Cómo está organizado el menú</h3>
                        <p>El menú lateral (a la izquierda de tu pantalla) agrupa las pantallas por tema:</p>
                        <ul>
                            <li><strong>Gestión de Proceso</strong> y <strong>Reservas</strong>: el tablero del día a día y el agendamiento de citas.</li>
                            <li><strong>Mantenimientos</strong> y <strong>Actividades</strong>: las fichas de trabajo de cada moto (checklist, repuestos, fotos).</li>
                            <li><strong>Ventas</strong> y <strong>Caja</strong>: todo lo relacionado a cobrar y emitir comprobantes.</li>
                            <li><strong>Compras</strong> e <strong>Inventario</strong>: entrada de mercadería y control de stock.</li>
                            <li><strong>Reportes</strong>: números del negocio (rentabilidad, caja, clientes, etc.).</li>
                            <li><strong>Configuración</strong> y <strong>Seguridad</strong>: los ajustes del taller y quién puede hacer qué.</li>
                        </ul>
                        <p>No todos los usuarios ven todos los módulos: cada uno aparece solo si tu rol tiene permiso para usarlo (ver siguiente sección).</p>
                    </div>
                    <div class="ayuda-nota tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>Usa el buscador de arriba de esta página en cualquier momento para saltar directo a un tema, sin tener que leer todo de corrido.</div>
                    </div>
                </div>
            </section>

            {{-- ========================================================= --}}
            {{-- ROLES Y PERMISOS --}}
            {{-- ========================================================= --}}
            <section class="ayuda-modulo" id="mod-roles" data-buscar="roles permisos quien puede admin gerente recepcion mecanico cajero compras ventas inventario reportes reservas">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#6366F1;"><i class="fas fa-user-shield"></i></div>
                    <div>
                        <h2>Roles y permisos: quién ve qué</h2>
                        <p>Los roles con los que normalmente trabaja un taller</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <p>El sistema viene con dos roles con acceso total (<strong>Admin</strong> y <strong>Gerente</strong>) y una lista de roles pensados para el resto del equipo. Un administrador los crea y asigna desde <strong>Seguridad → Roles</strong> y <strong>Seguridad → Usuario</strong> (ver esa sección más abajo). Estos son los roles típicos de un taller y qué puede hacer cada uno:</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead><tr><th>Rol</th><th>Para quién es</th><th>Qué puede hacer</th></tr></thead>
                            <tbody>
                                <tr><td><span class="badge badge-dark">Admin</span></td><td>El dueño / super-usuario</td><td>Acceso total a todo el sistema.</td></tr>
                                <tr><td><span class="badge badge-secondary">Gerente</span></td><td>Encargado general</td><td>Casi todo, salvo eliminar usuarios.</td></tr>
                                <tr><td><span class="badge badge-info">Recepción</span></td><td>Quien recibe las motos</td><td>Solo el tablero de <strong>Gestión de Proceso</strong>: ve las reservas de hoy y hace el check-in (recibir la moto, anotar el detalle y asignar mecánico).</td></tr>
                                <tr><td><span class="badge badge-warning">Mecánico</span></td><td>Quien hace el trabajo técnico</td><td>Ve y llena <strong>solo los mantenimientos que tiene asignados</strong> (checklist, repuestos, fotos), sin poder aprobarlos ni eliminarlos. Recibe el aviso cuando Recepción le agrega algo nuevo.</td></tr>
                                <tr><td><span class="badge badge-success">Ventas</span></td><td>Cajero / vendedor</td><td>Métodos de pago, Caja, Clientes, Ventas y Guías de remisión (sin eliminar).</td></tr>
                                <tr><td><span class="badge badge-primary">Compras</span></td><td>Encargado de abastecimiento</td><td>Proveedores, Gastos, Tipos de gasto y Compras (sin eliminar).</td></tr>
                                <tr><td><span class="badge badge-secondary">Inventario</span></td><td>Encargado de almacén</td><td>Clases, Categorías y Productos (sin eliminar).</td></tr>
                                <tr><td><span class="badge badge-dark">Reservas</span></td><td>Quien agenda citas</td><td>Toda la administración de reservaciones.</td></tr>
                                <tr><td><span class="badge badge-light border">Reportes</span></td><td>Supervisión</td><td>Solo puede ver Lista General y Rendimiento de Mecánicos.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="ayuda-nota warn">
                        <i class="fas fa-triangle-exclamation"></i>
                        <div>Importante: para que alguien aparezca en el selector de <strong>"Mecánico responsable"</strong> al recibir una moto, ese usuario tiene que tener asignado exactamente el rol llamado <strong>Mecánico</strong>. Si no aparece en la lista, revisa su rol en Seguridad → Usuario.</div>
                    </div>
                </div>
            </section>

            {{-- ========================================================= --}}
            {{-- GESTIÓN DE PROCESO --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('mantenimientos') && auth()->user()->can('tenant.procesos.index'))
            <section class="ayuda-modulo" id="mod-proceso" data-buscar="gestion de proceso tablero check-in checkin recibir moto bahia mecanico responsable aviso alerta entendido detalle">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#2563EB;"><i class="fas fa-clipboard-list"></i></div>
                    <div>
                        <h2>Gestión de Proceso</h2>
                        <p>El tablero del día — dónde empieza todo</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <p>Es el tablero que ve Recepción cada mañana: una columna por cada <strong>bahía</strong> (espacio físico de trabajo) del taller, con una tarjeta por cada reserva aprobada para hoy en esa bahía. Es el punto donde se junta la reserva de un cliente con el trabajo real que se le va a hacer a la moto.</p>
                    <div class="ayuda-roles">
                        <span class="badge badge-dark">Admin</span><span class="badge badge-secondary">Gerente</span><span class="badge badge-info">Recepción</span>
                        <span class="text-muted small ml-1">ven el tablero completo. <span class="badge badge-warning">Mecánico</span> ve solo lo suyo.</span>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-right-to-bracket"></i> Recibir una moto (check-in)</h3>
                        <ol>
                            <li>En la tarjeta de la reserva, haz clic en <strong>"Recibir moto"</strong>.</li>
                            <li>Elige el <strong>Tipo de mantenimiento</strong> (General/Preventivo × Carburada/Inyectada, o Actividad Variada).</li>
                            <li>Escribe el <strong>Detalle de lo que hay que hacer</strong> (ej. "Cambio de aceite, revisar frenos delanteros, ruido raro en la cadena...").</li>
                            <li>Elige el <strong>Mecánico responsable</strong> (solo aparecen usuarios con rol Mecánico).</li>
                            <li>Presiona <strong>Guardar</strong>.</li>
                        </ol>
                        <p>Esto crea automáticamente la ficha de mantenimiento correspondiente (ver el módulo <a href="#mod-mantenimientos">Mantenimientos</a>) y queda enlazada a esa reserva.</p>
                        <div class="ayuda-nota info">
                            <i class="fas fa-circle-info"></i>
                            <div>Una vez asignado, el <strong>tipo de mantenimiento ya no se puede cambiar</strong> — pero sí puedes volver a abrir "Recibir moto" más tarde para agregar más detalle o cambiar de mecánico.</div>
                        </div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-bell"></i> Avisos al mecánico</h3>
                        <p>
                            Si Recepción agrega algo al detalle <strong>después</strong> de que el mecánico ya tenía el trabajo asignado
                            (por ejemplo, el cliente llamó y pidió revisar algo más), el mecánico ve una alerta en su
                            pantalla en vivo, sin recargar. El mecánico debe presionar <strong>"Entendido"</strong> para confirmar
                            que ya vio el cambio.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-pen-to-square"></i> Continuar el trabajo</h3>
                        <p>Desde la misma tarjeta, un enlace lleva directo a la ficha completa del mantenimiento (el formulario detallado con checklist, repuestos y fotos) — ahí es donde el mecánico realmente hace su trabajo. Ver <a href="#mod-mantenimientos">Mantenimientos</a>.</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- RESERVAS --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('mantenimientos') && auth()->user()->hasAnyPermission([
                'tenant.reservaciones.administracion.index',
                'tenant.reservaciones.administracion.create',
                'tenant.reservaciones.administracion.notificar',
            ]))
            <section class="ayuda-modulo" id="mod-reservas" data-buscar="reservas reservaciones agendar cita grilla semanal bahia turno horario aprobar rechazar notificar whatsapp recordatorio">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#0891B2;"><i class="fas fa-calendar-check"></i></div>
                    <div>
                        <h2>Reservas</h2>
                        <p>Agendar citas y avisar por WhatsApp</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-table-cells"></i> Agendar una reserva</h3>
                        <p><span class="ayuda-ruta">Reservas → Reservaciones</span> muestra una <strong>grilla semanal</strong> (turno × día, con las bahías dentro de cada celda), coloreada según disponibilidad:</p>
                        <ul>
                            <li><span class="badge" style="background:#22C55E;color:#fff;">Verde</span> — disponible.</li>
                            <li><span class="badge" style="background:#F59E0B;color:#fff;">Amarillo</span> — pendiente de aprobar.</li>
                            <li><span class="badge" style="background:#EF4444;color:#fff;">Rojo</span> — ocupado.</li>
                            <li><span class="badge badge-secondary">Gris</span> — ese día/turno no está habilitado para esa sede (revisa <a href="#mod-configuracion">Configuración → Horarios</a>).</li>
                        </ul>
                        <p>Para reservar: haz clic en una celda <strong>verde</strong> y completa el modal con Placa, Motocicleta, Nombre del solicitante, Celular, Tipo de mantenimiento y el detalle del servicio (con la opción de indicar si incluye cambio de aceite y/o de filtro). Guarda con <strong>"Guardar Reservación"</strong>.</p>
                        <div class="ayuda-nota tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>Si escribes una placa que ya vino antes al taller, el sistema autocompleta el dueño y la moto automáticamente.</div>
                        </div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-check-double"></i> Aprobar o rechazar</h3>
                        <p>Haz clic en una celda amarilla u ocupada para abrir el detalle de esa reserva, y usa <strong>"Aprobar Reservación"</strong> o <strong>"Rechazar Reservación"</strong> (esto último libera el horario para que se pueda volver a reservar). Al aprobar una reserva sin tipo de mantenimiento definido (por ejemplo si la hizo el cliente desde la web), el sistema te pide completarlo ahí mismo.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fab fa-whatsapp"></i> Notificar reservas</h3>
                        <p><span class="ayuda-ruta">Reservas → Notificar reservas</span> te muestra las citas de mañana con un mensaje ya redactado (puedes personalizar la plantilla y desde qué hora del día quieres que te avise). El botón <strong>"Enviar"</strong> abre WhatsApp con el mensaje listo para mandar — el envío en sí lo haces tú manualmente, con un clic, no es automático.</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- MANTENIMIENTOS --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('mantenimientos') && auth()->user()->canAny([
                'tenant.mantenimientos.generalinyectada.index',
                'tenant.mantenimientos.generalcarburada.index',
                'tenant.mantenimientos.preventivoinyectada.index',
                'tenant.mantenimientos.preventivocarburada.index',
            ]))
            <section class="ayuda-modulo" id="mod-mantenimientos" data-buscar="mantenimientos general preventivo carburada inyectada checklist repuestos reemplazo fotos evidencias estado pendiente aprobado observado planes recomendaciones verificacion final estado de recepcion inventario visual inspeccion orden de servicio pdf">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#DC2626;"><i class="fas fa-tools"></i></div>
                    <div>
                        <h2>Mantenimientos</h2>
                        <p>La ficha de trabajo completa de cada moto</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <p>
                        Existen <strong>4 tipos</strong>, según el servicio y el motor: <strong>General</strong> o <strong>Preventivo</strong>,
                        cada uno en versión <strong>Carburada</strong> o <strong>Inyectada</strong>. Los cuatro funcionan igual, solo cambia
                        el checklist de tareas típico de ese servicio. Se llega a un registro nuevo desde <a href="#mod-proceso">Gestión de Proceso</a>
                        (check-in de una reserva) o creándolo directo desde <span class="ayuda-ruta">Mantenimientos</span> en el menú.
                    </p>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-file-circle-plus"></i> Partes del formulario</h3>
                        <ol>
                            <li><strong>Datos de la Unidad</strong>: placa (con búsqueda que autocompleta si esa placa ya vino antes), propietario, celular, modelo/unidad, kilometraje de entrada.</li>
                            <li><strong>Plan</strong> (opcional): elige un paquete predefinido para que el checklist muestre solo esos ítems en vez de la lista completa — ver <a href="#mant-planes">Planes de Mantenimiento</a> más abajo.</li>
                            <li><strong>Checklist</strong>: la lista fija de tareas de ese tipo de servicio (cambio de aceite, limpieza de chasis, ajuste de válvulas, revisión de frenos, etc.) — se marca cada una conforme se completa.</li>
                            <li><strong>Repuestos a reemplazar</strong>: se agregan los repuestos usados, con su precio, para calcular el costo.</li>
                            <li><strong>Evidencias fotográficas</strong>: fotos de la moto y/o del trabajo hecho.</li>
                            <li><strong>Recomendaciones y Verificación Final</strong>: qué le recomiendas al cliente para más adelante (con prioridad Alta/Media/Baja) y el checklist de salida antes de entregar la moto: arranque, luces, direccionales, nivel de aceite, prueba de ruta, lavado, más un campo "Otros" y la confirmación final de que la unidad quedó conforme.</li>
                        </ol>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-flag"></i> Estados</h3>
                        <p>
                            <span class="badge badge-warning">Pendiente</span> mientras se trabaja,
                            <span class="badge badge-success">Aprobado</span> cuando se aprueba y notifica al cliente,
                            <span class="badge badge-danger">Observado</span> si algo quedó mal y hay que corregirlo.
                        </p>
                    </div>

                    <div class="ayuda-sub" id="mant-checklist-recepcion">
                        <h3><i class="fas fa-motorcycle"></i> Estado de Recepción (cómo llegó la moto)</h3>
                        <p>
                            Es un checklist aparte, independiente del checklist de servicio: registra <strong>en qué estado llegó la moto</strong>
                            antes de tocarla — un diagrama de la moto marcando cada pieza visible como Bueno/Regular/Malo (faro, luces,
                            direccionales, llantas, frenos, suspensión, etc.), nivel de combustible, nivel de aceite, y una inspección
                            técnica más detallada de motor, transmisión, suspensión, frenos, sistema eléctrico y chasis. Se llena desde
                            dentro de la ficha de edición de cada mantenimiento.
                        </p>
                        <p>Con esos datos se generan dos documentos, accesibles como botones en el listado de cada tipo de mantenimiento:</p>
                        <ul>
                            <li><i class="fas fa-clipboard-list text-primary mr-1"></i> <strong>Orden de Servicio</strong>: el documento principal para el cliente — datos del taller, del cliente y del servicio, el estado de recepción, el checklist realizado, repuestos, recomendaciones y verificación final, con firma.</li>
                            <li><i class="fas fa-motorcycle text-secondary mr-1"></i> <strong>Estado de Recepción</strong>: un PDF solo con el diagrama de "cómo llegó la moto" — útil para mostrárselo al cliente en el momento de la recepción, antes de empezar el trabajo.</li>
                        </ul>
                        <div class="ayuda-nota info">
                            <i class="fas fa-circle-info"></i>
                            <div>El botón de <strong>Estado de Recepción</strong> solo aparece si ya se guardó al menos una respuesta de ese checklist — si nadie lo llenó todavía, el botón se oculta en vez de mostrar un documento vacío.</div>
                        </div>
                    </div>

                    <div class="ayuda-sub" id="mant-planes">
                        <h3><i class="fas fa-layer-group"></i> Planes de Mantenimiento</h3>
                        <p><span class="ayuda-ruta">Mantenimientos → Planes de Mantenimiento</span> te deja armar paquetes con nombre propio (ej. "Servicio básico") eligiendo solo un subconjunto de ítems del checklist completo de un tipo. Al crear un mantenimiento nuevo y elegir ese plan, el formulario muestra únicamente esos ítems — para no tener que revisar manualmente una lista larga cuando el servicio es simple.</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- ACTIVIDADES VARIADAS --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('mantenimientos') && auth()->user()->can('tenant.actividades.mantenimientoactividadvariada.index'))
            <section class="ayuda-modulo" id="mod-actividades" data-buscar="actividades variadas registros nueva actividad otro trabajo no estandar">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#7C3AED;"><i class="fas fa-list-check"></i></div>
                    <div>
                        <h2>Actividades Variadas</h2>
                        <p>Para trabajos que no encajan en los 4 tipos de mantenimiento</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <p>
                        Funciona igual que un mantenimiento (datos de la unidad, repuestos, fotos, recomendaciones, estado de recepción,
                        Orden de Servicio) pero sin un checklist fijo de tareas — sirve para cualquier trabajo que no sea
                        exactamente un mantenimiento General o Preventivo (reparaciones puntuales, revisiones, diagnósticos, etc.).
                    </p>
                    <p><span class="ayuda-ruta">Actividades → Registros</span> lista todo lo creado; <span class="ayuda-ruta">Actividades → Nueva Actividad</span> abre el formulario para crear una.</p>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- MOTOS ATENDIDAS --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('mantenimientos') && auth()->user()->can('tenant.motos.index'))
            <section class="ayuda-modulo" id="mod-motos" data-buscar="motos atendidas historial placa visitas por unidad">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#059669;"><i class="fas fa-motorcycle"></i></div>
                    <div>
                        <h2>Motos Atendidas</h2>
                        <p>El historial de cada moto, por placa</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <p>
                        En un taller se atiende por <strong>placa</strong>, no por cliente — la misma moto puede cambiar de dueño.
                        Esta pantalla junta los 4 tipos de mantenimiento y las actividades variadas, agrupados por placa, mostrando
                        el último servicio de cada una y cuántas veces vino en total.
                    </p>
                    <p>Presiona <strong>"Ver historial"</strong> en cualquier fila para ver el historial completo de esa placa específica: cada visita, con su tipo, fecha y estado, y un enlace directo a la ficha completa de cada una.</p>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- VENTAS --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('ventas') && auth()->user()->canAny([
                'tenant.ventas.venta.index',
                'tenant.ventas.cliente.index',
                'tenant.ventas.metodopago.index',
            ]))
            <section class="ayuda-modulo" id="mod-ventas" data-buscar="ventas vender cobrar punto de venta pos boleta factura nota de venta comprobante carrito item rapido cliente credito pago mixto vuelto ventas por bahia cotizaciones metodos de pago notas de credito guias de remision cuentas por cobrar abonar sunat">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#16A34A;"><i class="fas fa-cash-register"></i></div>
                    <div>
                        <h2>Ventas</h2>
                        <p>Vender, cobrar y emitir comprobantes</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">

                    <div class="ayuda-nota warn">
                        <i class="fas fa-triangle-exclamation"></i>
                        <div>Para poder vender necesitas tener una <strong>caja abierta</strong>. Si no la tienes, el sistema te lo va a pedir antes de dejarte continuar — ver el módulo <a href="#mod-caja">Caja</a>.</div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-basket-shopping"></i> Hacer una venta</h3>
                        <ol>
                            <li>Entra a <span class="ayuda-ruta">Ventas → Crear Venta</span>. A la derecha están los productos (con buscador y filtro por categoría); a la izquierda, tu carrito.</li>
                            <li>Agrega productos con el botón <strong>+</strong> de cada tarjeta. Si necesitas cobrar algo que no está en el catálogo (una mano de obra, un cargo especial), usa <strong>"Item rápido"</strong>: solo pide nombre, cantidad y precio.</li>
                            <li>En el carrito puedes ajustar cantidad, precio o un descuento por unidad de cualquier línea.</li>
                            <li>Presiona <strong>COBRAR</strong>.</li>
                        </ol>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-receipt"></i> Finalizar la venta</h3>
                        <p>En la ventana de cobro:</p>
                        <ul>
                            <li><strong>Tipo de comprobante</strong>: Nota de Venta (documento interno, no va a SUNAT), Boleta o Factura. Si al taller le falta configurar sus datos de facturación electrónica, Boleta y Factura no aparecen disponibles.</li>
                            <li><strong>Cliente</strong>: obligatorio para Factura (con RUC), recomendado para Boleta, opcional para Nota de Venta.</li>
                            <li><strong>Método de pago</strong>: puedes activar <strong>"Pago mixto"</strong> para dividir el cobro entre dos o más métodos (ej. parte en efectivo, parte con Yape).</li>
                            <li><strong>Venta al crédito</strong>: si el cliente no paga todo ahora, marca esta opción, indica la fecha de vencimiento y, si paga algo a cuenta, el monto inicial. El saldo queda pendiente en <a href="#vta-cxc">Cuentas por Cobrar</a>.</li>
                        </ul>
                        <p>Al confirmar, el sistema descuenta el stock automáticamente y, si es Boleta o Factura, la envía a SUNAT en segundo plano (no tienes que esperar). Al terminar te ofrece imprimir o compartir el comprobante.</p>
                        <div class="ayuda-nota info">
                            <i class="fas fa-circle-info"></i>
                            <div>Una <strong>Nota de Venta</strong> del mismo día se puede convertir después en Boleta con el botón <strong>"Reemitir"</strong> desde el listado, sin tener que volver a armar el carrito.</div>
                        </div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-warehouse"></i> Ventas por Bahía</h3>
                        <p>
                            Pensado para cobrar un servicio mientras se atiende la moto, sin cerrar la cuenta hasta el final. En
                            <span class="ayuda-ruta">Ventas → Ventas por Bahía</span> ves una columna por cada bahía con las reservas
                            del día; abre una cuenta con <strong>"Abrir cuenta"</strong>, ve agregando productos o ítems rápidos
                            mientras se trabaja la moto (sin afectar stock todavía), y al terminar presiona <strong>"Cobrar"</strong> —
                            te lleva al punto de venta normal con todo precargado para completar el cobro. Si al final no se cobra
                            nada, puedes usar <strong>"Cerrar sin cobrar"</strong>.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-file-signature"></i> Cotizaciones</h3>
                        <p>
                            Un presupuesto para el cliente que todavía no mueve stock ni genera comprobante. Créala desde
                            <span class="ayuda-ruta">Ventas → Cotizaciones</span> con los productos/servicios y una fecha de
                            vencimiento. Cuando el cliente acepta, usa <strong>"Aprobar y generar venta"</strong>: te lleva al
                            punto de venta con el carrito ya armado, listo para cobrar.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-users"></i> Clientes</h3>
                        <p>
                            En <span class="ayuda-ruta">Ventas → Clientes</span> puedes registrar clientes con el ícono de lupa junto
                            al número de documento: si escribes un DNI o RUC válido, el sistema busca automáticamente el nombre (y
                            la dirección, en el caso de RUC) para no tener que tipearlo a mano.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-credit-card"></i> Métodos de Pago</h3>
                        <p><span class="ayuda-ruta">Ventas → Métodos de Pago</span>: catálogo simple (Efectivo, Yape, Plin, Transferencia, Tarjeta...). Solo un campo: el nombre del método.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-file-invoice"></i> Notas de crédito</h3>
                        <p>
                            Se emiten sobre una Boleta o Factura <strong>ya aceptada por SUNAT</strong>, para anular parte o todo
                            de lo vendido (por un error, una devolución, un descuento posterior, etc.). Eliges qué ítems y cuánta
                            cantidad acreditar, el motivo (catálogo SUNAT), y si el cliente devolvió la mercadería físicamente,
                            marcas la opción de devolver esos productos al stock.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-truck"></i> Guías de remisión</h3>
                        <p>
                            Documenta el traslado/entrega de lo vendido en una Boleta o Factura. Se indica si el transporte es
                            público (datos del transportista) o privado (placa y datos del conductor), más las direcciones de
                            partida y llegada.
                        </p>
                    </div>

                    <div class="ayuda-sub" id="vta-cxc">
                        <h3><i class="fas fa-hand-holding-dollar"></i> Cuentas por Cobrar</h3>
                        <p>
                            Lista las ventas al crédito que todavía no se han cobrado del todo. Cada una muestra el total, lo
                            abonado y el saldo. Usa <strong>"Abonar"</strong> para registrar un pago parcial o total — el abono
                            entra a la caja que tengas abierta en ese momento. Al llegar el saldo a cero, la cuenta pasa a
                            <span class="badge badge-success">Pagada</span> sola.
                        </p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- CAJA --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('ventas') && auth()->user()->can('tenant.ventas.caja.index'))
            <section class="ayuda-modulo" id="mod-caja" data-buscar="caja aperturar abrir cerrar turno sesion historial cuadre diferencia monto esperado monto real">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#0D9488;"><i class="fas fa-vault"></i></div>
                    <div>
                        <h2>Caja</h2>
                        <p>Apertura, cierre y cuadre del turno</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <div class="ayuda-sub">
                        <h3><i class="fas fa-cash-register"></i> Aperturar una caja</h3>
                        <p>En <span class="ayuda-ruta">Caja → Cajas</span> (o directo desde la campanita de la barra superior), presiona <strong>"Aperturar"</strong> junto a la caja con la que vas a trabajar e indica el <strong>monto de apertura</strong> (el efectivo con el que arrancas el turno). Desde ese momento, todas tus ventas y gastos en efectivo se contabilizan a esa caja.</p>
                    </div>
                    <div class="ayuda-sub">
                        <h3><i class="fas fa-lock"></i> Cerrar la caja</h3>
                        <p>Al terminar el turno, presiona <strong>"Cerrar"</strong> e indica el <strong>monto real contado</strong> en la caja física. El sistema calcula solo el <strong>monto esperado</strong> (apertura + ventas en efectivo + abonos cobrados − compras y gastos en efectivo pagados en el turno) y te muestra la <strong>diferencia</strong>: si sobra o falta plata frente a lo que debería haber.</p>
                    </div>
                    <div class="ayuda-sub">
                        <h3><i class="fas fa-clock-rotate-left"></i> Historial de Caja</h3>
                        <p><span class="ayuda-ruta">Caja → Historial de Caja</span> lista todos los turnos (abiertos y cerrados) de todas las cajas, con quién abrió y cerró cada uno y la diferencia de cada cierre. El botón de detalle desglosa, turno por turno, los totales por cada método de pago y el listado completo de ventas, compras y gastos de ese turno.</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- ANULACIONES --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('ventas') && auth()->user()->can('tenant.ventas.venta.index'))
            <section class="ayuda-modulo" id="mod-anulaciones" data-buscar="anulaciones anular venta comprobante baja resumen diario comunicacion de baja ticket sunat devolver stock">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#B91C1C;"><i class="fas fa-ban"></i></div>
                    <div>
                        <h2>Anulaciones</h2>
                        <p>Anular una venta ya emitida</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <p>Hay dos formas de anular, según el tipo de comprobante:</p>
                    <div class="ayuda-sub">
                        <h3><i class="fas fa-bolt"></i> Nota de Venta</h3>
                        <p>Como nunca se declaró a SUNAT, la anulación es <strong>instantánea</strong>: indicas el motivo y si quieres devolver esos productos al stock, y listo.</p>
                    </div>
                    <div class="ayuda-sub">
                        <h3><i class="fas fa-hourglass-half"></i> Boleta / Factura / Nota de crédito</h3>
                        <p>
                            Estas ya están declaradas ante SUNAT, así que la anulación se tramita como una <strong>baja oficial</strong>
                            (Boleta = "Resumen diario", Factura/Nota de crédito = "Comunicación de baja") y <strong>no es inmediata</strong>:
                            queda "en trámite" con un ticket, y usas el botón <strong>"Consultar resultado"</strong> para ver cuándo SUNAT
                            la aprueba. Recién ahí el comprobante queda realmente anulado.
                        </p>
                    </div>
                    <p><span class="ayuda-ruta">Ventas → Anulaciones</span> lista el historial completo de todas las anulaciones pedidas, resueltas o en trámite.</p>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- COMPRAS --}}
            {{-- ========================================================= --}}
            @if (tenant_has_module('compras') && auth()->user()->canAny([
                'tenant.compras.compra.index',
                'tenant.compras.proveedor.index',
                'tenant.compras.gasto.index',
                'tenant.compras.tipogasto.index',
            ]))
            <section class="ayuda-modulo" id="mod-compras" data-buscar="compras proveedores gastos tipo de gasto mercaderia lote stock ingreso">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#EA580C;"><i class="fas fa-shopping-cart"></i></div>
                    <div>
                        <h2>Compras</h2>
                        <p>Ingreso de mercadería y gastos operativos</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <div class="ayuda-nota warn">
                        <i class="fas fa-triangle-exclamation"></i>
                        <div>Igual que en Ventas, necesitas una <strong>caja abierta</strong> para registrar una compra o un gasto.</div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-truck-ramp-box"></i> Registrar una Compra</h3>
                        <p>
                            Una compra <strong>sí aumenta tu stock</strong> — sirve para registrar mercadería que entra al taller
                            para vender o usar. En <span class="ayuda-ruta">Compras → Compras</span>: elige tipo y N° de documento,
                            tipo/método de pago y el proveedor (puedes crear uno nuevo al vuelo con el botón +). Luego, por cada
                            producto: elige producto y almacén de destino, ajusta precio de compra/venta si hace falta, indica la
                            cantidad y agrégalo al carrito. Cuando termines, presiona <strong>"Generar Compra"</strong>.
                        </p>
                        <div class="ayuda-nota info">
                            <i class="fas fa-circle-info"></i>
                            <div>Al guardar, los precios de compra y venta que usaste quedan como el precio "oficial" del producto para la próxima vez. Una compra ya generada no se puede editar ni eliminar después.</div>
                        </div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-handshake"></i> Proveedores</h3>
                        <p>
                            <span class="ayuda-ruta">Compras → Proveedores</span>: documento (con búsqueda automática por DNI/RUC),
                            razón social, dirección, celular y correo. Al editar uno ya usado en compras, el tipo/número de
                            documento y la razón social quedan bloqueados para no alterar identidades ya registradas en el
                            historial.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-money-bill-wave"></i> Gastos</h3>
                        <p>
                            Un Gasto es distinto de una Compra: es dinero que sale del negocio pero <strong>no es mercadería</strong>
                            (luz, alquiler, servicios, etc.) — por eso <strong>no mueve el stock</strong>. En
                            <span class="ayuda-ruta">Compras → Gastos</span> registras fecha, tipo de documento, proveedor, tipo de
                            gasto, monto, una descripción y si ese gasto <strong>afecta o no la caja</strong> (es decir, si sale
                            del efectivo de hoy o no). Puedes adjuntar una foto del comprobante.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-tags"></i> Tipo de Gastos</h3>
                        <p>Es solo el catálogo de categorías para clasificar cada Gasto (ej. "Servicios", "Alquiler", "Mantenimiento de local").</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- INVENTARIO --}}
            {{-- ========================================================= --}}
            @if ((tenant_has_module('inventario') || tenant_has_module('productos')) && auth()->user()->canAny([
                'tenant.inventario.clase.index',
                'tenant.inventario.categoria.index',
                'tenant.inventario.almacen.index',
                'tenant.inventario.producto.index',
            ]))
            <section class="ayuda-modulo" id="mod-inventario" data-buscar="inventario productos control inventario kardex lotes traslado de stock ajuste categoria clase almacen sede stock minimo importar excel plantilla">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#CA8A04;"><i class="fas fa-boxes"></i></div>
                    <div>
                        <h2>Inventario</h2>
                        <p>Productos, stock y movimientos</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-box"></i> Productos</h3>
                        <p>En <span class="ayuda-ruta">Inventario → Productos</span> registras Categoría, Nombre, Descripción, Precio de Compra y de Venta, Marca, y opcionalmente Código Interno / Código de Fabricación y Stock Mínimo (para las alertas de stock bajo).</p>
                        <div class="ayuda-nota warn">
                            <i class="fas fa-triangle-exclamation"></i>
                            <div>Un producto nuevo <strong>nace con stock 0</strong> — el formulario de creación no tiene campo de stock inicial. El stock se genera con una <strong>Compra</strong>, un <strong>Ajuste (+)</strong>, un <strong>Traslado</strong> recibido, o importando por Excel.</div>
                        </div>
                        <p>El botón <strong>"Importar"</strong> te deja descargar una plantilla Excel, completarla (nombre, categoría, marca, precios, stock inicial, stock mínimo) y subirla: crea los productos nuevos y a los que ya existen simplemente les suma el stock indicado.</p>
                        <p>"Eliminar" un producto con historial no lo borra: lo <strong>desactiva</strong> (puedes reactivarlo después).</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-warehouse"></i> Control Inventario</h3>
                        <p>
                            La vista general de existencias: stock total de cada producto (sumando todos los almacenes), con un
                            semáforo de color (rojo = agotado, amarillo = en el mínimo o menos, verde = bien) y una alerta arriba
                            si hay productos en stock crítico. Desde cada fila puedes abrir:
                        </p>
                        <ul>
                            <li><strong>Lotes</strong>: cada ingreso de stock de ese producto (fecha, cantidad, si sigue disponible).</li>
                            <li><strong>Kardex</strong>: el historial completo de movimientos (entradas y salidas) con filtro por fecha.</li>
                        </ul>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-right-left"></i> Traslado de Stock</h3>
                        <p>Mueve stock de un almacén a otro (no aumenta ni disminuye el total, solo cambia de ubicación). Elige almacén origen y destino, agrega los productos con la cantidad a mover (no puede superar lo disponible en el origen) y confirma con <strong>"Trasladar"</strong>.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-sliders"></i> Ajuste de Inventario</h3>
                        <p>
                            Para corregir el stock cuando <strong>no</strong> viene de una compra, venta o traslado: mermas, roturas,
                            vencimientos o diferencias encontradas en un conteo físico. Elige almacén y un <strong>motivo</strong>
                            obligatorio (Merma, Rotura, Vencimiento, Diferencia de conteo, Otro), y por cada producto indica si es
                            para <strong>Agregar (+)</strong> o <strong>Quitar (−)</strong> y la cantidad.
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-sitemap"></i> Categorías y Clases</h3>
                        <p>Es una jerarquía de dos niveles: la <strong>Clase</strong> es el grupo más amplio (ej. "Repuestos", "Accesorios", "Lubricantes"), y la <strong>Categoría</strong> es un subgrupo dentro de una Clase. Cada producto se clasifica por su Categoría.</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- REPORTES --}}
            {{-- ========================================================= --}}
            @if (auth()->user()->canAny([
                'tenant.reportes.listageneral',
                'tenant.reportes.rendimientomecanicos',
                'tenant.reportes.rentabilidad',
                'tenant.reportes.inventario',
                'tenant.reportes.comprasGastos',
                'tenant.reportes.clientes',
                'tenant.reportes.caja',
                'tenant.reportes.operacionTaller',
            ]))
            <section class="ayuda-modulo" id="mod-reportes" data-buscar="reportes lista general rendimiento de mecanicos rentabilidad inventario valorizado compras y gastos clientes caja operacion del taller">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#4338CA;"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <h2>Reportes</h2>
                        <p>Los números del negocio</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <p>La mayoría de reportes te dejan elegir un periodo (hoy / semana / mes / personalizado) y filtrar por sede, y comparan contra el periodo anterior equivalente.</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead><tr><th style="width:22%">Reporte</th><th>Para qué sirve</th></tr></thead>
                            <tbody>
                                <tr><td><strong>Lista General</strong></td><td>Todos los mantenimientos y actividades de los 4 tipos, en una sola tabla — con accesos directos a Orden de Servicio y Estado de Recepción de cada uno.</td></tr>
                                <tr><td><strong>Rendimiento de Mecánicos</strong></td><td>Cuántos trabajos terminó cada mecánico en el periodo, desglosado por tipo, y el tiempo promedio de atención — para evaluar carga de trabajo.</td></tr>
                                <tr><td><strong>Rentabilidad</strong></td><td>Ingresos menos el costo real de lo vendido menos gastos del periodo = utilidad neta. Incluye el top de productos más rentables y una gráfica de la evolución día a día.</td></tr>
                                <tr><td><strong>Inventario Valorizado</strong></td><td>Cuánto capital tienes inmovilizado en stock ahora mismo, y qué productos con stock <strong>no se vendieron ni una vez</strong> en el periodo (posible stock muerto).</td></tr>
                                <tr><td><strong>Compras y Gastos</strong></td><td>A dónde se va la plata: compras y gastos del periodo, desglosados por proveedor y por tipo de gasto.</td></tr>
                                <tr><td><strong>Clientes</strong></td><td>Quiénes compran más (para fidelizar) y cuántos clientes nuevos se registraron — para medir si la cartera de clientes está creciendo.</td></tr>
                                <tr><td><strong>Caja</strong></td><td>Cuadre acumulado: cuántas sesiones de caja cerraron con diferencia (sobrante o faltante) y de qué cajero, para detectar patrones.</td></tr>
                                <tr><td><strong>Operación del Taller</strong></td><td>Cuántas reservas se cumplen frente a las que se rechazan o quedan pendientes, y cómo se reparte la ocupación entre bahías y turnos.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- CONFIGURACIÓN --}}
            {{-- ========================================================= --}}
            @if (auth()->user()->canAny([
                'tenant.configuracion.empresa.index',
                'tenant.configuracion.sede.index',
                'tenant.configuracion.turno.index',
                'tenant.configuracion.bahia.index',
                'tenant.configuracion.horario.index',
                'tenant.configuracion.recepcion.index',
            ]))
            <section class="ayuda-modulo" id="mod-configuracion" data-buscar="configuracion asistente empresa sedes almacen turnos bahias horarios estado de recepcion catalogo sunat certificado digital serie ruc razon social logo colores de marca tema">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#475569;"><i class="fas fa-cogs"></i></div>
                    <div>
                        <h2>Configuración</h2>
                        <p>Los ajustes generales del taller</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">

                    <div class="ayuda-nota tip">
                        <i class="fas fa-wand-magic-sparkles"></i>
                        <div>Si es la primera vez que usas el sistema, empieza por <strong>Configuración → Asistente de Configuración</strong>: un wizard de 3 pasos (cuántas bahías tienes, cuántos turnos manejas, qué días trabajas) que te arma automáticamente las Bahías, Turnos y Horarios. Solo funciona si esas tres cosas están vacías todavía.</div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-building"></i> Empresa</h3>
                        <p>Los datos de tu negocio para la facturación electrónica: RUC, razón social, dirección, contacto, y el bloque de <strong>SUNAT</strong> (ambiente de pruebas o producción, usuario y clave SOL, certificado digital). También el logo del sistema, el logo de los PDF, y los colores de marca (con los que se pintan los documentos que genera el sistema).</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-store"></i> Sedes</h3>
                        <p>Un taller puede tener varias sucursales (según lo permita tu plan). Cada Sede es también el <strong>Almacén</strong> que eliges en Compras, Traslados y Ajustes — es el mismo registro visto desde dos módulos distintos. Aquí también se configuran, por sede, las <strong>series de comprobantes</strong> (Boleta, Factura, Guías, Notas de Crédito) y si esa sede permite vender sin stock disponible.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-user-clock"></i> Turnos</h3>
                        <p>Bloques horarios con nombre (ej. "Turno Mañana") y una descripción libre (ej. "08:00 - 13:00"), que luego se usan al armar los Horarios y en la grilla de Reservas.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-warehouse"></i> Bahías</h3>
                        <p>Cada Bahía es un espacio físico de trabajo del taller (un elevador o puesto donde se atiende una moto a la vez), con una Sede y un Responsable asignados. Son las columnas que ves en <a href="#mod-proceso">Gestión de Proceso</a> y en <a href="#mod-ventas">Ventas por Bahía</a>, y el destino de cada reserva.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-clock"></i> Horarios</h3>
                        <p>Combina Sede + Día de la semana + Turno para definir qué días y turnos están <strong>habilitados para reservar</strong> en cada sede. Si un día/turno no tiene Horario configurado, aparece gris (no reservable) en la grilla de Reservas.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-clipboard-check"></i> Estado de Recepción</h3>
                        <p>Administra el catálogo de categorías e ítems del checklist de "cómo llegó la moto" (ver <a href="#mant-checklist-recepcion">Mantenimientos → Estado de Recepción</a>): puedes agregar, renombrar, reordenar o desactivar ítems y categorías sin tocar código. Los ítems desactivados no desaparecen del historial de recepciones ya guardadas, solo dejan de mostrarse en formularios nuevos.</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- SEGURIDAD --}}
            {{-- ========================================================= --}}
            @if (auth()->user()->canAny([
                'tenant.seguridad.permiso.index',
                'tenant.seguridad.role.index',
                'tenant.seguridad.users.index',
            ]))
            <section class="ayuda-modulo" id="mod-seguridad" data-buscar="seguridad roles permisos usuarios crear usuario asignar rol contraseña acceso total">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#1E293B;"><i class="fas fa-lock"></i></div>
                    <div>
                        <h2>Seguridad</h2>
                        <p>Usuarios, roles y quién puede hacer qué</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-user-plus"></i> Crear un usuario</h3>
                        <p><span class="ayuda-ruta">Seguridad → Usuario</span>: nombre, correo y contraseña. Un usuario recién creado <strong>no tiene rol asignado todavía</strong> — eso se hace en un segundo paso.</p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-user-tag"></i> Asignar un rol</h3>
                        <p>Entra a <strong>Editar</strong> sobre ese usuario y marca el (o los) rol(es) que le corresponden de la lista de checkboxes. Al guardar, esos roles <strong>reemplazan</strong> los que tenía antes (no se suman).</p>
                        <div class="ayuda-nota warn">
                            <i class="fas fa-triangle-exclamation"></i>
                            <div>Tu plan contratado limita cuántos usuarios puedes crear. Si llegaste al tope, el sistema te avisa al intentar crear uno más.</div>
                        </div>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-users-gear"></i> Crear o editar un Rol</h3>
                        <p>
                            <span class="ayuda-ruta">Seguridad → Roles</span>: ponle un nombre y marca los permisos, agrupados por
                            módulo en bloques que puedes expandir/colapsar. Dos atajos: <strong>"Acceso Total"</strong> marca todo,
                            <strong>"Ningún Acceso"</strong> deja el rol sin permisos. Ver la sección <a href="#mod-roles">Roles y
                            permisos</a> más arriba para los roles típicos de un taller (Recepción, Mecánico, Ventas, Compras...).
                        </p>
                    </div>

                    <div class="ayuda-sub">
                        <h3><i class="fas fa-key"></i> Permisos</h3>
                        <p>Es el catálogo técnico de permisos individuales del sistema. Normalmente no necesitas tocar esta pantalla — los permisos ya vienen precargados; solo se usa si en algún momento se necesita dar de alta un permiso nuevo a medida.</p>
                    </div>
                </div>
            </section>
            @endif

            {{-- ========================================================= --}}
            {{-- MI FACTURACIÓN --}}
            {{-- ========================================================= --}}
            @hasanyrole('Admin|Gerente')
            <section class="ayuda-modulo" id="mod-facturacion" data-buscar="mi facturacion plan suscripcion pago culqi ciclo vencimiento historial de pagos">
                <div class="ayuda-modulo-header">
                    <div class="ayuda-modulo-icono" style="background:#9333EA;"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div>
                        <h2>Mi Facturación</h2>
                        <p>Tu suscripción al sistema (no la facturación SUNAT de tus ventas)</p>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </div>
                <div class="ayuda-modulo-body">
                    <div class="ayuda-nota info">
                        <i class="fas fa-circle-info"></i>
                        <div>Esta pantalla es distinta de la facturación electrónica de tus clientes (esa vive en <a href="#mod-ventas">Ventas</a> y en <a href="#mod-configuracion">Configuración → Empresa</a>). "Mi Facturación" es tu propia suscripción como cliente de este sistema.</div>
                    </div>
                    <p>Muestra el plan que tienes contratado, el monto del ciclo actual, la fecha de cobro (o de fin de la prueba gratuita, si aún estás en periodo de prueba) y tu estado: <span class="badge badge-info">En prueba</span>, <span class="badge badge-success">Pagado</span>, <span class="badge badge-warning">Por vencer</span> o <span class="badge badge-danger">Vencido</span>. Si tu plan está por vencer o vencido, aparece el botón <strong>"Pagar ahora"</strong>. Más abajo tienes el historial de tus últimos pagos.</p>
                </div>
            </section>
            @endhasanyrole

        </div>
    </div>

    <div id="ayudaSinResultados">
        <i class="fas fa-magnifying-glass-minus fa-2x mb-2"></i>
        <p class="mb-0">No encontramos ningún tema que coincida con tu búsqueda.</p>
    </div>

</div>
@endsection

@section('script')
<script>
$(function () {
    const $modulos = $('.ayuda-modulo');
    const $toc = $('#ayudaToc');

    // ---- Abrir / cerrar un módulo ----
    function toggle($mod, forzar) {
        const abrir = typeof forzar === 'boolean' ? forzar : !$mod.hasClass('abierto');
        $mod.toggleClass('abierto', abrir);
    }

    $('.ayuda-modulo-header').on('click', function () {
        toggle($(this).closest('.ayuda-modulo'));
    });

    $('#btnExpandirTodo').on('click', function () { $modulos.addClass('abierto'); });
    $('#btnColapsarTodo').on('click', function () { $modulos.removeClass('abierto'); });

    $('#btnTocMovil').on('click', function () { $toc.toggleClass('visible-mobile'); });

    // ---- Ir a una sección: desde el índice, o desde un enlace cruzado
    // dentro del propio contenido (ej. "ver Planes de Mantenimiento" citado
    // desde otro módulo). En ambos casos hay que abrir el acordeón del
    // módulo — si el destino queda adentro de un módulo colapsado, saltar
    // con el <a href="#..."> nativo del navegador no muestra nada porque el
    // contenido tiene display:none. ----
    $('#ayudaContent, #ayudaToc').on('click', 'a[href^="#mod-"], a[href^="#mant-"], a[href^="#vta-"]', function (e) {
        e.preventDefault();
        const id = $(this).attr('href');
        const $destino = $(id);
        if (!$destino.length) return;

        const $mod = $destino.hasClass('ayuda-modulo') ? $destino : $destino.closest('.ayuda-modulo');
        toggle($mod, true);

        $toc.find('a').removeClass('activo');
        $toc.find('a[href="#' + $mod.attr('id') + '"]').addClass('activo');
        $toc.removeClass('visible-mobile');

        setTimeout(function () {
            $('html, body').animate({ scrollTop: $destino.offset().top - 12 }, 350);
        }, 60);
    });

    // ---- Buscador ----
    const $buscar = $('#ayudaBuscar');
    const $sinResultados = $('#ayudaSinResultados');

    // Sin tildes/diacríticos y en minúsculas, para que "credito"/"último"
    // encuentren "crédito"/"ultimo" sin importar cómo lo haya tipeado el
    // usuario ni cómo esté escrito el texto real.
    function normalizar(str) {
        return (str || '')
            .toString()
            .normalize('NFD')
            .replace(/[̀-ͯ]/g, '')
            .toLowerCase();
    }

    function limpiarResaltado($mod) {
        $mod.find('mark.ayuda-hl').each(function () {
            const $m = $(this);
            $m.replaceWith($m.text());
        });
    }

    // Une cada palabra buscada (ya sin tildes) en un patrón que SÍ acepta
    // tildes en el texto real (así "credito" resalta "crédito" tal cual
    // está escrito, sin romper el acento en pantalla).
    const VOCAL_CON_TILDE = { a: '[aá]', e: '[eé]', i: '[ií]', o: '[oó]', u: '[uú]' };
    function patronConTildes(palabra) {
        return palabra
            .replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
            .replace(/[aeiou]/g, (v) => VOCAL_CON_TILDE[v]);
    }

    function resaltar($mod, palabras) {
        if (!palabras.length) return;
        const re = new RegExp('(' + palabras.map(patronConTildes).join('|') + ')', 'gi');
        $mod.find('.ayuda-modulo-header p, .ayuda-sub p, .ayuda-sub li, .ayuda-sub h3').each(function () {
            const $el = $(this);
            if ($el.find('*').not('i, mark').length) return; // no tocar nodos con hijos complejos (solo texto simple)
            const html = $el.html();
            if (html && re.test(html)) {
                $el.html(html.replace(re, '<mark class="ayuda-hl">$1</mark>'));
            }
        });
    }

    let timerBusqueda = null;
    $buscar.on('input', function () {
        clearTimeout(timerBusqueda);
        const qCrudo = $(this).val().trim();
        const palabras = normalizar(qCrudo).split(/\s+/).filter(Boolean);

        timerBusqueda = setTimeout(function () {
            if (palabras.length === 0) {
                $modulos.show().each(function () { limpiarResaltado($(this)); });
                $modulos.removeClass('abierto');
                $('#mod-intro').addClass('abierto');
                $sinResultados.hide();
                return;
            }

            let algunaVisible = false;
            $modulos.each(function () {
                const $mod = $(this);
                limpiarResaltado($mod);
                const texto = normalizar($mod.data('buscar') + ' ' + $mod.text());
                // Coincide si TODAS las palabras buscadas aparecen en algún
                // lado del módulo (no hace falta que sea la frase exacta ni
                // en el mismo orden).
                const coincide = palabras.every(p => texto.indexOf(p) !== -1);
                $mod.toggle(coincide);
                if (coincide) {
                    algunaVisible = true;
                    $mod.addClass('abierto');
                    resaltar($mod, palabras);
                }
            });
            $sinResultados.toggle(!algunaVisible);
        }, 180);
    });

    // ---- Resaltar en el índice el módulo visible al hacer scroll ----
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                $toc.find('a').removeClass('activo');
                $toc.find('a[href="#' + entry.target.id + '"]').addClass('activo');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    $modulos.each(function () { observer.observe(this); });
});
</script>
@endsection
