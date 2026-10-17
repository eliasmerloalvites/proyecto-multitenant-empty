import { File, Paths } from 'expo-file-system';
import { useEffect, useState } from 'react';
import { ActivityIndicator, Image, View, type ImageStyle, type StyleProp } from 'react-native';

import { sesion } from '@/lib/session';

/**
 * Foto que solo se sirve con sesión. En vez de dejar que el cargador de
 * imágenes del sistema pida la URL, se descarga con el token a la caché y se
 * muestra el archivo local; así funciona igual en Android e iOS.
 */
export function FotoSegura({
  url,
  clave,
  style,
  resizeMode,
  alFallar,
}: {
  url: string;
  clave: string;
  style: StyleProp<ImageStyle>;
  resizeMode?: 'cover' | 'contain';
  alFallar?: () => void;
}) {
  const [uri, setUri] = useState<string | null>(null);

  useEffect(() => {
    let vigente = true;
    setUri(null);
    (async () => {
      try {
        const destino = new File(Paths.cache, `foto-${clave}.webp`);
        const archivo = await File.downloadFileAsync(url, destino, {
          headers: { Authorization: `Bearer ${sesion.token}` },
          idempotent: true,
        });
        if (vigente) setUri(archivo.uri);
      } catch {
        if (vigente) alFallar?.();
      }
    })();
    return () => {
      vigente = false;
    };
  }, [url, clave]); // eslint-disable-line react-hooks/exhaustive-deps

  if (!uri) {
    return (
      <View style={[style as object, { backgroundColor: '#e5e7eb', alignItems: 'center', justifyContent: 'center' }]}>
        <ActivityIndicator />
      </View>
    );
  }

  return <Image source={{ uri }} style={style} resizeMode={resizeMode} />;
}
