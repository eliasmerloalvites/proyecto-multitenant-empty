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
                    <i class="nav-icon fas fa-lock"></i>
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
                </ul>
            </li>
        @endcan
        
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