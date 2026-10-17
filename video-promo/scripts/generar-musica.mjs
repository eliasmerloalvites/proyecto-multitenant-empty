// Música de fondo original (sin derechos de terceros), sintetizada aquí mismo:
// pop electrónico a 112 BPM con bombo, palmas, hi-hats, bajo y acordes
// (Do - Sol - La menor - Fa). Genera public/musica/fondo.wav.
//
// Uso: node scripts/generar-musica.mjs [segundos]
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const raiz = join(dirname(fileURLToPath(import.meta.url)), '..');
const SR = 44100;
const DURACION = Number(process.argv[2] ?? 80);
const BPM = 112;
const negra = 60 / BPM;
const compas = negra * 4;
const n = Math.floor(DURACION * SR);
const L = new Float32Array(n);
const R = new Float32Array(n);

const frec = (midi) => 440 * 2 ** ((midi - 69) / 12);
// Do, Sol, La m, Fa (notas MIDI del acorde y raíz del bajo)
const acordes = [
  { notas: [60, 64, 67, 72], bajo: 36 },
  { notas: [59, 62, 67, 71], bajo: 43 },
  { notas: [57, 60, 64, 69], bajo: 45 },
  { notas: [57, 60, 65, 69], bajo: 41 },
];

let semilla = 7;
const ruido = () => ((semilla = (semilla * 1103515245 + 12345) & 0x7fffffff) / 0x7fffffff) * 2 - 1;

function sumar(inicio, dur, fn, pan = 0) {
  const a = Math.floor(inicio * SR);
  const b = Math.min(n, Math.floor((inicio + dur) * SR));
  for (let i = Math.max(0, a); i < b; i++) {
    const v = fn((i - a) / SR);
    L[i] += v * (1 - pan) * 0.5 * 2;
    R[i] += v * (1 + pan) * 0.5 * 2;
  }
}

const compases = Math.ceil(DURACION / compas);
for (let c = 0; c < compases; c++) {
  const t0 = c * compas;
  const ac = acordes[c % 4];
  const intro = c < 2; // los dos primeros compases sin batería completa
  const final = t0 > DURACION - compas * 2;

  for (let g = 0; g < 4; g++) {
    const tg = t0 + g * negra;
    // Bombo
    if (!intro) sumar(tg, 0.35, (t) => Math.sin(2 * Math.PI * (50 + 90 * Math.exp(-t * 30)) * t) * Math.exp(-t * 9) * 0.55);
    // Palmas en 2 y 4
    if (!intro && g % 2 === 1) sumar(tg, 0.2, (t) => ruido() * Math.exp(-t * 22) * 0.18, 0.1);
    // Hi-hats en corcheas
    for (const k of [0, 0.5]) sumar(tg + k * negra, 0.05, (t) => ruido() * Math.exp(-t * 90) * (k ? 0.07 : 0.045), -0.3);
    // Bajo en corcheas, raíz y octava
    for (const k of [0, 0.5]) {
      const f = frec(ac.bajo + (k ? 12 : 0));
      sumar(tg + k * negra, negra * 0.45, (t) => {
        const env = Math.min(1, t * 200) * Math.exp(-t * 5);
        return (Math.sin(2 * Math.PI * f * t) + 0.3 * Math.sin(4 * Math.PI * f * t)) * env * 0.2;
      });
    }
  }

  // Acordes en síncopa (estilo "pluck")
  for (const pos of [0, 1.5, 2.5, 3.5]) {
    ac.notas.forEach((m, i) => {
      const f = frec(m);
      sumar(t0 + pos * negra, negra * 0.9, (t) => {
        const env = Math.min(1, t * 120) * Math.exp(-t * 4.5);
        const saw = 2 * ((f * t) % 1) - 1;
        return (0.6 * Math.sin(2 * Math.PI * f * t) + 0.25 * saw) * env * 0.05;
      }, i % 2 ? 0.35 : -0.35);
    });
  }

  // Melodía simple arriba, solo en la parte central
  if (!intro && !final && c % 2 === 0) {
    [0, 2, 4, 7].forEach((paso, j) => {
      const f = frec(ac.notas[j % 4] + 12);
      sumar(t0 + paso * (negra / 2), negra * 0.4, (t) => Math.sin(2 * Math.PI * f * t) * Math.exp(-t * 7) * 0.06, 0.2);
    });
  }
}

// Entrada y salida suaves + normalización
let pico = 0;
for (let i = 0; i < n; i++) {
  const t = i / SR;
  const fade = Math.min(1, t / 1.5, (DURACION - t) / 2.5);
  L[i] *= fade;
  R[i] *= fade;
  pico = Math.max(pico, Math.abs(L[i]), Math.abs(R[i]));
}
const k = 0.89 / (pico || 1);

const buf = Buffer.alloc(44 + n * 4);
buf.write('RIFF', 0);
buf.writeUInt32LE(36 + n * 4, 4);
buf.write('WAVEfmt ', 8);
buf.writeUInt32LE(16, 16);
buf.writeUInt16LE(1, 20);
buf.writeUInt16LE(2, 22);
buf.writeUInt32LE(SR, 24);
buf.writeUInt32LE(SR * 4, 28);
buf.writeUInt16LE(4, 32);
buf.writeUInt16LE(16, 34);
buf.write('data', 36);
buf.writeUInt32LE(n * 4, 40);
for (let i = 0; i < n; i++) {
  buf.writeInt16LE(Math.round(Math.max(-1, Math.min(1, L[i] * k)) * 32767), 44 + i * 4);
  buf.writeInt16LE(Math.round(Math.max(-1, Math.min(1, R[i] * k)) * 32767), 46 + i * 4);
}
mkdirSync(join(raiz, 'public/musica'), { recursive: true });
writeFileSync(join(raiz, 'public/musica/fondo.wav'), buf);
console.log(`Listo: public/musica/fondo.wav (${DURACION} s)`);
