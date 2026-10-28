{{--
    Marca del vehiculo (opcional) y casilla "Por garantia" de un
    mantenimiento. Va en la misma fila que Unidad y KM de los formularios
    de crear/editar. Lo guarda MarcaVehiculoService::aplicarAMantenimiento.

    Parametros: $prefijo (MGC, MGI, ...) y $registro (el mantenimiento al
    editar; null al crear). Garantia Hero/Lifan vienen marcados por defecto.
--}}
@php
    $marcasVehiculo = \App\Services\TenantTallerMotos\MarcaVehiculoService::activas();
    $marcaActual = $registro->MAR_Id ?? null;
    if ($marcaActual && ! $marcasVehiculo->contains('MAR_Id', $marcaActual)) {
        // Marca desactivada despues de elegirla: se sigue mostrando.
        $marcasVehiculo->push((object) ['MAR_Id' => $marcaActual, 'MAR_Nombre' => \App\Services\TenantTallerMotos\MarcaVehiculoService::nombre($marcaActual) . ' (desactivada)']);
    }
    $campoGarantia = $prefijo . '_EsGarantia';
    $esGarantia = $registro ? (bool) ($registro->{$campoGarantia} ?? false) : in_array($prefijo, ['MGH', 'MGL'], true);
@endphp
<div class="form-group col-md-6">
    <label for="MAR_Id_{{ $prefijo }}">MARCA <span class="text-muted font-weight-normal">(opcional)</span></label>
    <select class="form-control select2 select-marca-vehiculo" id="MAR_Id_{{ $prefijo }}" name="MAR_Id" style="width: 100%;">
        <option value="">Sin marca / no está en la lista</option>
        @foreach ($marcasVehiculo as $marca)
            <option value="{{ $marca->MAR_Id }}" @selected((int) $marcaActual === (int) $marca->MAR_Id)>{{ $marca->MAR_Nombre }}</option>
        @endforeach
    </select>
</div>
<div class="form-group col-md-6 d-flex align-items-end">
    <input type="hidden" name="{{ $campoGarantia }}" value="0">
    <div class="custom-control custom-switch mb-2">
        <input type="checkbox" class="custom-control-input" id="{{ $campoGarantia }}" name="{{ $campoGarantia }}" value="1" @checked($esGarantia)>
        <label class="custom-control-label font-weight-bold" for="{{ $campoGarantia }}">
            <i class="fas fa-shield-alt text-primary mr-1"></i> Mantenimiento por garantía
        </label>
    </div>
</div>
