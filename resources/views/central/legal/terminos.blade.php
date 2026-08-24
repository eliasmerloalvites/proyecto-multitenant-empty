@extends('central.landing.layouts.app')

@section('titulo', 'Términos y Condiciones — Kael Tech')

@section('content')

    <section class="relative bg-[#020817] pt-40 pb-16 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-32 w-[500px] h-[500px] bg-blue-500/20 blur-[180px] rounded-full"></div>
        </div>
        <div class="relative z-10 max-w-3xl mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-black text-white">Términos y Condiciones</h1>
            <p class="text-slate-400 mt-3">Última actualización: {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</p>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-3xl mx-auto px-6 text-slate-700 leading-7">

            <p class="mb-6">
                Estos Términos y Condiciones regulan el uso de la plataforma <strong>Kael Tech</strong>,
                un servicio de software como servicio (SaaS) operado por
                <strong>KAEL DEL VALLE S.A.C.</strong> (RUC 20616106865), con domicilio en
                Pacasmayo, La Libertad, Perú. Al crear una cuenta o usar la plataforma,
                aceptas estos términos en su totalidad.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">1. El servicio</h2>
            <p class="mb-6">
                Kael Tech ofrece un panel de administración en la nube (ventas, inventario,
                facturación y módulos adicionales según el rubro del negocio: talleres de
                motos, negocios genéricos, entre otros) organizado en planes de suscripción
                mensual (Start, Basic, Plus, Empresarial).
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">2. Cuenta y registro</h2>
            <p class="mb-6">
                Debes proporcionar información veraz al registrarte (razón social, RUC,
                correo de contacto). Eres responsable de la confidencialidad de tus
                credenciales de acceso y de toda actividad realizada desde tu cuenta.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">3. Periodo de prueba</h2>
            <p class="mb-6">
                Las nuevas cuentas inician con un periodo de prueba gratuito. Durante ese
                periodo no se realiza ningún cobro. Al finalizar la prueba, la cuenta entra
                al ciclo de facturación normal de su plan.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">4. Planes, precios y facturación</h2>
            <p class="mb-4">
                Los precios de cada plan se muestran en soles (S/) en nuestra página de
                planes y pueden ajustarse en casos particulares (por ejemplo, límites o
                condiciones distintas a las estándar), lo cual se comunica y refleja en tu
                panel de cliente antes de aplicarse.
            </p>
            <p class="mb-6">
                El cobro se realiza de forma periódica según el día de facturación asignado
                a tu cuenta. Los pagos se procesan a través de <strong>Culqi</strong>, una
                pasarela de pago certificada; Kael Tech no almacena los datos de tu tarjeta
                en ningún momento.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">5. Cancelación y política de reembolsos</h2>
            <p class="mb-4">
                Puedes cancelar tu suscripción en cualquier momento contactando a
                <a href="mailto:contacto@kael.pe" class="text-blue-600 hover:underline">contacto@kael.pe</a>.
                La cancelación aplica a partir del siguiente ciclo de facturación: seguirás
                teniendo acceso hasta el final del periodo ya pagado.
            </p>
            <p class="mb-6">
                <strong>Los pagos ya realizados por un ciclo de facturación no son
                reembolsables.</strong> Al no cobrarse nada durante el periodo de prueba, no
                existe ningún cargo que reembolsar antes de tu primer pago.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">6. Suspensión por falta de pago</h2>
            <p class="mb-6">
                Si un pago no se regulariza dentro del plazo de gracia informado por correo,
                el acceso a la cuenta puede suspenderse automáticamente hasta que el pago
                sea regularizado. Los datos de tu cuenta no se eliminan por una suspensión.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">7. Propiedad de tus datos</h2>
            <p class="mb-6">
                La información que registras en tu cuenta (productos, ventas, clientes,
                inventario) te pertenece a ti. Kael Tech la utiliza únicamente para prestar
                el servicio contratado.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">8. Limitación de responsabilidad</h2>
            <p class="mb-6">
                Kael Tech se esfuerza por mantener el servicio disponible de forma
                continua, pero no garantiza que esté libre de interrupciones. No somos
                responsables por pérdidas derivadas del uso indebido de la plataforma o de
                fallas ajenas a nuestro control (por ejemplo, de la pasarela de pago o de
                proveedores de infraestructura).
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">9. Modificaciones</h2>
            <p class="mb-6">
                Podemos actualizar estos términos ocasionalmente. Los cambios relevantes se
                comunicarán por correo o dentro del panel antes de entrar en vigencia.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">10. Ley aplicable</h2>
            <p class="mb-6">
                Estos términos se rigen por las leyes de la República del Perú.
            </p>

            <h2 class="text-xl font-bold text-slate-900 mt-10 mb-3">11. Contacto</h2>
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
