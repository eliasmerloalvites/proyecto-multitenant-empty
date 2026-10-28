{{--
    Tabla de firmas de los PDF de mantenimiento (pdf y orden de servicio).
    Si ya firmaron en el sistema, la firma sale sobre su linea con el nombre
    y la fecha; si no, queda la linea en blanco para firmar en papel.

    Parametros: $tabla (tabla del mantenimiento), $registroId (id del
    mantenimiento) y $subCliente (texto bajo "FIRMA DEL CLIENTE"). El id se
    pasa explicito porque el mismo PDF lo genera la API de la app movil,
    cuya ruta no tiene {id} (usa {ref}, ej. mgc-7).
--}}
@php
    $firmasPdf = \App\Services\TenantTallerMotos\FirmasMantenimientoService::firmas($tabla, (int) ($registroId ?? request()->route('id')));
    $empresaFirma = $empresa ?? \App\Models\Tenant\EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
    $nombreEmpresaFirma = $empresaFirma ? ($empresaFirma->nombre_comercial ?: $empresaFirma->razon_social) : '';
    $columnasFirma = [
        ['rol' => 'CLIENTE', 'titulo' => 'FIRMA DEL CLIENTE', 'sub' => $subCliente ?? 'Conforme'],
        ['rol' => 'TECNICO', 'titulo' => 'FIRMA DEL TÉCNICO', 'sub' => 'Responsable'],
        ['rol' => 'REPRESENTANTE', 'titulo' => 'FIRMA DEL REPRESENTANTE', 'sub' => $nombreEmpresaFirma],
    ];
@endphp
<table class="sign-table">
    <tr>
        @foreach ($columnasFirma as $col)
            @php
                $f = $firmasPdf->get($col['rol']);
                $img = $f ? \App\Services\TenantTallerMotos\FirmasMantenimientoService::dataUri($f->FIR_Imagen) : null;
            @endphp
            <td>
                @if ($img)
                    <div style="height: 58px; text-align: center;">
                        <img src="{{ $img }}" style="height: 56px; max-width: 100%;" alt="">
                    </div>
                @endif
                <div class="sign-line" @if ($img) style="margin-top: 2px;" @endif></div>
                <div class="sign-title">{{ $col['titulo'] }}</div>
                @if ($f)
                    <div class="sign-sub">
                        {{ $f->FIR_Nombre }}@if ($f->FIR_Documento) · DNI {{ $f->FIR_Documento }}@endif @if (($f->FIR_Origen ?? null) === 'APP') · desde la app @endif
                        <br>{{ \Carbon\Carbon::parse($f->created_at)->format('d/m/Y H:i') }}
                    </div>
                @else
                    <div class="sign-sub">{{ $col['sub'] }}</div>
                @endif
            </td>
        @endforeach
    </tr>
</table>
