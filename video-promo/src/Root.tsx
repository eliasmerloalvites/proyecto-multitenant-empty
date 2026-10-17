import { Composition } from 'remotion';

import { FPS } from './estilo';
import guionApp from './guion.json';
import guionBahia from './guion-bahia.json';
import guionInv from './guion-inventario.json';
import guionMto from './guion-mantenimiento.json';
import guionAvatar from './guion-avatar.json';
import guionCorto from './guion-corto.json';
import guionWeb from './guion-web.json';
import { duracionTotal, Promo, type Escena, type Voz } from './Promo';
import vozApp from './voz.json';
import vozBahia from './voz-bahia.json';
import vozInv from './voz-inventario.json';
import vozMto from './voz-mantenimiento.json';
import { duracionAvatar, PromoAvatar, type EscenaAvatar } from './PromoAvatar';
import vozAvatar from './voz-avatar.json';
import vozCorto from './voz-corto.json';
import vozWeb from './voz-web.json';

const app = { escenas: guionApp.escenas as Escena[], voz: vozApp as Voz[], video: 'app' as const };
const web = { escenas: guionWeb.escenas as Escena[], voz: vozWeb as Voz[], video: 'web' as const };

const bahia = { escenas: guionBahia.escenas as Escena[], voz: vozBahia as Voz[], video: 'bahia' as const };
const mto = { escenas: guionMto.escenas as Escena[], voz: vozMto as Voz[], video: 'mantenimiento' as const };
const inv = { escenas: guionInv.escenas as Escena[], voz: vozInv as Voz[], video: 'inventario' as const };
const corto = { escenas: guionCorto.escenas as Escena[], voz: vozCorto as Voz[], video: 'web' as const };

const avatar = { escenas: guionAvatar.escenas as EscenaAvatar[], voz: vozAvatar as Voz[] };

export const Root: React.FC = () => (
  <>
    {/* Sistema web para talleres (lo que ya está en producción). */}
    <Composition id="PromoWeb" component={Promo} defaultProps={web} durationInFrames={duracionTotal(web.escenas, web.voz)} fps={FPS} width={1080} height={1920} />
    {/* App móvil para clientes (lanzamiento posterior). */}
    <Composition id="PromoTaller" component={Promo} defaultProps={app} durationInFrames={duracionTotal(app.escenas, app.voz)} fps={FPS} width={1080} height={1920} />
    {/* Flujo de facturación electrónica por bahía. */}
    <Composition id="PromoBahia" component={Promo} defaultProps={bahia} durationInFrames={duracionTotal(bahia.escenas, bahia.voz)} fps={FPS} width={1080} height={1920} />
    {/* Módulo de mantenimientos. */}
    <Composition id="PromoMantenimiento" component={Promo} defaultProps={mto} durationInFrames={duracionTotal(mto.escenas, mto.voz)} fps={FPS} width={1080} height={1920} />
    {/* Módulo de inventario. */}
    <Composition id="PromoInventario" component={Promo} defaultProps={inv} durationInFrames={duracionTotal(inv.escenas, inv.voz)} fps={FPS} width={1080} height={1920} />
    {/* Versión corta (~28 s) con gancho, pensada para conseguir seguidores. */}
    <Composition id="PromoCorto" component={Promo} defaultProps={corto} durationInFrames={duracionTotal(corto.escenas, corto.voz)} fps={FPS} width={1080} height={1920} />
    {/* Mecánico animado que habla y cuenta los problemas de los talleres. */}
    <Composition id="PromoAvatar" component={PromoAvatar} defaultProps={avatar} durationInFrames={duracionAvatar(avatar.escenas, avatar.voz)} fps={FPS} width={1080} height={1920} />
  </>
);
