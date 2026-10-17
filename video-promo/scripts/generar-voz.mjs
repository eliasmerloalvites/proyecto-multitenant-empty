// Genera la narración de cada escena con las voces neuronales gratuitas de
// Microsoft Edge (msedge-tts) y guarda, por escena, el audio y el instante en
// que se dice cada palabra. Con eso el video sincroniza los subtítulos.
//
// Uso: npm run voz        → video de la app (src/guion.json)
//      npm run voz:web    → video del sistema web (src/guion-web.json)
import { execFileSync } from 'node:child_process';
import { existsSync, mkdirSync, readFileSync, renameSync, rmSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import ffmpeg from 'ffmpeg-static';
import { MsEdgeTTS, OUTPUT_FORMAT } from 'msedge-tts';

const raiz = join(dirname(fileURLToPath(import.meta.url)), '..');
const sufijo = process.argv[2] ? `-${process.argv[2]}` : '';
const guion = JSON.parse(readFileSync(join(raiz, `src/guion${sufijo}.json`), 'utf8'));
const carpeta = `voz${sufijo}`;
const dirVoz = join(raiz, 'public', carpeta);
mkdirSync(dirVoz, { recursive: true });

function duracionSegundos(archivo) {
  // ffmpeg escribe la duración en stderr; el comando "falla" a propósito (sin salida).
  let salida = '';
  try {
    execFileSync(ffmpeg, ['-hide_banner', '-i', archivo], { stdio: 'pipe' });
  } catch (e) {
    salida = String(e.stderr);
  }
  const m = salida.match(/Duration: (\d+):(\d+):([\d.]+)/);
  if (!m) throw new Error(`Sin duración para ${archivo}`);
  return Number(m[1]) * 3600 + Number(m[2]) * 60 + Number(m[3]);
}

const resultado = [];

const esperar = (ms) => new Promise((r) => setTimeout(r, ms));

// El servicio limita las conexiones seguidas (HTTP 429): se espera entre
// escenas y se reintenta con pausas cada vez más largas.
async function sintetizar(tmp, texto) {
  for (let intento = 1; ; intento++) {
    const tts = new MsEdgeTTS();
    try {
      await tts.setMetadata(guion.voz, OUTPUT_FORMAT.AUDIO_24KHZ_96KBITRATE_MONO_MP3, { wordBoundaryEnabled: true });
      return await tts.toFile(tmp, texto, { rate: guion.velocidad, pitch: guion.tono });
    } catch (e) {
      if (intento >= 10) throw e;
      console.log(`  reintento ${intento} en ${intento * 15} s (${e.message.slice(0, 60)})`);
      await esperar(intento * 15000);
    } finally {
      tts.close();
    }
  }
}

for (const escena of guion.escenas) {
  // Reanudable: si esta escena ya se generó con el mismo texto y voz, se reutiliza.
  const clave = JSON.stringify([escena.texto, guion.voz, guion.velocidad, guion.tono]);
  const cache = join(dirVoz, `${escena.id}.json`);
  if (existsSync(cache) && existsSync(join(dirVoz, `${escena.id}.mp3`))) {
    const previo = JSON.parse(readFileSync(cache, 'utf8'));
    if (previo.clave === clave) {
      resultado.push(previo.datos);
      console.log(`= ${escena.id}: sin cambios`);
      continue;
    }
  }

  const tmp = join(dirVoz, `_tmp_${escena.id}`);
  rmSync(tmp, { recursive: true, force: true });
  mkdirSync(tmp, { recursive: true });
  const { audioFilePath, metadataFilePath } = await sintetizar(tmp, escena.texto);
  await esperar(2500);

  const destino = join(dirVoz, `${escena.id}.mp3`);
  rmSync(destino, { force: true });
  renameSync(audioFilePath, destino);

  // El archivo de metadatos es un JSON con la lista de WordBoundary.
  const crudo = metadataFilePath ? readFileSync(metadataFilePath, 'utf8') : '{}';
  const palabras = (JSON.parse(crudo).Metadata ?? [])
    .filter((m) => m.Type === 'WordBoundary')
    .map((m) => ({
      texto: m.Data.text.Text,
      inicio: m.Data.Offset / 1e7, // ticks de 100 ns → segundos
      fin: (m.Data.Offset + m.Data.Duration) / 1e7,
    }));
  rmSync(tmp, { recursive: true, force: true });

  const duracion = duracionSegundos(destino);
  const datos = { id: escena.id, audio: `${carpeta}/${escena.id}.mp3`, duracion, palabras };
  writeFileSync(cache, JSON.stringify({ clave, datos }));
  resultado.push(datos);
  console.log(`✓ ${escena.id}: ${duracion.toFixed(2)} s, ${palabras.length} palabras`);
}

writeFileSync(join(raiz, `src/voz${sufijo}.json`), JSON.stringify(resultado, null, 2));
console.log(`Listo: src/voz${sufijo}.json`);
