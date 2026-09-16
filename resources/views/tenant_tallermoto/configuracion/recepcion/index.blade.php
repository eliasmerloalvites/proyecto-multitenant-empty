@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Estado de Recepción')
@section('contenido')

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-1">ESTADO DE RECEPCIÓN — CATEGORÍAS E ITEMS</h4>
                        <p class="text-muted mb-0">
                            Lo que se pinta en la ficha de recepción de cada mantenimiento sale de aquí. Agrega,
                            renombra o desactiva categorías/items sin tocar nada más — los cambios aplican al
                            instante en los formularios nuevos. Nada se borra de verdad (solo se desactiva), así que
                            las recepciones ya guardadas nunca se ven afectadas.
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnNuevaCategoria">
                        <i class="fa fa-plus"></i> Nueva categoría
                    </button>
                </div>

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-inventario">
                            Inventario Visual
                            <span class="badge badge-secondary">{{ $categorias->where('RCT_Grupo', 'INVENTARIO')->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-inspeccion">
                            Inspección de la Unidad
                            <span class="badge badge-secondary">{{ $categorias->where('RCT_Grupo', 'INSPECCION')->count() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    @foreach (['INVENTARIO' => 'tab-inventario', 'INSPECCION' => 'tab-inspeccion'] as $grupo => $tabId)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}">
                            @forelse ($categorias->where('RCT_Grupo', $grupo) as $categoria)
                                <div class="card mb-3 {{ !$categoria->RCT_Activo ? 'border-secondary' : '' }}">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $categoria->RCT_Nombre }}</strong>
                                            @unless ($categoria->RCT_Activo)
                                                <span class="badge badge-secondary ml-1">Inactiva</span>
                                            @endunless
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-success btn-sm btnNuevoItem"
                                                data-categoria-id="{{ $categoria->RCT_Id }}" data-categoria-nombre="{{ $categoria->RCT_Nombre }}">
                                                <i class="fa fa-plus"></i> Item
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm btnEditarCategoria"
                                                data-categoria="{{ $categoria->toJson() }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm btnToggleCategoria" data-id="{{ $categoria->RCT_Id }}">
                                                <i class="fa fa-{{ $categoria->RCT_Activo ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Etiqueta</th>
                                                    <th>Tipo de campo</th>
                                                    <th class="text-center">Activo</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($categoria->items as $item)
                                                    <tr class="{{ !$item->RIT_Activo ? 'text-muted' : '' }}">
                                                        <td>{{ $item->RIT_Etiqueta }}</td>
                                                        <td><span class="badge badge-light border">{{ $item->RIT_TipoCampo }}</span></td>
                                                        <td class="text-center">
                                                            @if ($item->RIT_Activo)
                                                                <span class="badge badge-success">Activo</span>
                                                            @else
                                                                <span class="badge badge-secondary">Inactivo</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-outline-primary btn-sm btnEditarItem"
                                                                data-item="{{ $item->toJson() }}">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm btnToggleItem" data-id="{{ $item->RIT_Id }}">
                                                                <i class="fa fa-{{ $item->RIT_Activo ? 'eye-slash' : 'eye' }}"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-2">Sin items todavía.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-3">Sin categorías todavía en este grupo.</p>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CATEGORIA --}}
    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCategoriaTitulo">Nueva categoría</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="categoria_id">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control" id="categoria_nombre" placeholder="Ej. Motor">
                    </div>
                    <div class="form-group">
                        <label>Grupo</label>
                        <select class="form-control" id="categoria_grupo">
                            <option value="INVENTARIO">Inventario Visual</option>
                            <option value="INSPECCION">Inspección de la Unidad</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Orden</label>
                        <input type="number" class="form-control" id="categoria_orden" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarCategoria">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ITEM --}}
    <div class="modal fade" id="modalItem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalItemTitulo">Nuevo item</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="item_id">
                    <input type="hidden" id="item_categoria_id">
                    <p class="text-muted small mb-2">Categoría: <strong id="item_categoria_nombre"></strong></p>
                    <div class="form-group">
                        <label>Etiqueta (lo que ve el usuario)</label>
                        <input type="text" class="form-control" id="item_etiqueta" placeholder="Ej. Nivel de aceite">
                    </div>
                    <div class="form-group">
                        <label>Código interno</label>
                        <input type="text" class="form-control" id="item_codigo" placeholder="Ej. motor_nivel_aceite">
                        <small class="form-text text-muted">Único, sin espacios. Se genera solo si lo dejas vacío.</small>
                    </div>
                    <div class="form-group">
                        <label>Tipo de campo</label>
                        <select class="form-control" id="item_tipo">
                            <option value="BOOLEAN">Sí / No</option>
                            <option value="STATUS">Estado (Bueno / Regular / Malo)</option>
                            <option value="INSPECTION">Inspección (OK / Reemplazar)</option>
                            <option value="SELECT">Selección de opciones</option>
                            <option value="TEXT">Texto libre</option>
                        </select>
                    </div>
                    <div class="form-group" id="item_opciones_wrap" style="display:none;">
                        <label>Opciones (una por línea)</label>
                        <textarea class="form-control" id="item_opciones" rows="3" placeholder="1/4&#10;1/2&#10;3/4&#10;Full"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label>Orden</label>
                        <input type="number" class="form-control" id="item_orden" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarItem">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        function slugify(texto) {
            return texto.toString().toLowerCase().trim()
                .normalize('NFD').replace(/[̀-ͯ]/g, '')
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '');
        }

        $('#btnNuevaCategoria').on('click', function () {
            $('#modalCategoriaTitulo').text('Nueva categoría');
            $('#categoria_id').val('');
            $('#categoria_nombre').val('');
            $('#categoria_grupo').val('INVENTARIO');
            $('#categoria_orden').val(0);
            $('#modalCategoria').modal('show');
        });

        $('body').on('click', '.btnEditarCategoria', function () {
            let c = $(this).data('categoria');
            $('#modalCategoriaTitulo').text('Editar categoría');
            $('#categoria_id').val(c.RCT_Id);
            $('#categoria_nombre').val(c.RCT_Nombre);
            $('#categoria_grupo').val(c.RCT_Grupo);
            $('#categoria_orden').val(c.RCT_Orden);
            $('#modalCategoria').modal('show');
        });

        $('#btnGuardarCategoria').on('click', function () {
            let id = $('#categoria_id').val();
            let data = {
                RCT_Nombre: $('#categoria_nombre').val(),
                RCT_Grupo: $('#categoria_grupo').val(),
                RCT_Orden: $('#categoria_orden').val() || 0,
                _token: '{{ csrf_token() }}'
            };

            if (!data.RCT_Nombre.trim()) {
                Swal.fire({ icon: 'warning', title: 'Falta el nombre' });
                return;
            }

            let url = id
                ? '{{ url("tenant/configuracion/recepcion/categoria") }}/' + id
                : '{{ url("tenant/configuracion/recepcion/categoria") }}';
            if (id) data._method = 'PUT';

            $.ajax({ url: url, method: 'POST', data: data })
                .done(function () { location.reload(); })
                .fail(function (xhr) {
                    Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Revisa los datos.' });
                });
        });

        $('body').on('click', '.btnToggleCategoria', function () {
            let id = $(this).data('id');
            $.ajax({
                url: '{{ url("tenant/configuracion/recepcion/categoria") }}/' + id + '/activar',
                method: 'PUT',
                data: { _token: '{{ csrf_token() }}' }
            }).done(function () { location.reload(); });
        });

        $('body').on('click', '.btnNuevoItem', function () {
            $('#modalItemTitulo').text('Nuevo item');
            $('#item_id').val('');
            $('#item_categoria_id').val($(this).data('categoria-id'));
            $('#item_categoria_nombre').text($(this).data('categoria-nombre'));
            $('#item_etiqueta').val('');
            $('#item_codigo').val('');
            $('#item_tipo').val('BOOLEAN').trigger('change');
            $('#item_opciones').val('');
            $('#item_orden').val(0);
            $('#modalItem').modal('show');
        });

        $('body').on('click', '.btnEditarItem', function () {
            let it = $(this).data('item');
            $('#modalItemTitulo').text('Editar item');
            $('#item_id').val(it.RIT_Id);
            $('#item_categoria_id').val(it.RCT_Id);
            $('#item_categoria_nombre').text('');
            $('#item_etiqueta').val(it.RIT_Etiqueta);
            $('#item_codigo').val(it.RIT_Codigo);
            $('#item_tipo').val(it.RIT_TipoCampo).trigger('change');
            $('#item_opciones').val((it.RIT_Opciones || []).join('\n'));
            $('#item_orden').val(it.RIT_Orden);
            $('#modalItem').modal('show');
        });

        $('#item_tipo').on('change', function () {
            $('#item_opciones_wrap').toggle($(this).val() === 'SELECT');
        });

        $('#btnGuardarItem').on('click', function () {
            let id = $('#item_id').val();
            let codigo = $('#item_codigo').val().trim() || slugify($('#item_etiqueta').val());
            let opciones = $('#item_opciones').val().split('\n').map(s => s.trim()).filter(Boolean);

            let data = {
                RCT_Id: $('#item_categoria_id').val(),
                RIT_Etiqueta: $('#item_etiqueta').val(),
                RIT_Codigo: codigo,
                RIT_TipoCampo: $('#item_tipo').val(),
                RIT_Orden: $('#item_orden').val() || 0,
                _token: '{{ csrf_token() }}'
            };
            if ($('#item_tipo').val() === 'SELECT') {
                data['RIT_Opciones'] = opciones;
            }

            if (!data.RIT_Etiqueta.trim()) {
                Swal.fire({ icon: 'warning', title: 'Falta la etiqueta' });
                return;
            }
            if ($('#item_tipo').val() === 'SELECT' && !opciones.length) {
                Swal.fire({ icon: 'warning', title: 'Agrega al menos una opción' });
                return;
            }

            let url = id
                ? '{{ url("tenant/configuracion/recepcion/item") }}/' + id
                : '{{ url("tenant/configuracion/recepcion/item") }}';
            if (id) data._method = 'PUT';

            $.ajax({ url: url, method: 'POST', data: data })
                .done(function () { location.reload(); })
                .fail(function (xhr) {
                    Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Revisa los datos.' });
                });
        });

        $('body').on('click', '.btnToggleItem', function () {
            let id = $(this).data('id');
            $.ajax({
                url: '{{ url("tenant/configuracion/recepcion/item") }}/' + id + '/activar',
                method: 'PUT',
                data: { _token: '{{ csrf_token() }}' }
            }).done(function () { location.reload(); });
        });
    </script>
@endsection
