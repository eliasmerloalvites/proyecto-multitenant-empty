@extends('central.layout.appAdminLte')
@section('titulo', 'Clientes')
@section('contenido')
    @can('admin.clients.create')
        <div class="col-5">
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white border-0 pb-0">
                    <h4 class="mb-1 font-weight-bold text-primary">
                        CREAR CLIENTE
                    </h4>
                    <small class="text-muted">
                        Registra un nuevo cliente en la plataforma SaaS
                    </small>
                </div>

                <div class="card-body">

                    <form id="ClienteForm" name="ClienteForm" action="{{ route('admin.clients.store') }}">
                        @csrf

                        {{-- ===================================================== --}}
                        {{-- INFORMACION CLIENTE --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-user-circle mr-1"></i>
                                Información del Cliente
                            </h6>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>RUC</label>
                                    <input type="text" name="ruc" id="ruc" class="form-control"
                                        placeholder="Ingrese RUC">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Razón Social</label>
                                    <input type="text" name="razon_social" id="razon_social" class="form-control"
                                        placeholder="Ingrese razón social" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label>Tipo de Negocio</label>

                                    <select class="form-control select2" id="tipo_negocio" name="tipo_negocio">
                                        <option value="generico">Genérico</option>
                                        <option value="tallermoto">Taller de Motos</option>
                                        <option value="optica">Óptica</option>
                                        <option value="ferreteria">Ferretería</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="hotel">Hotel</option>
                                    </select>
                                </div>

                            </div>
                        </div>


                        {{-- ===================================================== --}}
                        {{-- PLAN Y FACTURACION --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">

                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-file-invoice-dollar mr-1"></i>
                                Plan y Facturación
                            </h6>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>Plan SaaS</label>

                                    <select class="form-control" name="plan" id="plan">
                                        <option value="start">START</option>
                                        <option value="basic">BASIC</option>
                                        <option value="plus">PLUS</option>
                                        <option value="empresarial">EMPRESARIAL</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Día de Facturación</label>

                                    <input type="number" min="1" max="28" name="billing_day" class="form-control"
                                        placeholder="Día del mes">
                                </div>

                            </div>
                        </div>


                        {{-- ===================================================== --}}
                        {{-- DOMINIO Y ACCESO --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">

                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-globe mr-1"></i>
                                Dominio y Acceso
                            </h6>

                            <div class="alert alert-light border small">
                                El subdominio se utiliza para crear la base de datos y el identificador interno del tenant.
                                <br>
                                El dominio personalizado es opcional y solo aplica para planes premium.
                            </div>


                            {{-- TIPO DOMINIO --}}

                            <div class="mb-3">

                                <label class="d-block mb-2">
                                    Tipo de Dominio
                                </label>

                                <div class="custom-control custom-radio custom-control-inline">

                                    <input type="radio" id="use_subdomain" name="domain_type" class="custom-control-input"
                                        value="subdomain" checked>

                                    <label class="custom-control-label" for="use_subdomain">
                                        Subdominio KaelTech
                                    </label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline">

                                    <input type="radio" id="use_custom_domain" name="domain_type" class="custom-control-input"
                                        value="custom_domain">

                                    <label class="custom-control-label" for="use_custom_domain">
                                        Dominio Personalizado
                                    </label>
                                </div>
                            </div>


                            {{-- SUBDOMAIN --}}

                            <div class="mb-3" id="subdomainContainer">

                                <label>Subdominio</label>

                                <div class="input-group">

                                    <input type="text" class="form-control" name="subdomain" id="subdomain"
                                        placeholder="ejemplo">

                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            .{{ config('app.central_domain') }}
                                        </span>
                                    </div>
                                </div>

                                <small class="text-muted">
                                    Solo letras y números.
                                </small>
                            </div>


                            {{-- CUSTOM DOMAIN --}}

                            <div class="mb-3 d-none" id="customDomainContainer">

                                <label>Dominio Personalizado</label>

                                <input type="text" class="form-control" name="custom_domain" id="custom_domain"
                                    placeholder="midominio.com">

                                <small class="text-muted">
                                    Debe apuntar correctamente a tu servidor.
                                </small>
                            </div>

                        </div>


                        {{-- ===================================================== --}}
                        {{-- ACCESO SISTEMA --}}
                        {{-- ===================================================== --}}

                        <div class="mb-4">

                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-user-lock mr-1"></i>
                                Acceso al Sistema
                            </h6>

                            <div class="row">

                                <div class="col-md-12 mb-3">
                                    <label>Email</label>

                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="correo@empresa.com" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label>Contraseña</label>

                                    <div class="input-group">

                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Ingrese contraseña" required>

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        {{-- BOTONES --}}

                        <div class="d-flex align-items-center">

                            <button id="saveBtn" class="btn btn-primary mr-2">
                                <i class="fas fa-save mr-1"></i>
                                Guardar Cliente
                            </button>

                            <button type="reset" class="btn btn-danger">
                                <i class="fas fa-ban mr-1"></i>
                                Cancelar
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endcan
    @can('admin.clients.index')
        <div class="col-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">LISTA DE CLIENTES</h5>
                    <p class="card-text">

                    <div class="table-responsive" style="background:#FFF;">
                        <table class="table table-striped nowrap" id="table-users" name="table-users">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Ruc</th>
                                    <th scope="col">Razon Social</th>
                                    <th scope="col">Tipo de Negocio</th>
                                    <th scope="col">Dominio</th>
                                    <th scope="col">Día de Facturación</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan

@endsection

@section('script')
    <script>
        $('input[name="domain_type"]').on('change', function() {

            const value = $(this).val();

            if (value === 'subdomain') {

                $('#subdomainContainer').removeClass('d-none');
                $('#customDomainContainer').addClass('d-none');

            } else {

                $('#subdomainContainer').addClass('d-none');
                $('#customDomainContainer').removeClass('d-none');
            }
        });

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
            var table = $('#table-users').DataTable({
                responsive: true, // Habilitar la opción responsive
                autoWidth: false,
                searchDelay: 2000,
                processing: true,
                serverSide: true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ ",
                    "zeroRecords": "Nada encontrado - disculpa",
                    "info": "Mostrando la página _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },

                order: [
                    [0, "asc"]
                ],
                ajax: "{{ route('admin.clients.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'ruc',
                        name: 'ruc'
                    },
                    {
                        data: 'razon_social',
                        name: 'razon_social'
                    },
                    {
                        data: 'tipo_negocio',
                        name: 'tipo_negocio'
                    },
                    {
                        data: 'domain',
                        name: 'domain'
                    },
                    {
                        data: 'billing_day',
                        name: 'billing_day'
                    },
                    {
                        data: null,
                        name: 'name',
                        'render': function(data, type, row) {
                            return
                                @can('admin.clients.show')
                                    data.action3 + ' ' +
                                @endcan
                            ''
                            @can('admin.clients.edit')
                                +data.action1 + ' ' +
                            @endcan
                            ''
                            @can('admin.clients.destroy')
                                +data.action2
                            @endcan ;
                        }
                    }
                ]
            });


            $('#saveBtn').click(function(e) {
                e.preventDefault();
                name = $("#name").val();
                email = $("#email").val();
                contra = $("#password").val();
                confirmacontra = $("#confipassword").val();
                if (name == '' || email == '' || contra == '' || confirmacontra == '') {
                    Toast.fire({
                        type: 'error',
                        title: 'Complete todos los campos por favor'
                    })
                    return false;
                }
                $.ajax({
                    data: $('#ClienteForm').serialize(),
                    url: "{{ route('admin.clients.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                        $('#ClienteForm').trigger("reset");
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Toast.fire({
                            type: 'error',
                            title: 'Cliente fallo al Registrarse.'
                        })
                    }
                });
            });


            $('body').on('click', '.deleteClient', function() {

                var Cliente_id_delete = $(this).data("id");
                $confirm = confirm("¿Estás seguro de que quieres eliminarlo?");
                if ($confirm == true) {
                    $.ajax({
                        type: "DELETE",

                        url: '{{ route('admin.clients.destroy', ['client' => ':client']) }}'
                            .replace(':client', Cliente_id_delete),
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.draw();
                            Toast.fire({
                                type: 'success',
                                title: String(data.success)
                            });

                        },
                        error: function(data) {
                            console.log('Error:', data);
                            Toast.fire({
                                type: 'error',
                                title: 'Usuario fallo al Eliminarlo.'
                            })
                        }
                    });
                }
            });



        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@endsection
