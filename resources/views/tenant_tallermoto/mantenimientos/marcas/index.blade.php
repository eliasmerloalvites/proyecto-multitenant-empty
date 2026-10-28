@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Marcas de Vehículo')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3" style="gap: 12px;">
                    <div>
                        <h4 class="mb-1">MARCAS DE VEHÍCULO</h4>
                        <p class="text-muted mb-0">
                            Las marcas que se pueden elegir al reservar y en cada mantenimiento (siempre es opcional).
                            Desactiva las que no uses para que no aparezcan en la lista.
                        </p>
                    </div>
                    <form id="formNuevaMarca" class="d-flex" style="gap: 8px;" autocomplete="off">
                        <input type="text" class="form-control" id="nuevaMarca" maxlength="60" placeholder="Nueva marca, ej. Hero" aria-label="Nombre de la nueva marca" required>
                        <button type="submit" class="btn btn-primary text-nowrap"><i class="fa fa-plus"></i> Agregar</button>
                    </form>
                </div>

                <div class="row mb-2">
                    <div class="col-md-4">
                        <input type="search" class="form-control form-control-sm" id="buscarMarca" placeholder="Buscar marca..." aria-label="Buscar marca">
                    </div>
                    <div class="col-md-8 text-md-right text-muted small pt-1">
                        {{ $marcas->where('MAR_Estado', 1)->count() }} activas · {{ $marcas->where('MAR_Estado', 0)->count() }} desactivadas
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover" id="tablaMarcas">
                        <thead class="bg-light">
                            <tr>
                                <th>Marca</th>
                                <th class="text-center" style="width: 120px;">Reservas</th>
                                <th class="text-center" style="width: 130px;">Estado</th>
                                <th class="text-center" style="width: 150px;">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($marcas as $m)
                                <tr data-id="{{ $m->MAR_Id }}" data-nombre="{{ $m->MAR_Nombre }}" data-estado="{{ (int) $m->MAR_Estado }}">
                                    <td class="nombre">{{ $m->MAR_Nombre }}</td>
                                    <td class="text-center">{{ $m->reservas }}</td>
                                    <td class="text-center">
                                        @if ($m->MAR_Estado)
                                            <span class="badge badge-success">Activa</span>
                                        @else
                                            <span class="badge badge-secondary">Desactivada</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary editarMarca" title="Renombrar"><i class="fa fa-pen"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-{{ $m->MAR_Estado ? 'secondary' : 'success' }} alternarMarca"
                                            title="{{ $m->MAR_Estado ? 'Desactivar' : 'Activar' }}">
                                            <i class="fa {{ $m->MAR_Estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger eliminarMarca" title="Eliminar"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Aún no hay marcas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            const URL_BASE = @json(tenant_url('tenant.mantenimientos.marcas.index'));
            const TOKEN = '{{ csrf_token() }}';
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            const error = (x) => {
                const j = x.responseJSON || {};
                const msg = (j.errors && Object.values(j.errors)[0][0]) || j.message || 'No se pudo guardar.';
                Toast.fire({ type: 'error', title: msg });
            };
            const recargar = (r) => { Toast.fire({ type: 'success', title: r.message }); setTimeout(() => location.reload(), 600); };

            $('#buscarMarca').on('input', function () {
                const q = $(this).val().toLowerCase();
                $('#tablaMarcas tbody tr[data-id]').each(function () {
                    $(this).toggle($(this).data('nombre').toString().toLowerCase().includes(q));
                });
            });

            $('#formNuevaMarca').on('submit', function (e) {
                e.preventDefault();
                $.post(URL_BASE, { _token: TOKEN, MAR_Nombre: $('#nuevaMarca').val().trim() }).done(recargar).fail(error);
            });

            function actualizar(fila, nombre, estado) {
                $.ajax({ url: URL_BASE + '/' + fila.data('id'), type: 'PUT', data: { _token: TOKEN, MAR_Nombre: nombre, MAR_Estado: estado } })
                    .done(recargar).fail(error);
            }

            $('body').on('click', '.editarMarca', function () {
                const fila = $(this).closest('tr');
                Swal.fire({
                    title: 'Renombrar marca', input: 'text', inputValue: fila.data('nombre'),
                    showCancelButton: true, confirmButtonText: 'Guardar', cancelButtonText: 'Cancelar',
                    inputValidator: (v) => (!v || !v.trim()) ? 'Escribe el nombre.' : undefined
                }).then((r) => { if (r.value) actualizar(fila, r.value.trim(), fila.data('estado')); });
            });

            $('body').on('click', '.alternarMarca', function () {
                const fila = $(this).closest('tr');
                actualizar(fila, fila.data('nombre'), fila.data('estado') ? 0 : 1);
            });

            $('body').on('click', '.eliminarMarca', function () {
                const fila = $(this).closest('tr');
                Swal.fire({
                    title: '¿Eliminar ' + $('<div>').text(fila.data('nombre')).html() + '?',
                    text: 'Si ya se usó en reservas o mantenimientos, solo se desactivará.',
                    type: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                    confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar'
                }).then((r) => {
                    if (!r.value) return;
                    $.ajax({ url: URL_BASE + '/' + fila.data('id'), type: 'DELETE', data: { _token: TOKEN } }).done(recargar).fail(error);
                });
            });
        });
    </script>
@endsection
