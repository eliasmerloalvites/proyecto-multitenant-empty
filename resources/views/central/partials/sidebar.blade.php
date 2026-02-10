<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/home" class="brand-link navbar-light">
      <img id="avatarImageHeader" class="brand-image img-circle elevation-3" alt="User Image">
      <span class="brand-text font-weight-light">
          <img src="{{asset_root('adminlte/dist/img/logo.png') }}" alt="AdminLTE Logo" style="height:30px"> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional)
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="adminlte/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div> -->

      <!-- SidebarSearch Form 
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>-->

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
            <li class="nav-item has-treeview {{ request()->is('usuario*') || request()->is('permiso*') || request()->is('rol*') ? 'menu-open' : '' }}"
                id="idCabSeguridad">
                <a href="#"
                    class="nav-link {{ request()->is('usuario*') || request()->is('permisos*') || request()->is('roles*') ? 'active' : '' }}"
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
                                class="nav-link {{ request()->is('admin.clients*') ? 'active' : '' }}"
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
            <li class="nav-item has-treeview {{ request()->is('usuario*') || request()->is('permiso*') || request()->is('rol*') ? 'menu-open' : '' }}"
                id="idCabSeguridad">
                <a href="#"
                    class="nav-link {{ request()->is('usuario*') || request()->is('permisos*') || request()->is('roles*') ? 'active' : '' }}"
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
                                class="nav-link {{ request()->is('permiso*') ? 'active' : '' }}"
                                id="idSegPermiso">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permiso</p>
                            </a>
                        </li>
                    @endcan
                    @can('seguridad.roles.index')
                        <li class="nav-item">
                            <a href="{{ route('role.index') }}"
                                class="nav-link  {{ request()->is('rol*') ? 'active' : '' }}"
                                id="idSegRoles">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                    @endcan
                    @can('seguridad.users.index')
                        <li class="nav-item">
                            <a href="{{ route('central-usuarios.index') }}"
                                class="nav-link {{ request()->is('central-usuarios*') ? 'active' : '' }}"
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