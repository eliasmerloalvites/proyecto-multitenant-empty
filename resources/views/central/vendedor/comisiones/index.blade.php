@extends('central.layout.appAdminLte')
@section('titulo', 'Mis Comisiones')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">MIS COMISIONES</h5>
                <p class="text-muted">
                    Lo que ganaste cada mes según tus clientes referidos que ya pagaron. El pago se coordina fuera del
                    sistema — "Liquidado" significa que la empresa ya te lo pagó.
                </p>

                <div class="table-responsive" style="background:#FFF;">
                    <table class="table table-striped nowrap" id="table-mis-comisiones">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Periodo</th>
                                <th>Clientes Pagaron</th>
                                <th>Total Comisión</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#table-mis-comisiones').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                ajax: "{{ route('vendedor.comisiones.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'periodo', name: 'periodo' },
                    { data: 'clientes_pagaron', name: 'clientes_pagaron' },
                    { data: 'total_comision', name: 'total_comision' },
                    { data: 'estado', name: 'estado' },
                ]
            });
        });
    </script>
@endsection
