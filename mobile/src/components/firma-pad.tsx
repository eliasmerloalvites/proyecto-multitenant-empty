import { useRef, useState } from 'react';
import { PanResponder, View, type LayoutChangeEvent } from 'react-native';

import { Texto } from '@/components/ui';
import { useTema } from '@/lib/tema';

/** Trazos normalizados de 0 a 1: [[[x, y], ...], ...]. El servidor dibuja el PNG. */
export type Trazos = [number, number][][];

type Segmento = { x1: number; y1: number; x2: number; y2: number };

const GROSOR = 3;
const DISTANCIA_MIN = 2;

/**
 * Recuadro para firmar con el dedo. Dibuja con Views de React Native (sin
 * librerias nativas) y entrega los trazos normalizados al tamano del
 * recuadro, para que se vean igual en cualquier pantalla. Para borrar, el
 * padre lo vuelve a montar con otra key.
 */
export function FirmaPad({ trazos, onCambio, alto = 220 }: { trazos: Trazos; onCambio: (t: Trazos) => void; alto?: number }) {
  const t = useTema();
  const [tam, setTam] = useState({ ancho: 1, alto });
  const [segmentos, setSegmentos] = useState<Segmento[]>([]);
  const ultimo = useRef<{ x: number; y: number } | null>(null);
  const actual = useRef<Trazos>(trazos);
  actual.current = trazos;
  const tamRef = useRef(tam);
  tamRef.current = tam;

  const dentro = (v: number, max: number) => Math.max(0, Math.min(max, v));

  const responder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => true,
      onMoveShouldSetPanResponder: () => true,
      onPanResponderTerminationRequest: () => false,
      onPanResponderGrant: (e) => {
        const x = e.nativeEvent.locationX;
        const y = e.nativeEvent.locationY;
        ultimo.current = { x, y };
        onCambio([...actual.current, [[x / tamRef.current.ancho, y / tamRef.current.alto]]]);
        setSegmentos((s) => [...s, { x1: x, y1: y, x2: x + 0.5, y2: y + 0.5 }]);
      },
      onPanResponderMove: (e) => {
        const prev = ultimo.current;
        if (!prev) return;
        const x = dentro(e.nativeEvent.locationX, tamRef.current.ancho);
        const y = dentro(e.nativeEvent.locationY, tamRef.current.alto);
        if (Math.hypot(x - prev.x, y - prev.y) < DISTANCIA_MIN) return;
        ultimo.current = { x, y };
        const copia = actual.current.slice();
        copia[copia.length - 1] = [...copia[copia.length - 1], [x / tamRef.current.ancho, y / tamRef.current.alto]];
        onCambio(copia);
        setSegmentos((s) => [...s, { x1: prev.x, y1: prev.y, x2: x, y2: y }]);
      },
      onPanResponderRelease: () => {
        ultimo.current = null;
      },
    }),
  ).current;

  return (
    <View
      onLayout={(e: LayoutChangeEvent) => setTam({ ancho: e.nativeEvent.layout.width, alto: e.nativeEvent.layout.height })}
      {...responder.panHandlers}
      accessibilityLabel="Recuadro para firmar"
      style={{ height: alto, borderRadius: 14, borderWidth: 2, borderStyle: 'dashed', borderColor: t.borde, backgroundColor: '#fff', overflow: 'hidden' }}>
      {/* Guia de la linea de firma */}
      <View pointerEvents="none" style={{ position: 'absolute', left: '8%', right: '8%', bottom: 42, borderBottomWidth: 1, borderColor: '#cbd5e1' }} />
      {segmentos.length === 0 ? (
        <View pointerEvents="none" style={{ position: 'absolute', left: 0, right: 0, top: 0, bottom: 0, alignItems: 'center', justifyContent: 'center' }}>
          <Texto tipo="pequeno">Firma aquí con tu dedo</Texto>
        </View>
      ) : null}
      {segmentos.map((s, i) => {
        const largo = Math.max(Math.hypot(s.x2 - s.x1, s.y2 - s.y1), 0.5);
        const angulo = Math.atan2(s.y2 - s.y1, s.x2 - s.x1);
        return (
          <View
            key={i}
            pointerEvents="none"
            style={{
              position: 'absolute',
              left: (s.x1 + s.x2) / 2 - largo / 2,
              top: (s.y1 + s.y2) / 2 - GROSOR / 2,
              width: largo + GROSOR / 2,
              height: GROSOR,
              borderRadius: GROSOR / 2,
              backgroundColor: '#0b2a6b',
              transform: [{ rotate: `${angulo}rad` }],
            }}
          />
        );
      })}
    </View>
  );
}

/** Cantidad total de puntos (el servidor pide al menos 5). */
export const puntosDe = (t: Trazos) => t.reduce((n, trazo) => n + trazo.length, 0);
