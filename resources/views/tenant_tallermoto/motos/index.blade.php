@extends('tenant_tallermoto.layout.appAdminLte')
@section('titulo', 'Motos Atendidas')
@section('contenido')

<div class="col-12">
    <div class="card" style="border:none; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); background: var(--bg-card,#fff);">
        <div class="card-body">
            <div class="d-flex align-items-center mb-1">
                <i class="fas fa-motorcycle mr-2" style="color:#E52320;"></i>
                <h5 class="card-title mb-0" style="color:var(--text-main,#1E293B);">MOTOS ATENDIDAS</h5>
            </div>
            <p class="text-muted small mb-3">Una fila por placa — la moto es la que tiene historial, no el cliente. Cada placa puede haber tenido más de un dueño.</p>

            <div class="table-responsive">
                <table class="table" id="tabla_motos">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Placa</th>
                            <th>Propietario actual</th>
                            <th>Modelo</th>
                            <th>Última visita</th>
                            <th>Total visitas</th>
                            <th>Estado último mant.</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('#tabla_motos').DataTable({
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            searchDelay: 800,
            order: [[4, 'desc']],
            dom: 'Blfrtip',
            buttons: ['copyHtml5', 'excelHtml5', 'pdfHtml5'],
            ajax: "{{ tenant_url('tenant.motos.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'placa', name: 'placa' },
                { data: 'propietario', name: 'propietario' },
                { data: 'unidad', name: 'unidad' },
                { data: 'fecha', name: 'fecha' },
                { data: 'visitas', name: 'visitas' },
                { data: 'estado', name: 'estado' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endsection
