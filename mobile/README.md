# KaelTech Moto (app móvil)

App para los clientes finales de los talleres de motos: React Native + Expo (SDK 57) + TypeScript, con Expo Router.
Consume la API REST `/api/v1` del backend Laravel (raíz del repositorio).

## Flujo

1. **Taller**: se escanea el QR del taller (o se escribe su código de 8 caracteres). El QR solo trae el código público; la app lo consulta en el directorio central (`GET /api/v1/talleres/{codigo}`) y recibe la dirección (`base_url`) y la marca del taller. La app nunca se conecta a una dirección que no venga de ese directorio.
2. **Ingreso**: celular + código de un solo uso (OTP). El token se guarda en el almacén seguro del sistema (`expo-secure-store`).
3. **Mi garaje**, **Órdenes** (historial y estado, fotos y PDF), **Reservas** y **Perfil**.

## Ejecutar

```bash
cd mobile
cp .env.example .env      # EXPO_PUBLIC_DIRECTORIO_URL = dominio central de KaelTech
npm install
npx expo start
```

Se prueba en el teléfono con Expo Go o con un *development build* (`npx expo run:android`). La cámara (QR) requiere un dispositivo real.

### Contra el backend local

- En el `.env` de Laravel: `APP_MOVIL_ESQUEMA=http`.
- El directorio devuelve `http://motoclub9.localhost`, que un teléfono o emulador no resuelve. Opciones: exponer el backend con un túnel (HTTPS) y registrar ese dominio en el taller, o usar la IP de la PC en la red local registrada como dominio del taller.
- Android bloquea HTTP sin cifrar en builds de producción; en local usa el development build.

## Verificación

```bash
npx tsc --noEmit        # tipos
npx expo-doctor         # dependencias y configuración
npx expo export --platform android   # el bundle compila
```

## Pendiente

- Notificaciones push (fase posterior).
- Canal real de OTP (WhatsApp/SMS) en el backend; hoy el código solo se escribe en el log en entornos locales.
- Íconos y splash propios de KaelTech (los actuales son los de la plantilla de Expo).
- Páginas `/t/{codigo}` en el dominio central para abrir la app o la tienda desde el QR.
