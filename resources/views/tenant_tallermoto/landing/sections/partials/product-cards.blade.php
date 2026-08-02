@foreach ($dataProductos as $producto)
    <div data-prod-category="{{ $producto->CAT_Id }}"
         data-title="{{ $producto->PRO_Nombre }}"
         data-descripcion="{{ $producto->PRO_Descripcion ?? 'Sin descripción' }}"
         data-brand="{{ $producto->PRO_Marca ?? 'Sin marca' }}"
         data-price="S/. {{ number_format($producto->PRO_PrecioVenta, 2) }}"
         data-stock="{{ $producto->cantidad_total > 0 ? 'En Stock' : 'Agotado' }}"
         data-url="{{ asset_root('/storage/' . $tiponegocio . '/' . $tenantid . '/archivos/producto/' . $producto->PRO_Imagen) }}"
         data-has-image="{{ !empty($producto->PRO_Imagen) ? 'true' : 'false' }}"
         data-specs="{{ json_encode($producto->especificaciones ?? []) }}"
         class="product-card backdrop-blur-md rounded-2xl border p-4 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 group cursor-pointer {{ $colorview == 'dark' ? 'bg-slate-950/40 border-white/5 hover:border-brand-500/30' : 'bg-white border-slate-200 hover:border-red-500/30 shadow-sm' }}">

        <div class="space-y-3">
            <div class="w-full h-44 rounded-xl border flex items-center justify-center relative overflow-hidden bg-gradient-to-b {{ $colorview == 'dark' ? 'from-slate-900 to-slate-950 border-white/5' : 'from-slate-50 to-slate-100/50 border-slate-200' }}">
                
                <!-- Badge de Stock acumulado -->
                <div class="absolute top-2.5 right-2.5 z-10 {{ $producto->cantidad_total > 0 ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500' : 'bg-red-500/10 border-red-500/30 text-red-500' }} border text-[9px] font-black uppercase px-2 py-0.5 rounded-md">
                    {{ $producto->cantidad_total > 0 ? 'En Stock' : 'Agotado' }}
                </div>

                <!-- Imagen del producto o Ícono por defecto -->
                @if (!empty($producto->PRO_Imagen))
                    <img src="{{ asset_root('/storage/' . $tiponegocio . '/' . $tenantid . '/archivos/producto/' . $producto->PRO_Imagen) }}"
                         alt="{{ $producto->PRO_Nombre }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <i data-lucide="package"
                       class="w-12 h-12 {{ $colorview == 'dark' ? 'text-brand-500/20' : 'text-red-500/20' }} group-hover:scale-105 transition-transform duration-500"></i>
                @endif
            </div>

            <div>
                <span class="text-[9px] {{ $colorview == 'dark' ? 'text-gray-600' : 'text-slate-400' }} font-bold uppercase font-mono">
                    {{ $producto->CAT_Nombre }}
                </span>
                <h3 class="text-xs font-black uppercase {{ $colorview == 'dark' ? 'text-gray-400 group-hover:text-brand-400' : 'text-slate-700 group-hover:text-red-500' }} mt-0.5">
                    {{ $producto->PRO_Nombre }}
                </h3>
            </div>
        </div>

        <div class="pt-4 border-t {{ $colorview == 'dark' ? 'border-white/5' : 'border-slate-100' }} mt-4 flex items-center justify-between">
            <span class="text-sm font-black {{ $colorview == 'dark' ? 'text-gray-400' : 'text-slate-800' }} font-mono">
                S/. {{ number_format($producto->PRO_PrecioVenta, 2) }}
            </span>
            <span class="text-[10px] {{ $colorview == 'dark' ? 'text-brand-500' : 'text-red-500' }} font-bold flex items-center gap-1 group-hover:underline">
                Detalles <i data-lucide="eye" class="w-3 h-3"></i>
            </span>
        </div>
    </div>
@endforeach