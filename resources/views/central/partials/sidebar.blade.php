<aside class="main-sidebar sidebar-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/home" class="brand-link navbar-light">
      <img id="avatarImageHeader" class="brand-image img-circle elevation-3" alt="User Image">
      <span class="brand-text font-weight-light">
          <img src="{{asset_root('adminlte/dist/img/logo.png') }}" alt="AdminLTE Logo" style="height:30px"> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="/home" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Menu Principal
              </p>
            </a>
          </li>
          @can('admin.clients.index')
            <li class="nav-item has-treeview {{ request()->routeIs('admin.clients*')  ? 'menu-open' : '' }}"
                id="idCabSeguridad">
                <a href="#"
                    class="nav-link {{ request()->routeIs('admin.clients*')  ? 'active' : '' }}"
                    id="idSeguridad">
                    <i class="nav-icon fas fa-cogs"></i>
                    <p>
                        ADMINISTRACIÓN
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('admin.clients.index')
                        <li class="nav-item">
                            <a href="{{ route('admin.clients.index') }}"
                                class="nav-link {{ request()->routeIs('admin.clients*') ? 'active' : '' }}"
                                id="idSegClientes">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Clientes</p>
                            </a>
                        </li>
                    @endcan
                    @can('admin.cobros.index')
                        <li class="nav-item">
                            <a href="{{ route('admin.cobros.index') }}"
                                class="nav-link {{ request()->routeIs('admin.cobros*') ? 'active' : '' }}"
                                id="idSegCobros">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cobros</p>
                            </a>
                        </li>
                    @endcan
                    @can('admin.planes.index')
                        <li class="nav-item">
                            <a href="{{ route('admin.planes.index') }}"
                                class="nav-link {{ request()->routeIs('admin.planes*') ? 'active' : '' }}"
                                id="idSegPlanes">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Planes</p>
                            </a>
                        </li>
                    @endcan
                    @can('admin.auditoria.index')
                        <li class="nav-item">
                            <a href="{{ route('admin.auditoria.index') }}"
                                class="nav-link {{ request()->routeIs('admin.auditoria*') ? 'active' : '' }}"
                                id="idSegAuditoria">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Auditoría</p>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        @canany(['admin.vendedores.index', 'admin.comisiones.index'])
            <li class="nav-item has-treeview {{ request()->routeIs('admin.vendedores*') || request()->routeIs('admin.comisiones*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.vendedores*') || request()->routeIs('admin.comisiones*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-handshake"></i>
                    <p>
                        VENDEDORES
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('admin.vendedores.index')
                        <li class="nav-item">
                            <a href="{{ route('admin.vendedores.index') }}"
                                class="nav-link {{ request()->routeIs('admin.vendedores*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vendedores</p>
                            </a>
                        </li>
                    @endcan
                    @can('admin.comisiones.index')
                        <li class="nav-item">
                            <a href="{{ route('admin.comisiones.index') }}"
                                class="nav-link {{ request()->routeIs('admin.comisiones*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Comisiones</p>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['vendedor.clientes.index', 'vendedor.comisiones.index'])
            <li class="nav-item has-treeview {{ request()->routeIs('vendedor.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('vendedor.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>
                        MI PANEL
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('vendedor.clientes.index')
                        <li class="nav-item">
                            <a href="{{ route('vendedor.clientes.index') }}"
                                class="nav-link {{ request()->routeIs('vendedor.clientes*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mis Clientes</p>
                            </a>
                        </li>
                    @endcan
                    @can('vendedor.comisiones.index')
                        <li class="nav-item">
                            <a href="{{ route('vendedor.comisiones.index') }}"
                                class="nav-link {{ request()->routeIs('vendedor.comisiones*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mis Comisiones</p>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

          @can('seguridad.users.index')
            <li class="nav-item has-treeview {{ request()->routeIs('usuario*') || request()->routeIs('permiso*') || request()->routeIs('rol*') ? 'menu-open' : '' }}"
                id="idCabSeguridad">
                <a href="#"
                    class="nav-link {{ request()->routeIs('usuario*') || request()->routeIs('permisos*') || request()->routeIs('roles*') ? 'active' : '' }}"
                    id="idSeguridad">
                    <i class="nav-icon fas fa-lock"></i>
                    <p>
                        SEGURIDAD
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('seguridad.permiso.index')
                        <li class="nav-item">
                            <a href="{{ route('permiso.index') }}"
                                class="nav-link {{ request()->routeIs('permiso*') ? 'active' : '' }}"
                                id="idSegPermiso">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permiso</p>
                            </a>
                        </li>
                    @endcan
                    @can('seguridad.roles.index')
                        <li class="nav-item">
                            <a href="{{ route('role.index') }}"
                                class="nav-link  {{ request()->routeIs('rol*') ? 'active' : '' }}"
                                id="idSegRoles">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                    @endcan
                    @can('seguridad.users.index')
                        <li class="nav-item">
                            <a href="{{ route('usuario.index') }}"
                                class="nav-link {{ request()->routeIs('usuario*') ? 'active' : '' }}"
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