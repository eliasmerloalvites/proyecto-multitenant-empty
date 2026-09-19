{{--
    JS compartido por todas las paginas de Reportes: formateo de dinero,
    atajos de rango de fechas, y el cableado de los filtros (Enter en las
    fechas, cambio de almacen). Cada pagina define su propia buscarReporte()
    (porque cada una pide datos distintos), pero esta la puede llamar sin
    problema aunque este definida en un <script> mas abajo: las funciones
    declaradas con "function nombre(){}" quedan disponibles globalmente en
    cuanto el navegador termina de parsear la pagina (aunque esten en un
    bloque de codigo mas abajo), antes de que el usuario alcance a hacer
    click en nada.
--}}
<script>
    function moneyFmt(v) {
        return 'S/ ' + parseFloat(v || 0).toFixed(2);
    }

    function atajoRango(tipo) {
        let hoy = new Date();
        let desde = new Date();

        if (tipo === 'hoy') {
            desde = new Date(hoy);
        } else if (tipo === 'semana') {
            let dia = hoy.getDay() === 0 ? 7 : hoy.getDay();
            desde.setDate(hoy.getDate() - (dia - 1));
        } else if (tipo === 'mes') {
            desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        } else if (tipo === 'anio') {
            desde = new Date(hoy.getFullYear(), 0, 1);
        }

        $('#filtroFechaDesde').val(desde.toISOString().slice(0, 10));
        $('#filtroFechaHasta').val(hoy.toISOString().slice(0, 10));
        buscarReporte();
    }

    function urlConFiltros(urlBase) {
        let params = new URLSearchParams({
            fecha_desde: $('#filtroFechaDesde').val() || '',
            fecha_hasta: $('#filtroFechaHasta').val() || '',
            almacen_id: $('#filtroAlmacen').length ? ($('#filtroAlmacen').val() || '') : ''
        });

        return urlBase + '?' + params.toString();
    }

    $(document).ready(function() {
        $('#filtroFechaDesde, #filtroFechaHasta').on('keyup', function(e) {
            if (e.key === 'Enter') {
                buscarReporte();
            }
        });

        $('#filtroAlmacen').on('change', function() {
            buscarReporte();
        });
    });
</script>
