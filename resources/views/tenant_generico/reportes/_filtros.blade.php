{{--
    Partial de filtros compartido por todas las paginas de Reportes
    (Resumen General, Utilidad por Producto, Productos y Clientes,
    Utilidad por Categoria, Detalle de Ventas). Cada pagina define su
    propia funcion JS buscarReporte() que este partial invoca; asi el
    HTML/estilo de filtros no se repite 5 veces pero cada pagina sigue
    pidiendo los datos que le corresponden.

    Variables esperadas: $fechaDesde, $fechaHasta, $almacenes, $almacenId.
--}}
<div class="rpt-filtros">
    <div class="row align-items-end">
        <div class="col-md-3 col-6 mb-2">
            <label>Desde</label>
            <input type="date" class="form-control" id="filtroFechaDesde" value="{{ $fechaDesde }}">
        </div>
        <div class="col-md-3 col-6 mb-2">
            <label>Hasta</label>
            <input type="date" class="form-control" id="filtroFechaHasta" value="{{ $fechaHasta }}">
        </div>
        @if ($almacenes->count() > 1)
            <div class="col-md-3 col-8 mb-2">
                <label>Almacén</label>
                <select class="form-control" id="filtroAlmacen">
                    <option value="">Todos</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->ALM_Id }}" {{ (string) $almacenId === (string) $almacen->ALM_Id ? 'selected' : '' }}>
                            {{ $almacen->ALM_NombreAlmacen }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-2 col-4 mb-2">
            <button type="button" class="btn btn-block" style="background:#6C3BFF;color:#fff;font-weight:700;" onclick="buscarReporte()">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('hoy')">Hoy</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('semana')">Esta semana</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('mes')">Este mes</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="atajoRango('anio')">Este año</button>
        </div>
    </div>
</div>
