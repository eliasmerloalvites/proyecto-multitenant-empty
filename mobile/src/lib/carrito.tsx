import * as SecureStore from 'expo-secure-store';
import { createContext, useContext, useEffect, useMemo, useRef, useState, type ReactNode } from 'react';
import { Platform } from 'react-native';

/**
 * Carrito de la tienda. Solo guarda producto y cantidad (y la sede elegida):
 * el nombre, el precio y la disponibilidad se leen siempre del servidor, y el
 * precio final lo fija el taller al crear el pedido. Se guarda por taller,
 * asi cambiar de taller no mezcla carritos.
 */
export type LineaCarrito = { id: number; cantidad: number };
type Datos = { lineas: LineaCarrito[]; sede: number | null };

type Estado = Datos & {
  unidades: number;
  agregar: (id: number, cantidad: number) => void;
  cambiar: (id: number, cantidad: number) => void;
  quitar: (id: number) => void;
  vaciar: () => void;
  elegirSede: (sede: number) => void;
};

const Ctx = createContext<Estado | null>(null);
const web = Platform.OS === 'web';
export const MAX_POR_PRODUCTO = 20;

const claveDe = (taller: string | null) => `kt.carrito.${(taller ?? 'sin-taller').replace(/[^A-Za-z0-9]/g, '')}`;

async function leer(clave: string): Promise<Datos> {
  try {
    const crudo = web ? globalThis.sessionStorage?.getItem(clave) : await SecureStore.getItemAsync(clave);
    const d = crudo ? JSON.parse(crudo) : null;
    return {
      lineas: Array.isArray(d?.l) ? d.l.map(([id, cantidad]: [number, number]) => ({ id, cantidad })) : [],
      sede: d?.s ?? null,
    };
  } catch {
    return { lineas: [], sede: null };
  }
}

async function guardar(clave: string, d: Datos) {
  // Formato compacto: el almacen seguro de Android admite valores pequenos.
  const valor = JSON.stringify({ l: d.lineas.map((l) => [l.id, l.cantidad]), s: d.sede });
  try {
    if (web) globalThis.sessionStorage?.setItem(clave, valor);
    else await SecureStore.setItemAsync(clave, valor);
  } catch {
    /* sin almacenamiento: el carrito vive solo en memoria */
  }
}

export function CarritoProvider({ children, tallerCodigo }: { children: ReactNode; tallerCodigo: string | null }) {
  const [datos, setDatos] = useState<Datos>({ lineas: [], sede: null });
  const cargado = useRef(false);
  const clave = claveDe(tallerCodigo);

  useEffect(() => {
    cargado.current = false;
    leer(clave).then((d) => {
      setDatos(d);
      cargado.current = true;
    });
  }, [clave]);

  useEffect(() => {
    if (cargado.current) void guardar(clave, datos);
  }, [clave, datos]);

  const valor = useMemo<Estado>(() => {
    const tope = (n: number) => Math.max(0, Math.min(MAX_POR_PRODUCTO, n));
    return {
      ...datos,
      unidades: datos.lineas.reduce((s, l) => s + l.cantidad, 0),
      agregar: (id, cantidad) =>
        setDatos((d) => {
          const existe = d.lineas.find((l) => l.id === id);
          return {
            ...d,
            lineas: existe
              ? d.lineas.map((l) => (l.id === id ? { ...l, cantidad: tope(l.cantidad + cantidad) } : l))
              : [...d.lineas, { id, cantidad: tope(cantidad) }],
          };
        }),
      cambiar: (id, cantidad) =>
        setDatos((d) => ({
          ...d,
          lineas: cantidad <= 0 ? d.lineas.filter((l) => l.id !== id) : d.lineas.map((l) => (l.id === id ? { ...l, cantidad: tope(cantidad) } : l)),
        })),
      quitar: (id) => setDatos((d) => ({ ...d, lineas: d.lineas.filter((l) => l.id !== id) })),
      vaciar: () => setDatos((d) => ({ ...d, lineas: [] })),
      elegirSede: (sede) => setDatos((d) => ({ ...d, sede })),
    };
  }, [datos]);

  return <Ctx.Provider value={valor}>{children}</Ctx.Provider>;
}

export function useCarrito() {
  const v = useContext(Ctx);
  if (!v) throw new Error('useCarrito fuera de CarritoProvider');
  return v;
}
