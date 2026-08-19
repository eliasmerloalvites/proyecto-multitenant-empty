<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="/tenant/home" class="nav-link">Menu</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Navbar Search
    <li class="nav-item">
      <a class="nav-link" data-widget="navbar-search" href="#" role="button">
        <i class="fas fa-search"></i>
      </a>
      <div class="navbar-search-block">
        <form class="form-inline">
          <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
              </button>
              <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </li> -->

    <!-- Messages Dropdown Menu 
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-comments"></i>
        <span class="badge badge-danger navbar-badge">3</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <a href="#" class="dropdown-item">
          <div class="media">
            <img src="adminlte/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
            <div class="media-body">
              <h3 class="dropdown-item-title">
                Brad Diesel
                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
              </h3>
              <p class="text-sm">Call me whenever you can...</p>
              <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
            </div>
          </div>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <div class="media">
            <img src="adminlte/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
            <div class="media-body">
              <h3 class="dropdown-item-title">
                John Pierce
                <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
              </h3>
              <p class="text-sm">I got your message bro</p>
              <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
            </div>
          </div>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <div class="media">
            <img src="adminlte/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
            <div class="media-body">
              <h3 class="dropdown-item-title">
                Nora Silvester
                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
              </h3>
              <p class="text-sm">The subject goes here</p>
              <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
            </div>
          </div>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
      </div>
    </li>-->
    <!-- Notifications Dropdown Menu 
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge">15</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">15 Notifications</span>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-envelope mr-2"></i> 4 new messages
          <span class="float-right text-muted text-sm">3 mins</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-users mr-2"></i> 8 friend requests
          <span class="float-right text-muted text-sm">12 hours</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-file mr-2"></i> 3 new reports
          <span class="float-right text-muted text-sm">2 days</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
      </div>
    </li>-->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    {{-- <li class="nav-item">
      <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
        <i class="fas fa-th-large"></i>
      </a>
    </li> --}}
    @if (($cajasDisponibles ?? collect())->isNotEmpty())
      <li class="nav-item dropdown">
        <a id="cajaDropdown" class="nav-link dropdown-toggle" href="#" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cash-register mr-1"></i>
            <span id="cajaActivaLabel">{{ $cajaActiva->CAJ_Nombre ?? 'Sin caja abierta' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="cajaDropdown">
            @if ($cajaActiva)
                <a href="#" class="dropdown-item cerrarCajaNavbar" data-id="{{ $cajaActiva->CAJ_Id }}" data-nombre="{{ $cajaActiva->CAJ_Nombre }}">
                    <i class="fas fa-lock mr-2 text-danger"></i>Cerrar "{{ $cajaActiva->CAJ_Nombre }}"
                </a>
                <div class="dropdown-divider"></div>
            @endif
            @php $cajasAbiertas = $cajasDisponibles->filter(fn($cj) => $cj->sesionAbierta); @endphp
            @if ($cajasAbiertas->count() > 1)
                <span class="dropdown-item dropdown-header">Cambiar a otra caja abierta</span>
                @foreach ($cajasAbiertas as $cj)
                    <a href="#" class="dropdown-item seleccionarCaja {{ ($cajaActiva && $cajaActiva->CAJ_Id === $cj->CAJ_Id) ? 'active' : '' }}" data-caja-id="{{ $cj->CAJ_Id }}">
                        <i class="fas fa-cash-register mr-2"></i>{{ $cj->CAJ_Nombre }}
                    </a>
                @endforeach
                <div class="dropdown-divider"></div>
            @endif
            @php $cajasCerradas = $cajasDisponibles->filter(fn($cj) => ! $cj->sesionAbierta); @endphp
            @if ($cajasCerradas->isNotEmpty())
                <span class="dropdown-item dropdown-header">Aperturar caja</span>
                @foreach ($cajasCerradas as $cj)
                    <a href="#" class="dropdown-item aperturarCajaNavbar" data-id="{{ $cj->CAJ_Id }}" data-nombre="{{ $cj->CAJ_Nombre }}" data-monto="{{ $cj->CAJ_MontoApertura }}">
                        <i class="fas fa-unlock mr-2 text-success"></i>{{ $cj->CAJ_Nombre }}
                    </a>
                @endforeach
            @endif
        </div>
      </li>
    @endif
    <li class="nav-item dropdown ">
      <a id="navbarDropdown" onclick="CerrarSession()" class="nav-link dropdown-toggle" href="#"
          role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
          <b> {{ Auth::user()->name }} </b> <span class="caret"></span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" id="idSession" style=" display: none;  "
          aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{ tenant_url('tenant.logout') }}"
              onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
              {{ __('Cerrar Session') }}
          </a>
          <form id="logout-form" action="{{ tenant_url('tenant.logout') }}" method="POST">
              @csrf
          </form>
      </div>
  </li>
  </ul>
</nav>