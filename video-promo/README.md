# Videos promocionales para talleres (Kael Tech)

Videos verticales 1080×1920 (TikTok, Reels, WhatsApp):

- **PromoWeb**: el sistema web para talleres de motos (lo que ya está en producción). Guion en `src/guion-web.json`.
- **PromoTaller**: la app móvil para los clientes del taller (lanzamiento posterior). Guion en `src/guion.json`.
Hecho con [Remotion](https://www.remotion.dev) (React → MP4).

## Cómo funciona

- `src/guion.json`: el guion. Cada escena tiene su texto narrado, las palabras que se resaltan, la etiqueta y el fondo.
- `npm run voz`: genera la narración de cada escena con las voces neuronales gratuitas de Microsoft Edge
  (`es-PE-AlexNeural`, voz peruana) y guarda los tiempos de cada palabra en `src/voz.json`.
  Los subtítulos se sincronizan con esos tiempos.
- `public/img/logo-kaeltech.jpg`: logo de Kael Tech.
- `npm run musica`: genera la música de fondo original (`public/musica/fondo.wav`), sintetizada por código, sin derechos de terceros.
- `npm run capturas`: captura pantallas reales del sistema LOCAL (`motoclub9.localhost:8000`) en `public/capturas/`,
  con nombres, celulares y documentos difuminados. Usa el usuario de prueba de `.env.capturas` (no se sube a git)
  y necesita el MySQL de `C:\xampp` y `php artisan serve` corriendo.

## Comandos

```bash
npm install
npm run capturas    # vuelve a tomar las capturas reales del sistema local
npm run musica      # vuelve a generar la música de fondo
npm run voz:web     # narración del video web (solo si cambiaste src/guion-web.json; usa internet)
npm run voz         # narración del video de la app
npm run studio      # vista previa y ajustes en el navegador
npm run render:web  # genera out/promo-sistema-web.mp4
npm run render      # genera out/promo-app-taller.mp4
```

Si el servicio de voz responde "429", es un límite temporal: el script espera y reintenta solo.
Para otra voz, cambia `voz` en `src/guion.json` (por ejemplo `es-PE-CamilaNeural`).
