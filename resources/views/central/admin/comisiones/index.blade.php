@extends('central.layout.appAdminLte')
@section('titulo', 'Comisiones')
@section('contenido')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">REPORTE DE COMISIONES</h5>
                <p class="text-muted">
                    Comisión generada por cada vendedor, mes a mes, según qué clientes referidos ya pagaron ese
                    período. El pago al vendedor se hace fuera del sistema — usa "Marcar liquidado" para llevar el
                    control de qué meses ya facturaste.
                </p>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Vendedor</label>
                        <select id="filtro_vendedor" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($vendedores as $vendedor)
                                <option value="{{ $vendedor->id }}">{{ $vendedor->user->name ?? 'Vendedor #' . $vendedor->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Periodo (YYYY-MM)</label>
                        <input type="month" id="filtro_periodo" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="btnFiltrar" class="btn btn-primary w-100">
                            <i class="fas fa-filter mr-1"></i>Filtrar
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="background:#FFF;">
                    <table class="table table-striped nowrap" id="table-comisiones">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Vendedor</th>
                                <th>Periodo</th>
                                <th>Clientes Pagaron</th>
                                <th>Total Comisión</th>
                                <th>Estado</th>
                                <th>Acción</th>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            var table = $('#table-comisiones').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.comisiones.index') }}",
                    data: function(d) {
                        d.vendedor_id = $('#filtro_vendedor').val();
                        d.periodo = $('#filtro_periodo').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'vendedor_nombre', name: 'vendedor_nombre' },
                    { data: 'periodo', name: 'periodo' },
                    { data: 'clientes_pagaron', name: 'clientes_pagaron' },
                    { data: 'total_comision', name: 'total_comision' },
                    { data: 'estado', name: 'estado' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#btnFiltrar').on('click', function() {
                table.draw();
            });

            $('body').on('click', '.liquidarComision', function() {
                var vendedorId = $(this).data('vendedor');
                var periodo = $(this).data('periodo');

                Swal.fire({
                    title: '¿Marcar como liquidado?',
                    text: 'Confirma que ya facturaste/pagaste a este vendedor el periodo ' + periodo + '.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Sí, marcar liquidado',
                    cancelButtonText: 'Cancelar'
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('admin.comisiones.liquidar') }}",
                        type: 'POST',
                        data: { vendedor_id: vendedorId, periodo: periodo },
                        success: function(data) {
                            Toast.fire({ icon: 'success', title: data.success });
                            table.draw();
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo liquidar.';
                            Toast.fire({ icon: 'error', title: msg });
                        }
                    });
                });
            });
        });
    </script>
@endsection
