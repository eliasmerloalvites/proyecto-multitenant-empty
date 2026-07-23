@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')

@section('titulo', 'Configuración Empresa')


<style>
    .title-main {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 25px;
    }

    .title-main i {
        color: #2563eb;
        margin-right: 10px;
    }

    /* =========================================
       CARD
    ========================================= */

    .config-card {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(15, 23, 42, .06);
        margin-bottom: 24px;
    }

    .config-card .card-header {
        border: none;
        padding: 16px 22px;
    }

    .config-card .card-header h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .4px;
    }

    .config-card .card-body {
        padding: 24px;
    }

    /* =========================================
       HEADER COLORS
    ========================================= */

    .card-header-blue {
        background: linear-gradient(135deg, #1877f2, #0047b3);
        color: white;
    }

    .card-header-cyan {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }

    .card-header-dark {
        background: linear-gradient(135deg, #334155, #0f172a);
        color: white;
    }

    .card-header-gray {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
    }

    /* =========================================
       FORM
    ========================================= */

    .form-group {
        margin-bottom: 18px;
    }

    label {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .form-control {
        height: 46px;
        border-radius: 12px;
        border: 1px solid #dbe2ea;
        font-size: 14px;
        padding: 10px 14px;
        transition: .2s;
    }

    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
    }

    select.form-control {
        height: 46px !important;
    }

    /* =========================================
       BUTTON SEARCH
    ========================================= */

    .btn-search-ruc {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: white;
        transition: .2s;
    }

    .btn-search-ruc:hover {
        transform: scale(1.03);
    }

    /* =========================================
       LOGOS
    ========================================= */

    .logo-upload-box {
        text-align: center;
    }

    .logo-preview {
        width: 190px;
        height: 190px;
        object-fit: contain;
        border-radius: 18px;
        border: 2px dashed #dbe2ea;
        background: #f8fafc;
        padding: 14px;
        margin-bottom: 18px;
    }

    .custom-file-label {
        border-radius: 12px;
        height: 46px;
        padding-top: 11px;
    }

    .custom-file-input {
        height: 46px;
    }

    /* =========================================
       SAVE BUTTON
    ========================================= */

    .save-btn {
        height: 50px;
        padding: 0 35px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        font-size: 15px;
        font-weight: 700;
        box-shadow: 0 5px 15px rgba(37, 99, 235, .25);
    }

    .save-btn i {
        margin-right: 8px;
    }
</style>


@section('contenido')

    <div class="container-fluid">
        <!-- =========================================
             FORM
        ========================================= -->

        <form method="POST" id="empresa_form" enctype="multipart/form-data">

            @csrf

            <div class="row">

                <!-- =========================================
                     DATOS EMPRESA
                ========================================= -->
                <div class="col-12 col-lg-6">
                    <div class="card config-card">
                        <div class="card-header card-header-blue">
                            <h6>
                                <i class="fas fa-building mr-2"></i>
                                DATOS EMPRESA
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>RUC</label>
                                <input type="number" class="form-control" value="{{ $empresa->ruc ?? '' }}" id="ruc"
                                    name="ruc" maxlength="11">
                            </div>

                            <div class="form-group">
                                <label>Razón Social</label>
                                <input type="text" class="form-control" value="{{ $empresa->razon_social ?? '' }}"
                                    id="razon_social" name="razon_social">
                            </div>

                            <div class="form-group">
                                <label>Nombre Comercial</label>
                                <input type="text" class="form-control" value="{{ $empresa->nombre_comercial ?? '' }}"
                                    id="nombre_comercial" name="nombre_comercial">
                            </div>

                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" class="form-control" value="{{ $empresa->direccion ?? '' }}"
                                    id="direccion" name="direccion">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Departamento</label>
                                        <input type="text" class="form-control"
                                            value="{{ $empresa->departamento ?? '' }}" id="departamento"
                                            name="departamento">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Provincia</label>
                                        <input type="text" class="form-control" value="{{ $empresa->provincia ?? '' }}"
                                            id="provincia" name="provincia">
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Distrito</label>
                                        <input type="text" class="form-control" value="{{ $empresa->distrito ?? '' }}"
                                            id="distrito" name="distrito">
                                    </div>
                                </div>
                            </div>
                            <!-- CONTACTO -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" value="{{ $empresa->telefono ?? '' }}"
                                            id="telefono" name="telefono">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>WhatsApp</label>
                                        <input type="text" class="form-control" value="{{ $empresa->whatsapp ?? '' }}"
                                            id="whatsapp" name="whatsapp">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card config-card">
                        <div class="card-header card-header-cyan">
                            <h6>
                                <i class="fas fa-file-invoice mr-2"></i>
                                FACTURACIÓN ELECTRÓNICA
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- AMBIENTE -->
                            <div class="form-group">
                                <label>Ambiente</label>
                                <select class="form-control" value="{{ $empresa->ambiente ?? '' }}" id="ambiente"
                                    name="ambiente">
                                    <option value="beta">BETA</option>
                                    <option value="produccion">PRODUCCIÓN</option>
                                </select>
                            </div>
                            <!-- SOL -->
                            <div class="form-group">
                                <label>Usuario SOL</label>
                                <input type="text" class="form-control" value="{{ $empresa->sol_usuario ?? '' }}"
                                    id="sol_usuario" name="sol_usuario">
                            </div>
                            <div class="form-group">
                                <label>Password SOL</label>
                                <input type="password" class="form-control" value="{{ $empresa->sol_password ?? '' }}"
                                    id="sol_password" name="sol_password">
                            </div>
                            <!-- SERIES -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Serie Factura</label>
                                        <input type="text" class="form-control"
                                            value="{{ $empresa->serie_factura ?? '' }}" id="serie_factura"
                                            name="serie_factura">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Serie Boleta</label>
                                        <input type="text" class="form-control"
                                            value="{{ $empresa->serie_boleta ?? '' }}" id="serie_boleta"
                                            name="serie_boleta">
                                    </div>
                                </div>
                            </div>
                            <!-- CERTIFICADO -->
                            <div class="form-group">
                                <label>Certificado Digital</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="certificado_ruta"
                                        name="certificado_ruta">
                                    <label class="custom-file-label">
                                        Seleccionar archivo
                                    </label>
                                </div>
                            </div>
                            <!-- PASS CERT -->
                            <div class="form-group">
                                <label>Password Certificado</label>
                                <input type="password" class="form-control"
                                    value="{{ $empresa->certificado_password ?? '' }}" id="certificado_password"
                                    name="certificado_password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LOGO EMPRESA -->
                <div class="col-12 col-md-6">
                    <div class="card config-card">
                        <div class="card-header card-header-gray">
                            <h6>
                                <i class="fas fa-image mr-2"></i>
                                LOGO EMPRESA
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="logo-upload-box">
                                <img id="preview_logo" src="{{ asset_root('images/imagen_default.png') }}"
                                    class="logo-preview">
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="logo" name="logo">
                                <label class="custom-file-label">Seleccionar logo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOGO PDF -->
                <div class="col-12 col-md-6">
                    <div class="card config-card">
                        <div class="card-header card-header-dark">
                            <h6>
                                <i class="fas fa-file-pdf mr-2"></i>
                                LOGO PDF
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="logo-upload-box">
                                <img id="preview_logo_pdf" src="{{ asset_root('images/imagen_default.png') }}"
                                    class="logo-preview">
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="logo_pdf" name="logo_pdf">
                                <label class="custom-file-label">Seleccionar logo PDF</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card config-card">
                        <div class="card-header card-header-purple">
                            <h6>
                                <i class="fas fa-palette mr-2"></i>
                                IDENTIDAD VISUAL (PÁGINA WEB)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="logo-upload-box">
                                    <img id="preview_logo_portada1" src="{{ asset_root('images/imagen_default.png') }}"
                                        class="logo-preview">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo_portada1" name="logo_portada1">
                                    <label class="custom-file-label">Seleccionar logo_portada1</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="logo-upload-box">
                                    <img id="preview_logo_portada2" src="{{ asset_root('images/imagen_default.png') }}"
                                        class="logo-preview">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo_portada2" name="logo_portada2">
                                    <label class="custom-file-label">Seleccionar logo_portada2</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Contraste General del Tema</label>
                                <select class="form-control" id="tipo_tema" name="tipo_tema">
                                    <option value="dark" {{ ($empresa->tipo_tema ?? '') == 'dark' ? 'selected' : '' }}>
                                        Modo Oscuro (Fondo Oscuro / Letras Blancas)</option>
                                    <option value="light" {{ ($empresa->tipo_tema ?? '') == 'light' ? 'selected' : '' }}>
                                        Modo Claro (Fondo Blanco / Letras Oscuras)</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Color Marca (Base)</label>
                                        <input type="color" class="form-control form-control-color"
                                            value="{{ $empresa->color_main ?? '#3b82f6' }}" id="color_main"
                                            name="color_main">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Color Marca (Hover)</label>
                                        <input type="color" class="form-control form-control-color"
                                            value="{{ $empresa->color_light ?? '#60a5fa' }}" id="color_light"
                                            name="color_light">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Fondo Web</label>
                                        <input type="color" class="form-control form-control-color"
                                            value="{{ $empresa->color_bg ?? '#030712' }}" id="color_bg"
                                            name="color_bg">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Fondo Tarjetas</label>
                                        <input type="color" class="form-control form-control-color"
                                            value="{{ $empresa->color_card ?? '#070b17' }}" id="color_card"
                                            name="color_card">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mb-4">
                <button type="submit" class="save-btn" id="saveBtn">
                    <i class="fas fa-save"></i>
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>

@endsection

@section('script')

    <script>
        /*
        |--------------------------------------------------------------------------
        | PREVIEW LOGO EMPRESA
        |--------------------------------------------------------------------------
        */

        $('#logo').change(function(e) {

            let reader = new FileReader();

            reader.onload = function(e) {

                $('#preview_logo').attr('src', e.target.result);

            }

            reader.readAsDataURL(this.files[0]);

        });

        /*
        |--------------------------------------------------------------------------
        | PREVIEW LOGO PDF
        |--------------------------------------------------------------------------
        */

        $('#logo_pdf').change(function(e) {

            let reader = new FileReader();

            reader.onload = function(e) {

                $('#preview_logo_pdf').attr('src', e.target.result);

            }

            reader.readAsDataURL(this.files[0]);

        });

        $('#logo_portada1').change(function(e) {

            let reader = new FileReader();

            reader.onload = function(e) {

                $('#preview_logo_portada1').attr('src', e.target.result);

            }

            reader.readAsDataURL(this.files[0]);

        });

        $('#logo_portada2').change(function(e) {

            let reader = new FileReader();

            reader.onload = function(e) {

                $('#preview_logo_portada2').attr('src', e.target.result);

            }

            reader.readAsDataURL(this.files[0]);

        });
    </script>

@endsection
