<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motors Bike Service</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .hero {
            background:
                linear-gradient(rgba(0, 0, 0, .7),
                    rgba(0, 0, 0, .7)),
                url('https://images.unsplash.com/photo-1558981806-ec527fa84c39');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- HEADER -->
    <header class="bg-white shadow sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">

            <h1 class="text-2xl font-bold text-brand-700">
                KAEL TECH
            </h1>

            <nav class="hidden md:flex gap-6">
                <a href="#servicios" class="hover:text-brand-600">Servicios</a>
                <a href="#reserva" class="hover:text-brand-600">Reservar</a>
                <a href="#productos" class="hover:text-brand-600">Productos</a>
                <a href="#contacto" class="hover:text-brand-600">Contacto</a>
            </nav>

        </div>
    </header>

    <!-- HERO -->
    <section class="hero min-h-screen flex items-center">

        <div class="container mx-auto px-6 text-center text-white">

            <h2 class="text-5xl font-bold mb-6">
                Mantenimiento Profesional para tu Moto
            </h2>

            <p class="text-xl mb-8 max-w-3xl mx-auto">
                Técnicos especializados, repuestos garantizados y seguimiento online
                de todas tus reparaciones.
            </p>

            <div class="flex flex-wrap justify-center gap-4">

                <a href="#reserva"
                    class="bg-brand-600 hover:bg-brand-700 px-8 py-4 rounded-lg font-semibold">
                    Reservar Cita
                </a>

                <a href="https://wa.me/51929386665"
                    class="bg-green-500 hover:bg-green-600 px-8 py-4 rounded-lg font-semibold">
                    WhatsApp
                </a>

            </div>

        </div>

    </section>

    <!-- CONSULTA PLACA -->
    <section class="py-20 bg-white">

        <div class="container mx-auto px-6">

            <div class="text-center mb-10">

                <h3 class="text-4xl font-bold">
                    Consulta el Historial de tu Moto
                </h3>

                <p class="mt-3 text-gray-500">
                    Ingresa tu placa y revisa mantenimientos anteriores.
                </p>

            </div>

            <div class="max-w-xl mx-auto flex gap-3">

                <input
                    type="text"
                    placeholder="Ejemplo: 1234-AB"
                    class="w-full border rounded-lg p-4">

                <button
                    class="bg-brand-600 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} px-6 rounded-lg">
                    Buscar
                </button>

            </div>

        </div>

    </section>

    <!-- RESERVA -->
    <section id="reserva" class="py-20">

        <div class="container mx-auto px-6">

            <div class="text-center mb-12">
                <h3 class="text-4xl font-bold">
                    Agenda tu Servicio
                </h3>
            </div>

            <form class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow">

                <div class="grid md:grid-cols-2 gap-4">

                    <input type="text" placeholder="Nombre"
                        class="border p-3 rounded-lg">

                    <input type="text" placeholder="Teléfono"
                        class="border p-3 rounded-lg">

                    <input type="text" placeholder="Placa"
                        class="border p-3 rounded-lg">

                    <input type="text" placeholder="Modelo"
                        class="border p-3 rounded-lg">

                    <input type="date"
                        class="border p-3 rounded-lg">

                    <input type="time"
                        class="border p-3 rounded-lg">

                </div>

                <button
                    class="w-full mt-6 bg-brand-600 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} py-4 rounded-lg">
                    Reservar Ahora
                </button>

            </form>

        </div>

    </section>

    <!-- SERVICIOS -->
    <section id="servicios" class="py-20 bg-white">

        <div class="container mx-auto px-6">

            <h3 class="text-4xl font-bold text-center mb-12">
                Nuestros Servicios
            </h3>

            <div class="grid md:grid-cols-3 gap-8">

                <div class="bg-gray-100 p-6 rounded-xl">
                    <h4 class="font-bold text-xl mb-3">
                        Cambio de Aceite
                    </h4>
                    <p>
                        Protege el motor y mejora el rendimiento.
                    </p>
                </div>

                <div class="bg-gray-100 p-6 rounded-xl">
                    <h4 class="font-bold text-xl mb-3">
                        Afinamiento General
                    </h4>
                    <p>
                        Revisión completa de tu motocicleta.
                    </p>
                </div>

                <div class="bg-gray-100 p-6 rounded-xl">
                    <h4 class="font-bold text-xl mb-3">
                        Sistema de Frenos
                    </h4>
                    <p>
                        Diagnóstico y mantenimiento profesional.
                    </p>
                </div>

                <div class="bg-gray-100 p-6 rounded-xl">
                    <h4 class="font-bold text-xl mb-3">
                        Sistema Eléctrico
                    </h4>
                    <p>
                        Luces, batería y cableado.
                    </p>
                </div>

                <div class="bg-gray-100 p-6 rounded-xl">
                    <h4 class="font-bold text-xl mb-3">
                        Suspensión
                    </h4>
                    <p>
                        Reparación y mantenimiento.
                    </p>
                </div>

                <div class="bg-gray-100 p-6 rounded-xl">
                    <h4 class="font-bold text-xl mb-3">
                        Diagnóstico Computarizado
                    </h4>
                    <p>
                        Detección rápida de fallas.
                    </p>
                </div>

            </div>

        </div>

    </section>

    <!-- BENEFICIOS -->
    <section class="py-20">

        <div class="container mx-auto px-6">

            <h3 class="text-4xl font-bold text-center mb-12">
                ¿Por qué elegirnos?
            </h3>

            <div class="grid md:grid-cols-3 gap-8">

                <div class="bg-white p-6 rounded-xl shadow">
                    ✅ Técnicos Certificados
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    ✅ Repuestos Garantizados
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    ✅ Historial Digital
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    ✅ Seguimiento Online
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    ✅ Atención Rápida
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    ✅ Garantía de Servicio
                </div>

            </div>

        </div>

    </section>

    <!-- TESTIMONIOS -->
    <section class="py-20 bg-white">

        <div class="container mx-auto px-6">

            <h3 class="text-4xl font-bold text-center mb-12">
                Lo que dicen nuestros clientes
            </h3>

            <div class="grid md:grid-cols-3 gap-8">

                <div class="bg-gray-100 p-6 rounded-xl">
                    ⭐⭐⭐⭐⭐
                    <p class="mt-4">
                        Excelente atención y entrega puntual.
                    </p>
                </div>

                <div class="bg-gray-100 p-6 rounded-xl">
                    ⭐⭐⭐⭐⭐
                    <p class="mt-4">
                        Ahora puedo revisar todo mi historial online.
                    </p>
                </div>

                <div class="bg-gray-100 p-6 rounded-xl">
                    ⭐⭐⭐⭐⭐
                    <p class="mt-4">
                        Muy buenos precios y servicio profesional.
                    </p>
                </div>

            </div>

        </div>

    </section>

    <!-- CONTACTO -->
    <section id="contacto" class="py-20">

        <div class="container mx-auto px-6 text-center">

            <h3 class="text-4xl font-bold mb-6">
                Contáctanos
            </h3>

            <p class="text-lg mb-4">
                📞 929 386 665
            </p>

            <p class="text-lg mb-8">
                📍 Tu dirección aquí
            </p>

            <a href="https://wa.me/51929386665"
                class="bg-green-500 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} px-8 py-4 rounded-lg">
                Escribir por WhatsApp
            </a>

        </div>

    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-900 {{ $colorview == 'dark' ? 'text-gray-400' : 'text-gray-600' }} py-10">

        <div class="container mx-auto px-6 text-center">

            <h4 class="text-2xl font-bold mb-4">
                KAEL TECH
            </h4>

            <p>
                Tu moto merece el mejor cuidado.
            </p>

            <p class="mt-4 text-gray-400">
                © 2026 Todos los derechos reservados.
            </p>

        </div>

    </footer>

</body>

</html>