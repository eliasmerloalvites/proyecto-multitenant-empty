<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/tenant/home" class="brand-link navbar-light">
      <img id="avatarImageHeader" class="brand-image img-circle elevation-3" alt="User Image">
      <span class="brand-text font-weight-light">
          <img src="{{asset_root('/adminlte/dist/img/logo.png') }}" alt="AdminLTE Logo" style="height:30px"> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="/tenant/home" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>
                Menu Principal
              </p>
            </a>
          </li>
            @can('tenant.inventario.clase.index')
            <li class="nav-item has-treeview 
                {{ request()->routeIs('tenant.inventario.clase*') || 
                request()->routeIs('tenant.inventario.categoria*') || 
                request()->routeIs('tenant.inventario.almacen*') ||
                request()->routeIs('tenant.inventario.producto*') ? 'menu-open' : '' }}" >
                <a href="#"
                    class="nav-link 
                    {{ request()->routeIs('tenant.inventario.clase*') || 
                    request()->routeIs('tenant.inventario.categoria*') || 
                    request()->routeIs('tenant.inventario.almacen*') ||
                    request()->routeIs('tenant.inventario.producto*')  ? 'active' : '' }}" >
                    <i class="nav-icon fas fa-boxes"></i>
                    <p>
                        INVENTARIO
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                        @can('tenant.inventario.producto.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.inventario.producto.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.inventario.producto*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Productos</p>
                            </a>
                        </li>
                        @endcan
                        @can('tenant.inventario.categoria.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.inventario.categoria.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.inventario.categoria*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categorias</p>
                            </a>
                        </li>
                        @endcan
                        @can('tenant.inventario.clase.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.inventario.clase.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.inventario.clase*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Clases</p>
                            </a>
                        </li>
                        @endcan
                        @can('tenant.inventario.almacen.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.inventario.almacen.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.inventario.almacen*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Almacenes</p>
                            </a>
                        </li>
                        @endcan
                        
                </ul>
            </li>
            @endcan
            @can('tenant.compras.tipogasto.index')
            <li class="nav-item has-treeview 
                {{ request()->routeIs('tenant.compras.compra*') ||
                request()->routeIs('tenant.compras.proveedor*') ||
                request()->routeIs('tenant.compras.gasto*') ||
                request()->routeIs('tenant.compras.tipogasto*')  ? 'menu-open' : '' }}" >
                <a href="#"
                    class="nav-link 
                    {{ request()->routeIs('tenant.compras.compra*') ||
                    request()->routeIs('tenant.compras.proveedor*') ||
                    request()->routeIs('tenant.compras.gasto*') ||
                    request()->routeIs('tenant.compras.tipogasto*')  ? 'active' : '' }}" >
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>
                        COMPRAS
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                        @can('tenant.compras.compra.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.compras.compra.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.compras.compra*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Compras</p>
                            </a>
                        </li>
                        @endcan
                        @can('tenant.compras.proveedor.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.compras.proveedor.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.compras.proveedor*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Proveedores</p>
                            </a>
                        </li>
                        @endcan
                        @can('tenant.compras.gasto.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.compras.gasto.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.compras.gasto*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gastos</p>
                            </a>
                        </li>
                        @endcan
                        @can('tenant.compras.tipogasto.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.compras.tipogasto.index', tenant('id')) }}"
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
            <li class="nav-item has-treeview 
                {{ request()->routeIs('tenant.ventas.metodopago*') ||
                request()->routeIs('tenant.ventas.cliente*') ||
                request()->routeIs('tenant.ventas.venta*')  ? 'menu-open' : '' }}" >
                <a href="#"
                    class="nav-link 
                    {{ request()->routeIs('tenant.ventas.metodopago*') ||
                    request()->routeIs('tenant.ventas.cliente*') ||
                    request()->routeIs('tenant.ventas.venta*')  ? 'active' : '' }}" >
                    <i class="nav-icon fas fa-cash-register"></i>
                    <p>
                        VENTAS
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('tenant.ventas.venta.index')
                    <li class="nav-item">
                        <a href="{{ route('tenant.ventas.venta.index', tenant('id')) }}"
                            class="nav-link {{ request()->routeIs('tenant.ventas.venta*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Ventas</p>
                        </a>
                    </li>
                    @endcan
                    @can('tenant.ventas.cliente.index')
                    <li class="nav-item">
                        <a href="{{ route('tenant.ventas.cliente.index', tenant('id')) }}"
                            class="nav-link {{ request()->routeIs('tenant.ventas.cliente*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Clientes</p>
                        </a>
                    </li>
                    @endcan
                    @can('tenant.ventas.metodopago.index')
                    <li class="nav-item">
                        <a href="{{ route('tenant.ventas.metodopago.index', tenant('id')) }}"
                            class="nav-link {{ request()->routeIs('tenant.ventas.metodopago*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Métodos de Pago</p>
                        </a>
                    </li>
                    @endcan
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
                            <a href="{{ route('tenant.seguridad.permiso.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.seguridad.permiso*') ? 'active' : '' }}"
                                id="idSegPermiso">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permiso</p>
                            </a>
                        </li>
                    @endcan
                    @can('tenant.seguridad.roles.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.seguridad.role.index', tenant('id')) }}"
                                class="nav-link  {{ request()->routeIs('tenant.seguridad.role*') ? 'active' : '' }}"
                                id="idSegRoles">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                    @endcan
                    @can('tenant.seguridad.users.index')
                        <li class="nav-item">
                            <a href="{{ route('tenant.seguridad.usuario.index', tenant('id')) }}"
                                class="nav-link {{ request()->routeIs('tenant.seguridad.usuario*') ? 'active' : '' }}"
                                id="idSegUsuario">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuario</p>
                            </a>
                        </li>
                    @endcan
                    @can('grupo.index')
                        <li class="nav-item">
                            <a href="{{ route('seguridad.grupo.index') }}" class="nav-link" id="idSegGrupo">
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