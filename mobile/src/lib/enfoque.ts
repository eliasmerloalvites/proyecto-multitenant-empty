import { useFocusEffect } from 'expo-router';
import { useCallback, useRef } from 'react';

/**
 * Vuelve a pedir los datos cada vez que la pantalla gana el foco (cambiar de
 * pestaña o volver atrás). Las pestañas siguen montadas, así que sin esto
 * mostrarían datos viejos hasta que el usuario deslice para refrescar.
 */
export function useRefrescarAlEnfocar(refetch: () => unknown) {
  const primera = useRef(true);
  useFocusEffect(
    useCallback(() => {
      if (primera.current) {
        primera.current = false; // la primera carga ya la hace la consulta
        return;
      }
      void refetch();
    }, [refetch]),
  );
}
