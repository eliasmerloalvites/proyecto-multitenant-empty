@extends('central.landing.layouts.app')

@section('titulo', 'Política de Privacidad — Kael Tech')

@section('content')

    <section class="relative bg-[#020817] pt-40 pb-16 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-32 w-[500px] h-[500px] bg-cyan-400/15 blur-[180px] rounded-full"></div>
        </div>
        <div class="relative z-10 max-w-3xl mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-black text-white">Política de Privacidad</h1>
            <p class="text-slate-400 mt-3">Última actualización: {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</p>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-3xl mx-auto px-6 text-slate-700 leading-7">

            <p class="mb-6">
                <strong>KAEL DEL VALLE S.A.C.</strong> (RUC 20616106865), con domicilio en
                Pacasmayo, La Libertad, Perú, es responsable del tratamiento de los datos
                personales que recopila a través de la plataforma <strong>Kael Tech</strong>,
                conforme a la Ley N.º 29733, Ley de Protección de Datos Personales, y su
                reglamento.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">1. Datos que recopilamos</h2>
            <ul class="list-disc list-inside mb-6 space-y-2">
                <li>Datos de registro: razón social, RUC, nombre de contacto, correo electrónico, teléfono.</li>
                <li>Datos de facturación: día de pago, historial de pagos, plan contratado.</li>
                <li>Datos operativos que tú mismo ingresas al usar la plataforma (productos, clientes, ventas de tu negocio).</li>
                <li>Datos técnicos básicos (dirección IP, dispositivo, navegador) para seguridad y soporte.</li>
            </ul>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">2. Para qué usamos tus datos</h2>
            <ul class="list-disc list-inside mb-6 space-y-2">
                <li>Brindarte acceso y soporte sobre el servicio contratado.</li>
                <li>Procesar el cobro de tu suscripción.</li>
                <li>Enviarte avisos de facturación (recordatorios, vencimientos, confirmaciones de pago).</li>
                <li>Cumplir obligaciones legales y contables.</li>
            </ul>
            <p class="mb-6">
                No vendemos ni alquilamos tus datos personales a terceros con fines
                publicitarios.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">3. Con quién compartimos datos</h2>
            <p class="mb-4">
                Para poder operar, compartimos ciertos datos con proveedores que actúan
                como encargados de tratamiento, únicamente para el fin específico de cada
                servicio:
            </p>
            <ul class="list-disc list-inside mb-6 space-y-2">
                <li><strong>Culqi</strong> (pasarela de pago): procesa el cobro de tu suscripción. Kael Tech no almacena los datos de tu tarjeta.</li>
                <li><strong>Proveedor de correo transaccional</strong>: envía los avisos automáticos de facturación a tu correo de contacto.</li>
                <li><strong>Proveedor de infraestructura en la nube</strong>: aloja la base de datos de tu cuenta.</li>
            </ul>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">4. Tus derechos (ARCO)</h2>
            <p class="mb-6">
                Puedes solicitar en cualquier momento el Acceso, Rectificación, Cancelación
                u Oposición (derechos ARCO) sobre tus datos personales, escribiendo a
                <a href="mailto:contacto@kael.pe" class="text-blue-600 hover:underline">contacto@kael.pe</a>.
                Atenderemos tu solicitud dentro de los plazos que establece la ley.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">5. Conservación de datos</h2>
            <p class="mb-6">
                Conservamos tus datos mientras tu cuenta esté activa y, luego de una
                cancelación, durante el plazo necesario para cumplir obligaciones legales
                (por ejemplo, tributarias) antes de eliminarlos.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">6. Seguridad</h2>
            <p class="mb-6">
                Aplicamos medidas técnicas razonables para proteger tu información
                (conexiones cifradas, control de acceso). Ningún sistema es 100% infalible,
                pero trabajamos para minimizar riesgos de acceso no autorizado.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">7. Cambios a esta política</h2>
            <p class="mb-6">
                Podemos actualizar esta política ocasionalmente. Los cambios relevantes se
                comunicarán por correo o dentro del panel antes de entrar en vigencia.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">8. Contacto</h2>
            <p class="mb-2">
                KAEL DEL VALLE S.A.C. — RUC 20616106865
            </p>
            <p class="mb-2">Pacasmayo, La Libertad, Perú</p>
            <p class="mb-2">
                Correo: <a href="mailto:contacto@kael.pe" class="text-blue-600 hover:underline">contacto@kael.pe</a>
            </p>
            <p>Teléfono/WhatsApp: +51 953 765 418</p>

        </div>
    </section>

    @include('central.landing.sections.footer')

@endsection
