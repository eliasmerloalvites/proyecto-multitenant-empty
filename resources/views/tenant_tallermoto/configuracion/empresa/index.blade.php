@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')

@section('titulo', 'Configuración Empresa')

<style>
    :root {
        --primary-color: #2563eb;
        --border-radius-card: 16px;
        --border-radius-input: 10px;
    }

    .title-main {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .title-main i {
        color: var(--primary-color);
    }

    /* CARD DESIGN */
    .config-card {
        border: none;
        border-radius: var(--border-radius-card);
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        margin-bottom: 24px;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .config-card:hover {
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.08);
    }

    .config-card .card-header {
        border: none;
        padding: 16px 20px;
        display: flex;
        align-items: center;
    }

    .config-card .card-header h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .config-card .card-body {
        padding: 24px;
    }

    /* HEADER GRADIENTS */
    .card-header-blue { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; }
    .card-header-cyan { background: linear-gradient(135deg, #0891b2, #0e7490); color: white; }
    .card-header-dark { background: linear-gradient(135deg, #334155, #0f172a); color: white; }
    .card-header-gray { background: linear-gradient(135deg, #64748b, #334155); color: white; }
    .card-header-purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; }

    /* FORMS */
    .form-group {
        margin-bottom: 18px;
    }

    label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    .form-control {
        height: 44px;
        border-radius: var(--border-radius-input);
        border: 1px solid #cbd5e1;
        font-size: 14px;
        padding: 8px 14px;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
    }

    select.form-control {
        height: 44px !important;
    }

    /* UPLOAD BOX ENHANCED */
    .logo-upload-wrapper {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 16px;
        background: #f8fafc;
        text-align: center;
        transition: all 0.2s ease;
        position: relative;
    }

    .logo-upload-wrapper:hover {
        border-color: var(--primary-color);
        background: #f1f5f9;
    }

    .logo-preview-container {
        width: 100%;
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .logo-preview {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 10px;
    }

    .custom-file-label {
        border-radius: var(--border-radius-input);
        height: 40px;
        line-height: 26px;
        font-size: 13px;
        border-color: #cbd5e1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .custom-file-label::after {
        height: 38px;
        line-height: 26px;
        background-color: #e2e8f0;
        color: #334155;
        font-weight: 600;
    }

    /* COLOR INPUTS */
    .color-picker-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-control-color {
        width: 50px;
        height: 44px;
        padding: 4px;
        cursor: pointer;
        flex-shrink: 0;
    }

    /* SAVE BUTTON */
    .save-btn {
        height: 48px;
        padding: 0 32px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        font-size: 15px;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(37, 99, 235, .3);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .save-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, .4);
    }
</style>

@section('contenido')
    <div class="container-fluid py-3">
        <div class="title-main">
            <i class="fas fa-cog"></i>
            <span>Configuración General de la Empresa</span>
        </div>

        <form method="POST" id="empresa_form" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <!-- DATOS EMPRESA -->
                <div class="col-12 col-lg-6">
                    <div class="card config-card">
                        <div class="card-header card-header-blue">
                            <h6><i class="fas fa-building"></i> Datos Empresa</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="ruc">RUC</label>
                                <input type="number" class="form-control" value="{{ $empresa->ruc ?? '' }}" id="ruc" name="ruc" maxlength="11">
                            </div>

                            <div class="form-group">
                                <label for="razon_social">Razón Social</label>
                                <input type="text" class="form-control" value="{{ $empresa->razon_social ?? '' }}" id="razon_social" name="razon_social">
                            </div>

                            <div class="form-group">
                                <label for="nombre_comercial">Nombre Comercial</label>
                                <input type="text" class="form-control" value="{{ $empresa->nombre_comercial ?? '' }}" id="nombre_comercial" name="nombre_comercial">
                            </div>

                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text" class="form-control" value="{{ $empresa->direccion ?? '' }}" id="direccion" name="direccion">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="departamento">Departamento</label>
                                        <input type="text" class="form-control" value="{{ $empresa->departamento ?? '' }}" id="departamento" name="departamento">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="provincia">Provincia</label>
                                        <input type="text" class="form-control" value="{{ $empresa->provincia ?? '' }}" id="provincia" name="provincia">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="distrito">Distrito</label>
                                        <input type="text" class="form-control" value="{{ $empresa->distrito ?? '' }}" id="distrito" name="distrito">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" class="form-control" value="{{ $empresa->telefono ?? '' }}" id="telefono" name="telefono">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="whatsapp">WhatsApp</label>
                                        <input type="text" class="form-control" value="{{ $empresa->whatsapp ?? '' }}" id="whatsapp" name="whatsapp">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="correo">Correo Electrónico</label>
                                        <input type="email" class="form-control" value="{{ $empresa->correo ?? '' }}" id="correo" name="correo">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="web">Sitio Web</label>
                                        <input type="text" class="form-control" value="{{ $empresa->web ?? '' }}" id="web" name="web">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FACTURACIÓN ELECTRÓNICA -->
                <div class="col-12 col-lg-6">
                    <div class="card config-card">
                        <div class="card-header card-header-cyan">
                            <h6><i class="fas fa-file-invoice"></i> Facturación Electrónica</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="ambiente">Ambiente</label>
                                <select class="form-control" id="ambiente" name="ambiente">
                                    <option value="beta" {{ ($empresa->ambiente ?? '') == 'beta' ? 'selected' : '' }}>BETA (Pruebas)</option>
                                    <option value="produccion" {{ ($empresa->ambiente ?? '') == 'produccion' ? 'selected' : '' }}>PRODUCCIÓN</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sol_usuario">Usuario SOL</label>
                                <input type="text" class="form-control" value="{{ $empresa->sol_usuario ?? '' }}" id="sol_usuario" name="sol_usuario">
                            </div>

                            <div class="form-group">
                                <label for="sol_password">Password SOL</label>
                                <input type="password" class="form-control" value="{{ $empresa->sol_password ?? '' }}" id="sol_password" name="sol_password">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serie_factura">Serie Factura</label>
                                        <input type="text" class="form-control" value="{{ $empresa->serie_factura ?? '' }}" id="serie_factura" name="serie_factura">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serie_boleta">Serie Boleta</label>
                                        <input type="text" class="form-control" value="{{ $empresa->serie_boleta ?? '' }}" id="serie_boleta" name="serie_boleta">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="certificado_ruta">Certificado Digital (.pfx / .p12)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="certificado_ruta" name="certificado_ruta">
                                    <label class="custom-file-label" for="certificado_ruta">
                                        {{ !empty($empresa->certificado_ruta) ? basename($empresa->certificado_ruta) : 'Seleccionar archivo...' }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="certificado_password">Password Certificado</label>
                                <input type="password" class="form-control" value="{{ $empresa->certificado_password ?? '' }}" id="certificado_password" name="certificado_password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LOGO EMPRESA -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card config-card">
                        <div class="card-header card-header-gray">
                            <h6><i class="fas fa-image"></i> Logo Sistema</h6>
                        </div>
                        <div class="card-body">
                            <div class="logo-upload-wrapper">
                                <div class="logo-preview-container">
                                    <img id="preview_logo" 
                                         src="{{ !empty($empresa->logo) ? asset_root($empresa->logo) : asset_root('images/imagen_default.png') }}" 
                                         class="logo-preview" alt="Logo Sistema">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                                    <label class="custom-file-label" for="logo">Cambiar logo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOGO PDF -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card config-card">
                        <div class="card-header card-header-dark">
                            <h6><i class="fas fa-file-pdf"></i> Logo PDF</h6>
                        </div>
                        <div class="card-body">
                            <div class="logo-upload-wrapper">
                                <div class="logo-preview-container">
                                    <img id="preview_logo_pdf" 
                                         src="{{ !empty($empresa->logo_pdf) ? asset_root($empresa->logo_pdf) : asset_root('images/imagen_default.png') }}" 
                                         class="logo-preview" alt="Logo PDF">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo_pdf" name="logo_pdf" accept="image/*">
                                    <label class="custom-file-label" for="logo_pdf">Cambiar logo PDF</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PORTADA 1 -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card config-card">
                        <div class="card-header card-header-purple">
                            <h6><i class="fas fa-desktop"></i> Portada Web 1</h6>
                        </div>
                        <div class="card-body">
                            <div class="logo-upload-wrapper">
                                <div class="logo-preview-container">
                                    <img id="preview_logo_portada1" 
                                         src="{{ !empty($empresa->logo_portada1) ? asset_root($empresa->logo_portada1) : asset_root('images/imagen_default.png') }}" 
                                         class="logo-preview" alt="Portada 1">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo_portada1" name="logo_portada1" accept="image/*">
                                    <label class="custom-file-label" for="logo_portada1">Cambiar Portada 1</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PORTADA 2 -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card config-card">
                        <div class="card-header card-header-purple">
                            <h6><i class="fas fa-desktop"></i> Portada Web 2</h6>
                        </div>
                        <div class="card-body">
                            <div class="logo-upload-wrapper">
                                <div class="logo-preview-container">
                                    <img id="preview_logo_portada2" 
                                         src="{{ !empty($empresa->logo_portada2) ? asset_root($empresa->logo_portada2) : asset_root('images/imagen_default.png') }}" 
                                         class="logo-preview" alt="Portada 2">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo_portada2" name="logo_portada2" accept="image/*">
                                    <label class="custom-file-label" for="logo_portada2">Cambiar Portada 2</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERSONALIZACIÓN DE TEMA -->
            <div class="row">
                <div class="col-12">
                    <div class="card config-card">
                        <div class="card-header card-header-purple">
                            <h6><i class="fas fa-palette"></i> Identidad Visual y Colores de la Marca</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipo_tema">Tema General</label>
                                        <select class="form-control" id="tipo_tema" name="tipo_tema">
                                            <option value="dark" {{ ($empresa->tipo_tema ?? '') == 'dark' ? 'selected' : '' }}>Modo Oscuro</option>
                                            <option value="light" {{ ($empresa->tipo_tema ?? '') == 'light' ? 'selected' : '' }}>Modo Claro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="form-group">
                                        <label for="color_main">Color Marca (Base)</label>
                                        <div class="color-picker-group">
                                            <input type="color" class="form-control form-control-color" value="{{ $empresa->color_main ?? '#3b82f6' }}" id="color_main" name="color_main">
                                            <input type="text" class="form-control" value="{{ $empresa->color_main ?? '#3b82f6' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="form-group">
                                        <label for="color_light">Color Marca (Hover)</label>
                                        <div class="color-picker-group">
                                            <input type="color" class="form-control form-control-color" value="{{ $empresa->color_light ?? '#60a5fa' }}" id="color_light" name="color_light">
                                            <input type="text" class="form-control" value="{{ $empresa->color_light ?? '#60a5fa' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="form-group">
                                        <label for="color_bg">Fondo Web</label>
                                        <div class="color-picker-group">
                                            <input type="color" class="form-control form-control-color" value="{{ $empresa->color_bg ?? '#030712' }}" id="color_bg" name="color_bg">
                                            <input type="text" class="form-control" value="{{ $empresa->color_bg ?? '#030712' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="form-group">
                                        <label for="color_card">Fondo Tarjetas</label>
                                        <div class="color-picker-group">
                                            <input type="color" class="form-control form-control-color" value="{{ $empresa->color_card ?? '#070b17' }}" id="color_card" name="color_card">
                                            <input type="text" class="form-control" value="{{ $empresa->color_card ?? '#070b17' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCIÓN DE GUARDADO -->
            <div class="text-right mb-5">
                <button type="submit" class="save-btn" id="saveBtn">
                    <i class="fas fa-save"></i>
                    <span>Guardar Configuración</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document.body).on('change', '.custom-file-input', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Seleccionar archivo');
        });

        function handleImagePreview(inputId, previewId) {
            $(`#${inputId}`).change(function(e) {
                let file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $(`#${previewId}`).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        handleImagePreview('logo', 'preview_logo');
        handleImagePreview('logo_pdf', 'preview_logo_pdf');
        handleImagePreview('logo_portada1', 'preview_logo_portada1');
        handleImagePreview('logo_portada2', 'preview_logo_portada2');

        $('.form-control-color').on('input', function() {
            $(this).next('input[type="text"]').val($(this).val());
        });
    </script>
@endsection