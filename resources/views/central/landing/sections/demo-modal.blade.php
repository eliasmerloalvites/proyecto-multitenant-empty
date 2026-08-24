    <!-- ======================================= -->
    <!-- MODAL DEMO (video) -->
    <!-- ======================================= -->

    {{--
        Sube tu video a public/videos/demo.mp4 (mp4 es el formato con mejor
        soporte en todos los navegadores). Los botones "Ver demos en vivo"
        (hero) y "Ver demostración" (footer) abren este mismo modal — buscan
        cualquier elemento con [data-open-demo].
    --}}

    <div id="demoModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/90 backdrop-blur-sm p-4"
        role="dialog" aria-modal="true" aria-label="Video de demostración de Kael Tech">

        <div class="relative w-full max-w-4xl">

            <button type="button" data-close-demo
                class="absolute -top-12 right-0 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20
                    border border-white/15 text-white flex items-center justify-center transition"
                aria-label="Cerrar video">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="rounded-2xl overflow-hidden shadow-2xl bg-black aspect-video">

                <video id="demoVideo" class="w-full h-full" controls preload="none"
                    poster="{{ asset('videos/Registro Kael Tech Escritorio.mp4') }}">
                    <source src="{{ asset('videos/Registro Kael Tech Escritorio.mp4') }}" type="video/mp4">
                </video>

                <div id="demoVideoFallback"
                    class="hidden w-full h-full flex-col items-center justify-center text-center text-slate-300 p-10">
                    <i class="fa-solid fa-video-slash text-4xl mb-4 text-slate-500"></i>
                    <p class="font-semibold">El video de demostración aún no está disponible.</p>
                    <p class="text-sm text-slate-500 mt-1">Vuelve a intentarlo más tarde.</p>
                </div>

            </div>

        </div>

    </div>

    <script>
        (function () {
            const modal = document.getElementById('demoModal');
            const video = document.getElementById('demoVideo');
            const fallback = document.getElementById('demoVideoFallback');

            function openDemo(e) {
                e.preventDefault();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                video.play().catch(() => {});
            }

            function closeDemo() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
                video.pause();
            }

            document.querySelectorAll('[data-open-demo]').forEach((btn) => {
                btn.addEventListener('click', openDemo);
            });

            document.querySelectorAll('[data-close-demo]').forEach((btn) => {
                btn.addEventListener('click', closeDemo);
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeDemo();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeDemo();
            });

            // Si el archivo no existe todavía (subida manual pendiente), se
            // muestra un mensaje en vez de un reproductor roto.
            video.addEventListener('error', function () {
                video.classList.add('hidden');
                fallback.classList.remove('hidden');
                fallback.classList.add('flex');
            });
        })();
    </script>
