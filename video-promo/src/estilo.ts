import { loadFont } from '@remotion/google-fonts/Poppins';

export const { fontFamily } = loadFont('normal', { weights: ['400', '500', '600', '700', '800'], subsets: ['latin', 'latin-ext'] });

/** Paleta del video de Kael Tech DataValle. */
export const C = {
  azul: '#3461f0',
  azulClaro: '#e8eefe',
  morado: '#7c4dff',
  verde: '#1f9d55',
  ambar: '#f59e0b',
  rojo: '#ef4444',
  tinta: '#0f172a',
  suave: '#64748b',
  borde: '#e2e8f0',
  claro: '#f4f6fa',
  oscuro: '#0a0f1f',
  blanco: '#ffffff',
};

export const FPS = 30;
/** Pausa al final de cada escena, después de la última palabra (segundos). */
export const RESPIRO = 0.45;

export const sombra = '0 24px 60px rgba(15, 23, 42, 0.12), 0 4px 14px rgba(15, 23, 42, 0.06)';
