<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $empresa->nombre }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            400: 'rgba(var(--color-brand-light), 1)',
                            500: 'rgba(var(--color-brand-main), 1)',
                            950: 'var(--color-brand-card)',
                        },
                        // Si la web es clara, sobreescribimos slate para los fondos alternativos de los botones inactivos
                        @if($empresa->tipo_tema == 'light')
                        slate: {
                            900: '#f1f5f9', 
                            950: '#f8fafc', 
                        }
                        @endif
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            /* Inyección de colores desde el mantenedor único */
            --color-brand-main: {{ $empresa->hexToRgb($empresa->color_main) }};
            --color-brand-light: {{ $empresa->hexToRgb($empresa->color_light) }};
            --color-brand-card: {{ $empresa->color_card }};
            
            background-color: {{ $empresa->color_bg }};
        }

        body {
            background-color: {{ $empresa->color_bg }};
        }

        /* Paneles con Glassmorphism que se adaptan al color de tarjeta guardado */
        .glass-panel {
            background: {{ $empresa->color_card }};
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid @if($empresa->tipo_tema == 'dark') rgba(255, 255, 255, 0.05) @else rgba(0, 0, 0, 0.06) @endif;
        }

        .neon-border {
            border: 1px solid rgb(var(--color-brand-light) / @if($empresa->tipo_tema == 'dark') 0.3 @else 0.15 @endif);
        }

        /* Desactivar filtros oscuros del mapa si el tema elegido por la empresa es claro */
        @if($empresa->tipo_tema == 'light')
        .grayscale.invert {
            filter: none !important;
            opacity: 1 !important;
        }
        @endif
    </style>
</head>

<body @class([
    // Si eligió tema oscuro, textos claros. Si eligió claro, textos oscuros.
    'text-gray-300' => $empresa->tipo_tema == 'dark',
    'text-gray-700' => $empresa->tipo_tema == 'light',
    'font-sans antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col justify-between relative'
])>

    @yield('content')

    <script>
        // Compartimos la configuración del tema con JavaScript para que los scripts de las páginas (como el de las sedes) actúen bajo las mismas reglas
        window.themeConfig = {
            tipo_tema: "{{ $empresa->tipo_tema }}"
        };
        
        lucide.createIcons();
    </script>
</body>
</html>