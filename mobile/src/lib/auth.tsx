import { useQueryClient } from '@tanstack/react-query';
import { createContext, useCallback, useContext, useEffect, useMemo, useState, type ReactNode } from 'react';

import { api } from './api';
import { sesion } from './session';
import type { TallerGuardado } from './types';

type Estado = {
  listo: boolean;
  taller: TallerGuardado | null;
  conSesion: boolean;
  elegirTaller: (t: TallerGuardado) => Promise<void>;
  cambiarTaller: () => Promise<void>;
  iniciarSesion: (token: string) => Promise<void>;
  cerrarSesion: (avisarAlServidor?: boolean) => Promise<void>;
};

const Ctx = createContext<Estado | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const cache = useQueryClient();
  const [listo, setListo] = useState(false);
  const [taller, setTaller] = useState<TallerGuardado | null>(null);
  const [conSesion, setConSesion] = useState(false);

  const cerrarSesion = useCallback(
    async (avisarAlServidor = false) => {
      if (avisarAlServidor && sesion.token) {
        await api.post('auth/logout').catch(() => {});
      }
      await sesion.guardarToken(null);
      cache.clear();
      setConSesion(false);
    },
    [cache],
  );

  useEffect(() => {
    sesion.alExpirar = () => {
      void cerrarSesion(false);
    };
    sesion.cargar().then(() => {
      setTaller(sesion.taller);
      setConSesion(!!sesion.token);
      setListo(true);
    });
  }, [cerrarSesion]);

  const valor = useMemo<Estado>(
    () => ({
      listo,
      taller,
      conSesion,
      elegirTaller: async (t) => {
        // Cambiar de taller invalida la sesión: el token es de otro taller.
        if (sesion.taller && sesion.taller.codigo !== t.codigo) await cerrarSesion(false);
        await sesion.guardarTaller(t);
        setTaller(t);
      },
      cambiarTaller: async () => {
        await cerrarSesion(true);
        await sesion.guardarTaller(null);
        setTaller(null);
      },
      iniciarSesion: async (token) => {
        await sesion.guardarToken(token);
        setConSesion(true);
      },
      cerrarSesion,
    }),
    [listo, taller, conSesion, cerrarSesion],
  );

  return <Ctx.Provider value={valor}>{children}</Ctx.Provider>;
}

export function useAuth() {
  const v = useContext(Ctx);
  if (!v) throw new Error('useAuth fuera de AuthProvider');
  return v;
}
