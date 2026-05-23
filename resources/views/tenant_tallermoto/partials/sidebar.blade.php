<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/tenant/home" class="brand-link navbar-light">
        <img id="avatarImageHeader" class="brand-image img-circle elevation-3" alt="User Image">
        <span class="brand-text font-weight-light">
            <img src="{{ asset_root('/adminlte/dist/img/logo.png') }}" alt="AdminLTE Logo" style="height:30px"> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="/tenant/home" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Menu Principal
                        </p>
                    </a>
                </li>

                <!-- RESERVAS -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>
                            RESERVAS
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Semanal Chepén</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Semanal Pacasmayo</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-list-alt nav-icon"></i>
                                <p>Todas las Reservas</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- REPORTES -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            REPORTES
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-file-alt nav-icon"></i>
                                <p>Reporte General</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- MANTENIMIENTOS -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            MANTENIMIENTOS
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <!-- GENERAL -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-wrench text-primary"></i>
                                <p>
                                    General
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fas fa-motorcycle nav-icon text-danger"></i>
                                        <p>Inyectadas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fas fa-motorcycle nav-icon text-secondary"></i>
                                        <p>Carburadas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- PREVENTIVO -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-shield-alt text-success"></i>
                                <p>
                                    Preventivo
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fas fa-motorcycle nav-icon text-danger"></i>
                                        <p>Inyectadas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fas fa-motorcycle nav-icon text-secondary"></i>
                                        <p>Carburadas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- ACTIVIDADES -->
                <li
                    class="nav-item has-treeview 
                    {{ request()->routeIs('tenant.actividaes.mateniemientoactividadvariada*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('tenant.actividaes.mateniemientoactividadvariada*') ? 'active': '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            ACTIVIDADES
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ tenant_url('tenant.actividades.mateniemientoactividadvariada.index') }}" 
                                class="nav-link {{ request()->routeIs('tenant.actividades.mateniemientoactividadvariada.index') ? 'active' : '' }}">
                                <i class="far fa-list-alt nav-icon"></i>
                                <p>Registros</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ tenant_url('tenant.actividades.mateniemientoactividadvariada.create') }}" 
                                class="nav-link {{ request()->routeIs('tenant.actividades.mateniemientoactividadvariada.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle nav-icon"></i>
                                <p>Nueva Actividad</p>
                            </a>
                        </li>

                    </ul>
                </li>

                @can('tenant.inventario.clase.index')
                    <li
                        class="nav-item has-treeview 
                {{ request()->routeIs('tenant.inventario.clase*') ||
                request()->routeIs('tenant.inventario.categoria*') ||
                request()->routeIs('tenant.inventario.almacen*') ||
                request()->routeIs('tenant.inventario.producto*') ||
                request()->routeIs('tenant.inventario.controlinventario*')
                    ? 'menu-open'
                    : '' }}">
                        <a href="#"
                            class="nav-link 
                    {{ request()->routeIs('tenant.inventario.clase*') ||
                    request()->routeIs('tenant.inventario.categoria*') ||
                    request()->routeIs('tenant.inventario.almacen*') ||
                    request()->routeIs('tenant.inventario.producto*') ||
                    request()->routeIs('tenant.inventario.controlinventario*')
                        ? 'active'
                        : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>
                                INVENTARIO
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('tenant.inventario.producto.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.inventario.controlinventario.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.inventario.controlinventario.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Control Inventario</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.inventario.producto.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.inventario.producto*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Productos</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.inventario.categoria.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.inventario.categoria.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.inventario.categoria*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Categorias</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.inventario.clase.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.inventario.clase.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.inventario.clase*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Clases</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan
                @can('tenant.compras.tipogasto.index')
                    <li
                        class="nav-item has-treeview 
                {{ request()->routeIs('tenant.compras.compra*') ||
                request()->routeIs('tenant.compras.proveedor*') ||
                request()->routeIs('tenant.compras.gasto*') ||
                request()->routeIs('tenant.compras.tipogasto*')
                    ? 'menu-open'
                    : '' }}">
                        <a href="#"
                            class="nav-link 
                    {{ request()->routeIs('tenant.compras.compra*') ||
                    request()->routeIs('tenant.compras.proveedor*') ||
                    request()->routeIs('tenant.compras.gasto*') ||
                    request()->routeIs('tenant.compras.tipogasto*')
                        ? 'active'
                        : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                COMPRAS
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('tenant.compras.compra.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.compras.compra.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.compras.compra*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Compras</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.compras.proveedor.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.compras.proveedor.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.compras.proveedor*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Proveedores</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.compras.gasto.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.compras.gasto.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.compras.gasto*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gastos</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.compras.tipogasto.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.compras.tipogasto.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.compras.tipogasto*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tipo de Gastos</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                @can('tenant.ventas.metodopago.index')
                    <li
                        class="nav-item has-treeview 
                            {{ request()->routeIs('tenant.ventas.metodopago*') ||
                            request()->routeIs('tenant.ventas.cliente*') ||
                            request()->routeIs('tenant.ventas.venta*')
                                ? 'menu-open'
                                : '' }}">
                        <a href="#"
                            class="nav-link 
                                {{ request()->routeIs('tenant.ventas.metodopago*') ||
                                request()->routeIs('tenant.ventas.cliente*') ||
                                request()->routeIs('tenant.ventas.venta*')
                                    ? 'active'
                                    : '' }}">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>
                                VENTAS
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('tenant.ventas.venta.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.ventas.venta.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.ventas.venta*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Ventas</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.ventas.cliente.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.ventas.cliente.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.ventas.cliente*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Clientes</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.ventas.metodopago.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.ventas.metodopago.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.ventas.metodopago*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Métodos de Pago</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                <!-- CONFIGURACIÓN -->
                
                @can('tenant.configuracion.sede.index')
                    <li class="nav-item has-treeview 
                        {{ request()->routeIs('tenant.configuracion.sede*') ||
                            request()->routeIs('tenant.configuracion.turno*') ||
                            request()->routeIs('tenant.configuracion.bahia*') ||
                            request()->routeIs('tenant.configuracion.horario*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('tenant.configuracion.sede*') ||
                            request()->routeIs('tenant.configuracion.turno*') ||
                            request()->routeIs('tenant.configuracion.bahia*') ||
                            request()->routeIs('tenant.configuracion.horario*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                CONFIGURACIÓN
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- SEDES -->
                            <li class="nav-item">
                                <a href="{{ tenant_url('tenant.configuracion.sede.index') }}" 
                                    class="nav-link  {{ request()->routeIs('tenant.configuracion.sede*') ? 'active' : '' }}">
                                    <i class="fas fa-store nav-icon"></i>
                                    <p>Sedes</p>
                                </a>
                            </li>
                            <!-- TURNOS -->
                            <li class="nav-item">
                                <a href="{{ tenant_url('tenant.configuracion.turno.index') }}" 
                                    class="nav-link  {{ request()->routeIs('tenant.configuracion.turno*') ? 'active' : '' }}">
                                    <i class="fas fa-user-clock nav-icon"></i>
                                    <p>Turnos</p>
                                </a>
                            </li>
                            <!-- BAHÍAS -->
                            <li class="nav-item">
                                <a href="{{ tenant_url('tenant.configuracion.bahia.index') }}" 
                                    class="nav-link  {{ request()->routeIs('tenant.configuracion.bahia*') ? 'active' : '' }}">
                                    <i class="fas fa-warehouse nav-icon"></i>
                                    <p>Bahías</p>
                                </a>
                            </li>
                            <!-- HORARIOS -->
                            <li class="nav-item">
                                <a href="{{ tenant_url('tenant.configuracion.horario.index') }}" 
                                    class="nav-link  {{ request()->routeIs('tenant.configuracion.horario*') ? 'active' : '' }}">
                                    <i class="fas fa-clock nav-icon"></i>
                                    <p>Horarios</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('tenant.seguridad.users.index')
                    <li class="nav-item has-treeview {{ request()->routeIs('tenant.seguridad.usuario*') || request()->routeIs('tenant.seguridad.permiso*') || request()->routeIs('tenant.seguridad.rol*') ? 'menu-open' : '' }}"
                        id="idCabSeguridad">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('tenant.seguridad.usuario*') || request()->routeIs('tenant.seguridad.permiso*') || request()->routeIs('tenant.seguridad.rol*') ? 'active' : '' }}"
                            id="idSeguridad">
                            <i class="nav-icon fas fa-lock"></i>
                            <p>
                                SEGURIDAD
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('tenant.seguridad.permiso.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.seguridad.permiso.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.seguridad.permiso*') ? 'active' : '' }}"
                                        id="idSegPermiso">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Permiso</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.seguridad.roles.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.seguridad.role.index') }}"
                                        class="nav-link  {{ request()->routeIs('tenant.seguridad.role*') ? 'active' : '' }}"
                                        id="idSegRoles">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tenant.seguridad.users.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('tenant.seguridad.usuario.index') }}"
                                        class="nav-link {{ request()->routeIs('tenant.seguridad.usuario*') ? 'active' : '' }}"
                                        id="idSegUsuario">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Usuario</p>
                                    </a>
                                </li>
                            @endcan
                            @can('grupo.index')
                                <li class="nav-item">
                                    <a href="{{ tenant_url('seguridad.grupo.index') }}" class="nav-link" id="idSegGrupo">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Grupo</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
