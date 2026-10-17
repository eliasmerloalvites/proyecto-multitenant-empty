// Captura pantallas reales del sistema del taller (entorno LOCAL) para el
// video. Inicia sesión con el usuario de prueba de .env.capturas y guarda cada
// módulo en public/capturas/<nombre>.png.
//
// Uso: node scripts/capturar.mjs          → pantallas del video general
//      node scripts/capturar.mjs bahia    → flujo de facturación por bahía
//      node scripts/capturar.mjs mantenimiento → módulo de mantenimientos (por secciones)
//      node scripts/capturar.mjs inventario    → módulo de inventario
import { existsSync, mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import puppeteer from 'puppeteer-core';

const raiz = join(dirname(fileURLToPath(import.meta.url)), '..');
const env = Object.fromEntries(
  readFileSync(join(raiz, '.env.capturas'), 'utf8')
    .split(/\r?\n/)
    .filter((l) => l && !l.startsWith('#'))
    .map((l) => [l.slice(0, l.indexOf('=')), l.slice(l.indexOf('=') + 1)]),
);
const BASE = env.CAPTURAS_URL;
if (!/^http:\/\/[a-z0-9.-]+\.localhost(:\d+)?$/.test(BASE)) throw new Error('Solo se capturan entornos locales (*.localhost).');

const GENERAL = [
  { nombre: 'inicio', ruta: '/tenant/home' },
  { nombre: 'procesos', ruta: '/tenant/procesos?fecha=2026-07-28' },
  { nombre: 'mantenimiento', ruta: '/tenant/mantenimientos/generalinyectada/8/edit' },
  { nombre: 'mantenimientos', ruta: '/tenant/mantenimientos/generalinyectada' },
  { nombre: 'reservaciones', ruta: '/tenant/reservaciones/administracion' },
  { nombre: 'notificaciones', ruta: '/tenant/reservaciones/notificaciones' },
  { nombre: 'venta', ruta: '/tenant/ventas/venta/create' },
  { nombre: 'ventas', ruta: '/tenant/ventas/venta' },
  { nombre: 'inventario', ruta: '/tenant/inventario/controlinventario' },
  { nombre: 'traslado', ruta: '/tenant/inventario/traslado' },
  { nombre: 'caja', ruta: '/tenant/ventas/caja/historial' },
  { nombre: 'rentabilidad', ruta: '/tenant/reportes/rentabilidad' },
  { nombre: 'mecanicos', ruta: '/tenant/reportes/rendimientomecanicos' },
  { nombre: 'web', ruta: '/', publica: true },
  { nombre: 'web-reservar', ruta: '/reservar', publica: true },
];

// Flujo "Ventas por Bahía": tablero con cuentas abiertas y el punto de venta
// precargado desde una cuenta (cuentas 22 y 23 del tenant local).
const BAHIA = [
  { nombre: 'bahia-tablero', ruta: '/tenant/ventas/bahias?fecha=2026-09-17' },
  { nombre: 'bahia-tablero-2', ruta: '/tenant/ventas/bahias?fecha=2026-09-16' },
  { nombre: 'bahia-cobro', ruta: '/tenant/ventas/venta/create?cuenta_bahia=23' },
  { nombre: 'bahia-cobro-2', ruta: '/tenant/ventas/venta/create?cuenta_bahia=22' },
];

// Módulo de mantenimientos. Las entradas con `seccion` capturan solo la tarjeta
// del formulario cuyo título contiene ese texto (MGI-1 tiene recepción y fotos;
// MGI-3 tiene repuestos cargados).
const EDIT = '/tenant/mantenimientos/generalinyectada';
const MANTENIMIENTO = [
  { nombre: 'mto-lista', ruta: EDIT },
  { nombre: 'mto-unidad', ruta: `${EDIT}/1/edit`, seccion: 'Datos de la Unidad', conVecina: true },
  { nombre: 'mto-recepcion', ruta: `${EDIT}/1/edit`, seccion: 'Estado de Recepción de la Motocicleta', expandir: '#estadoRecepcionCard .card-header', esperarEn: '#estadoRecepcionBody' },
  { nombre: 'mto-servicio', ruta: `${EDIT}/1/edit`, seccion: 'Descripción del Servicio' },
  { nombre: 'mto-repuestos', ruta: `${EDIT}/3/edit`, seccion: 'Repuestos a Reemplazar' },
  { nombre: 'mto-fotos', ruta: `${EDIT}/1/edit`, seccion: 'Galería de Evidencias' },
  { nombre: 'mto-verificacion', ruta: `${EDIT}/1/edit`, seccion: 'Recomendaciones y Verificación Final' },
  { nombre: 'mto-planes', ruta: '/tenant/mantenimientos/planes' },
  { nombre: 'mto-config-recepcion', ruta: '/tenant/configuracion/recepcion' },
];

// Módulo de inventario (producto 2 = Capuchón de bujía, con varios lotes).
const INVENTARIO = [
  { nombre: 'inv-productos', ruta: '/tenant/inventario/producto' },
  { nombre: 'inv-control', ruta: '/tenant/inventario/controlinventario' },
  { nombre: 'inv-detalle', ruta: '/tenant/inventario/controlinventario', abrir: { clase: 'eyeProducto', id: 2, modal: '#modalVerDetalle' } },
  { nombre: 'inv-kardex', ruta: '/tenant/inventario/controlinventario', abrir: { clase: 'kardexProducto', id: 2, modal: '#modalKardex' } },
  { nombre: 'inv-ajuste', ruta: '/tenant/inventario/ajuste' },
  { nombre: 'inv-traslado', ruta: '/tenant/inventario/traslado' },
  { nombre: 'inv-valorizado', ruta: '/tenant/reportes/inventario' },
];

const CONJUNTOS = { bahia: BAHIA, mantenimiento: MANTENIMIENTO, inventario: INVENTARIO };
const PAGINAS = CONJUNTOS[process.argv[2]] ?? GENERAL;

/**
 * El video es público: se difuminan nombres, celulares y documentos de
 * clientes (columnas de tablas y campos de formularios) antes de capturar.
 */
function difuminarDatosPersonales() {
  const sensibles = /cliente|celular|propietario|tel[eé]fono|documento|usuario|responsable|mec[aá]nico|nombre del cliente/i;
  for (const tabla of document.querySelectorAll('table')) {
    const cabeceras = [...tabla.querySelectorAll('thead th')];
    cabeceras.forEach((th, i) => {
      if (!sensibles.test(th.textContent ?? '')) return;
      for (const fila of tabla.querySelectorAll('tbody tr')) {
        const celda = fila.children[i];
        if (celda) celda.style.filter = 'blur(6px)';
      }
    });
  }
  for (const campo of document.querySelectorAll('input, textarea')) {
    const id = (campo.name || campo.id || '') + ' ' + (campo.closest('.form-group, div')?.querySelector('label')?.textContent ?? '');
    if (/propietario|celular|cliente|telefono|documento/i.test(id)) campo.style.filter = 'blur(5px)';
  }
  document.querySelectorAll('.proc-cliente, .res-cliente, #clientName, #clientSubtitle').forEach((e) => (e.style.filter = 'blur(6px)'));
  // Franja "Cobrando cuenta de bahía — <cliente>" del punto de venta.
  for (const fuerte of document.querySelectorAll('strong')) {
    if (/Cobrando cuenta de bah/.test(fuerte.parentElement?.textContent ?? '')) fuerte.style.filter = 'blur(7px)';
  }
  // Nombre del usuario conectado en la barra superior.
  document.querySelectorAll('.navbar .dropdown-toggle, .user-panel .info').forEach((e) => (e.style.filter = 'blur(5px)'));
}

const destino = join(raiz, 'public/capturas');
mkdirSync(destino, { recursive: true });

// Medidas de cada captura (el video las usa para encuadrar sin deformar).
const archivoMedidas = join(raiz, 'src/capturas.json');
const medidas = existsSync(archivoMedidas) ? JSON.parse(readFileSync(archivoMedidas, 'utf8')) : {};
const tamañoPng = (ruta) => {
  const b = readFileSync(ruta);
  return { w: b.readUInt32BE(16), h: b.readUInt32BE(20) };
};

const navegador = await puppeteer.launch({
  executablePath: join(raiz, 'node_modules/.remotion/chrome-headless-shell/win64/chrome-headless-shell-win64/chrome-headless-shell.exe'),
  headless: true,
  defaultViewport: { width: 1440, height: 900, deviceScaleFactor: 1.5 },
});

const pagina = await navegador.newPage();
const esperar = (ms) => new Promise((r) => setTimeout(r, ms));

// Las páginas públicas primero, sin sesión.
for (const p of PAGINAS.filter((x) => x.publica)) {
  await pagina.goto(BASE + p.ruta, { waitUntil: 'networkidle2', timeout: 60000 });
  await esperar(1500);
  await pagina.screenshot({ path: join(destino, `${p.nombre}.png`) });
  medidas[p.nombre] = tamañoPng(join(destino, `${p.nombre}.png`));
  console.log(`✓ ${p.nombre}`);
}

await pagina.goto(`${BASE}/tenant/login`, { waitUntil: 'networkidle2' });
// El login del sistema es por AJAX (responde JSON): se envía igual que lo hace la página.
const login = await pagina.evaluate(async (email, password) => {
  const datos = new FormData();
  datos.append('email', email);
  datos.append('password', password);
  datos.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? document.querySelector('input[name="_token"]')?.value ?? '');
  const r = await fetch(window.location.href, { method: 'POST', body: datos, headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
  return { status: r.status, json: await r.json().catch(() => null) };
}, env.CAPTURAS_EMAIL, env.CAPTURAS_PASSWORD);
if (login.status !== 200) throw new Error(`No se pudo iniciar sesión (${login.status}): ${JSON.stringify(login.json)}`);

for (const p of PAGINAS.filter((x) => !x.publica)) {
  try {
    await pagina.goto(BASE + p.ruta, { waitUntil: 'networkidle2', timeout: 60000 });
    await esperar(2000); // tablas y gráficos que cargan por AJAX
    // Cierra avisos emergentes (SweetAlert / modales) para que no tapen la pantalla.
    await pagina.evaluate(() => document.querySelectorAll('.swal2-container, .modal-backdrop').forEach((e) => e.remove()));
    await pagina.evaluate(difuminarDatosPersonales);
    await esperar(300);
    const archivo = join(destino, `${p.nombre}.png`);
    if (p.abrir) {
      // Abre la ventana emergente del producto como si se pulsara su botón en la tabla.
      await pagina.evaluate(({ clase, id }) => {
        window.jQuery(`<a class="${clase}" data-id="${id}"></a>`).appendTo('body').trigger('click');
      }, p.abrir);
      await esperar(3000);
      await pagina.evaluate(difuminarDatosPersonales);
      await esperar(300);
      const ventana = await pagina.$(`${p.abrir.modal} .modal-content`);
      await ventana.screenshot({ path: archivo });
    } else if (p.seccion) {
      // Sin menú lateral, para que la tarjeta no quede tapada ni cortada.
      await pagina.evaluate(() => {
        document.body.classList.add('sidebar-collapse');
        document.querySelector('.main-sidebar')?.style.setProperty('display', 'none');
        document.querySelector('.content-wrapper')?.style.setProperty('margin-left', '0', 'important');
      });
      await esperar(600);
      if (p.expandir) {
        await pagina.click(p.expandir);
        await pagina.waitForFunction((sel) => !/Cargando/.test(document.querySelector(sel)?.textContent ?? 'Cargando'), { timeout: 20000 }, p.esperarEn).catch(() => {});
        await esperar(1200);
        await pagina.evaluate(difuminarDatosPersonales);
      }
      const tarjeta = await pagina.evaluateHandle((texto, conVecina) => {
        const h = [...document.querySelectorAll('h3')].find((x) => x.textContent.includes(texto));
        const card = h?.closest('.card');
        // "Datos de la Unidad" va al lado del detalle: se captura la fila completa.
        return conVecina ? card?.closest('.row') ?? card : card;
      }, p.seccion, !!p.conVecina);
      await tarjeta.evaluate((el) => el.scrollIntoView({ block: 'center' }));
      await esperar(400);
      await tarjeta.asElement().screenshot({ path: archivo });
    } else {
      await pagina.screenshot({ path: archivo });
    }
    medidas[p.nombre] = tamañoPng(archivo);
    console.log(`✓ ${p.nombre} (${pagina.url().replace(BASE, '')}) ${medidas[p.nombre].w}×${medidas[p.nombre].h}`);
  } catch (e) {
    console.log(`✗ ${p.nombre}: ${e.message}`);
  }
}

await navegador.close();
writeFileSync(archivoMedidas, JSON.stringify(medidas, null, 2));
