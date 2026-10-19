{{--
    Boton "Tomar foto" para las fotos de un mantenimiento/actividad.

    Se incluye al final de @section('script') de cada edit, DESPUES de su
    <script> principal. Necesita de esa vista: images, id_mpi, MAX_IMAGES,
    cargarImagenes() y cargarFileForImagen() (que aqui se envuelve para
    agregar el boton cada vez que se redibuja la zona de subida).

    - En celular abre la camara trasera (input con capture).
    - En PC abre la webcam en un modal (getUserMedia).
    - La foto se reduce en el navegador (max 1600 px, JPEG) antes de subir;
      el servidor la vuelve a optimizar a WebP 1200 px + miniatura.

    Parametros: $urlBase (ruta index del modulo) y $prefijo (MGCI, MGII, ...).
--}}
<style>
    .foto-camara-acciones { display: flex; justify-content: center; margin-top: -12px; margin-bottom: 22px; }
    .btn-tomar-foto {
        display: inline-flex; align-items: center; gap: 8px;
        background: #1f6feb; color: #fff; border: 0; border-radius: 10px;
        padding: 10px 20px; font-weight: 600; transition: .2s;
    }
    .btn-tomar-foto:hover, .btn-tomar-foto:focus { background: #1858c4; color: #fff; outline: none; box-shadow: 0 0 0 3px rgba(31, 111, 235, .3); }
    .btn-tomar-foto:disabled { opacity: .65; cursor: wait; }
    #modalFotoCamara video { width: 100%; max-height: 60vh; background: #000; border-radius: 12px; object-fit: contain; }
    #modalFotoCamara .modal-content { border-radius: 18px; }
</style>

<div class="modal fade" id="modalFotoCamara" tabindex="-1" aria-labelledby="modalFotoCamaraTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFotoCamaraTitulo"><i class="fas fa-camera mr-2"></i>Tomar foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <video id="fotoCamaraVideo" autoplay playsinline muted></video>
                <p class="text-muted small mt-2 mb-0" id="fotoCamaraAviso"></p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" id="fotoCamaraCambiar" style="display: none;">
                    <i class="fas fa-sync-alt mr-1"></i> Cambiar cámara
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="fotoCamaraCapturar" disabled>
                        <i class="fas fa-camera mr-1"></i> Capturar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="file" id="fotoCamaraInput" accept="image/*" capture="environment" hidden>

<script>
    (function() {
        const URL_BASE = @json($urlBase);
        const PREFIJO = @json($prefijo);
        const LADO_MAXIMO = 1600;
        const CALIDAD = 0.82;

        let stream = null;
        let camaras = [];
        let camaraActual = 0;

        // Celular/tablet: el input con "capture" abre la camara del equipo.
        // En PC ese atributo se ignora, por eso ahi se usa la webcam.
        const esMovil = window.matchMedia('(pointer: coarse)').matches || /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

        const aviso = (tipo, texto) => Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, type: tipo, title: texto });

        function agregarBoton() {
            const zona = document.querySelector('.fileforImagen');
            if (!zona || zona.querySelector('.btn-tomar-foto')) return;
            const cont = document.createElement('div');
            cont.className = 'foto-camara-acciones';
            cont.innerHTML = '<button type="button" class="btn-tomar-foto"><i class="fas fa-camera"></i> Tomar foto</button>';
            zona.appendChild(cont);
        }

        // Se redibuja la zona de subida cada vez que se sube una imagen:
        // se envuelve la funcion de la vista para volver a poner el boton.
        const original = window.cargarFileForImagen;
        if (typeof original === 'function') {
            window.cargarFileForImagen = function() {
                original.apply(this, arguments);
                agregarBoton();
            };
        }
        $(agregarBoton);

        $('body').on('click', '.btn-tomar-foto', function() {
            if (images.length >= MAX_IMAGES) {
                Swal.fire({ type: 'warning', title: 'Límite alcanzado', text: 'Tu plan permite máximo ' + MAX_IMAGES + ' imágenes.' });
                return;
            }
            if (esMovil || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                $('#fotoCamaraInput').val('').trigger('click');
            } else {
                abrirWebcam();
            }
        });

        $('#fotoCamaraInput').on('change', function() {
            const archivo = this.files && this.files[0];
            if (archivo) reducirYSubir(archivo);
        });

        async function abrirWebcam() {
            $('#fotoCamaraAviso').text('Permite el acceso a la cámara si el navegador lo pide.');
            $('#fotoCamaraCapturar').prop('disabled', true);
            $('#modalFotoCamara').modal('show');
            try {
                await iniciarStream();
                const dispositivos = await navigator.mediaDevices.enumerateDevices();
                camaras = dispositivos.filter((d) => d.kind === 'videoinput');
                $('#fotoCamaraCambiar').toggle(camaras.length > 1);
            } catch (e) {
                $('#modalFotoCamara').modal('hide');
                // Sin camara o sin permiso: se ofrece elegir un archivo.
                Swal.fire({
                    type: 'info',
                    title: 'No se pudo abrir la cámara',
                    text: 'Revisa que el equipo tenga cámara y que el navegador tenga permiso. También puedes elegir una foto guardada.',
                    showCancelButton: true, confirmButtonText: 'Elegir archivo', cancelButtonText: 'Cerrar'
                }).then((r) => { if (r.value) $('#fotoCamaraInput').val('').trigger('click'); });
            }
        }

        async function iniciarStream() {
            detenerStream();
            const video = camaras[camaraActual]
                ? { deviceId: { exact: camaras[camaraActual].deviceId } }
                : { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } };
            stream = await navigator.mediaDevices.getUserMedia({ video: video, audio: false });
            const el = document.getElementById('fotoCamaraVideo');
            el.srcObject = stream;
            await el.play();
            $('#fotoCamaraAviso').text('');
            $('#fotoCamaraCapturar').prop('disabled', false);
        }

        function detenerStream() {
            if (stream) stream.getTracks().forEach((t) => t.stop());
            stream = null;
        }

        $('#fotoCamaraCambiar').on('click', function() {
            camaraActual = (camaraActual + 1) % camaras.length;
            iniciarStream().catch(() => aviso('error', 'No se pudo cambiar de cámara.'));
        });

        $('#modalFotoCamara').on('hidden.bs.modal', detenerStream);

        $('#fotoCamaraCapturar').on('click', function() {
            const video = document.getElementById('fotoCamaraVideo');
            if (!video.videoWidth) return;
            const lienzo = dibujarReducido(video, video.videoWidth, video.videoHeight);
            lienzo.toBlob((blob) => {
                $('#modalFotoCamara').modal('hide');
                if (blob) subir(blob);
            }, 'image/jpeg', CALIDAD);
        });

        function dibujarReducido(fuente, ancho, alto) {
            const escala = Math.min(1, LADO_MAXIMO / Math.max(ancho, alto));
            const lienzo = document.createElement('canvas');
            lienzo.width = Math.round(ancho * escala);
            lienzo.height = Math.round(alto * escala);
            lienzo.getContext('2d').drawImage(fuente, 0, 0, lienzo.width, lienzo.height);
            return lienzo;
        }

        function reducirYSubir(archivo) {
            const url = URL.createObjectURL(archivo);
            const img = new Image();
            img.onload = function() {
                URL.revokeObjectURL(url);
                dibujarReducido(img, img.naturalWidth, img.naturalHeight).toBlob((blob) => {
                    subir(blob || archivo);
                }, 'image/jpeg', CALIDAD);
            };
            img.onerror = function() {
                // Formato que el navegador no sabe dibujar (ej. HEIC en
                // Chrome): se sube tal cual y el servidor lo optimiza.
                URL.revokeObjectURL(url);
                subir(archivo);
            };
            img.src = url;
        }

        function subir(blob) {
            if (typeof isImgProcessing !== 'undefined' && isImgProcessing) {
                aviso('info', 'Espera a que termine la imagen anterior.');
                return;
            }
            if (typeof isImgProcessing !== 'undefined') isImgProcessing = true;

            const $btn = $('.btn-tomar-foto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
            const datos = new FormData();
            datos.append('_token', '{{ csrf_token() }}');
            datos.append('file', blob, 'foto-' + Date.now() + '.jpg');

            $.ajax({
                url: URL_BASE + '/' + id_mpi + '/crop',
                type: 'POST', data: datos, processData: false, contentType: false,
                success: function(r) {
                    const lista = (r && r.msg && r.msg.data) || [];
                    images = lista.map((d) => ({
                        id: d[PREFIJO + '_Item'], src: d[PREFIJO + '_url'],
                        name: d[PREFIJO + '_Nombre'], size: d[PREFIJO + '_Peso']
                    }));
                    cargarImagenes();
                    aviso('success', (r && r.msg && r.msg.mensaje) || 'Foto guardada.');
                },
                error: function(x) {
                    const j = x.responseJSON || {};
                    const msg = j.msg || j.message || (j.errors && j.errors.file && j.errors.file[0]) || 'No se pudo subir la foto.';
                    aviso('error', msg);
                },
                complete: function() {
                    if (typeof isImgProcessing !== 'undefined') isImgProcessing = false;
                    $('.btn-tomar-foto').prop('disabled', false).html('<i class="fas fa-camera"></i> Tomar foto');
                }
            });
        }
    })();
</script>
