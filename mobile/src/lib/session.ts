import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

import type { TallerGuardado } from './types';

/**
 * Sesión local: el taller elegido (dirección y marca pública) y el token de
 * la cuenta. El token va en el almacén seguro del sistema (Keychain/Keystore),
 * nunca en AsyncStorage ni en texto plano. En web (solo desarrollo) se usa
 * sessionStorage.
 */
const K_TALLER = 'kt.taller';
const K_TOKEN = 'kt.token';

const web = Platform.OS === 'web';

async function leer(clave: string): Promise<string | null> {
  try {
    return web ? (globalThis.sessionStorage?.getItem(clave) ?? null) : await SecureStore.getItemAsync(clave);
  } catch {
    return null;
  }
}

async function escribir(clave: string, valor: string | null): Promise<void> {
  try {
    if (web) {
      if (valor === null) globalThis.sessionStorage?.removeItem(clave);
      else globalThis.sessionStorage?.setItem(clave, valor);
    } else if (valor === null) {
      await SecureStore.deleteItemAsync(clave);
    } else {
      await SecureStore.setItemAsync(clave, valor);
    }
  } catch {
    /* sin almacenamiento disponible: la sesión vive solo en memoria */
  }
}

export const sesion = {
  taller: null as TallerGuardado | null,
  token: null as string | null,
  /** Lo invoca la app cuando el servidor responde 401 (token vencido o revocado). */
  alExpirar: (() => {}) as () => void,

  async cargar() {
    const t = await leer(K_TALLER);
    this.taller = t ? (JSON.parse(t) as TallerGuardado) : null;
    this.token = await leer(K_TOKEN);
  },
  async guardarTaller(taller: TallerGuardado | null) {
    this.taller = taller;
    await escribir(K_TALLER, taller ? JSON.stringify(taller) : null);
  },
  async guardarToken(token: string | null) {
    this.token = token;
    await escribir(K_TOKEN, token);
  },
};
