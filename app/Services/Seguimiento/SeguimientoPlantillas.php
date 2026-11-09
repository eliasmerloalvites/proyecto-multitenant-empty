<?php

namespace App\Services\Seguimiento;

/**
 * Textos de seguimiento. Variables: {negocio}, {dias} (dias que le quedan de
 * prueba), {fecha_fin}, {url} (acceso a su panel) y {asesor}.
 *
 * 'auto' = clave con la que el envio automatico se registra para no repetirse.
 */
class SeguimientoPlantillas
{
    public const PLANTILLAS = [
        'sin_uso' => [
            'nombre' => 'Aún no usa el sistema (ofrecer ayuda)',
            'asunto' => '¿Te ayudamos a empezar con {negocio}?',
            'cuerpo' => "Hola, equipo de {negocio}:\n\nVimos que creaste tu cuenta de prueba en Kael Tech pero todavía no la has usado. ¿Tuviste alguna duda o algo te frenó?\n\nPodemos ayudarte a dejarla lista en 10 minutos: cargar tus productos, registrar tu primer cliente y emitir tu primera venta o mantenimiento.\n\nResponde este correo o escríbenos por WhatsApp y te acompañamos.\n\n{asesor}",
        ],
        'poco_uso' => [
            'nombre' => 'Usó poco (animar a seguir)',
            'asunto' => 'Vas muy bien con {negocio}, ¿seguimos?',
            'cuerpo' => "Hola, equipo de {negocio}:\n\nVimos que ya empezaste a probar Kael Tech, ¡buen comienzo! Aún te quedan {dias} días de prueba.\n\nSi quieres, te mostramos en una llamada corta cómo sacarle más provecho (reportes, control de inventario y firmas digitales).\n\n{asesor}",
        ],
        'por_vencer' => [
            'nombre' => 'La prueba está por vencer',
            'asunto' => 'Tu prueba de Kael Tech termina el {fecha_fin}',
            'cuerpo' => "Hola, equipo de {negocio}:\n\nTu prueba gratuita termina el {fecha_fin} ({dias} días). Para no perder tu información y seguir trabajando sin interrupciones, puedes activar tu plan cuando quieras.\n\nSi necesitas más días o tienes dudas sobre los planes, respóndenos y lo vemos.\n\n{asesor}",
        ],
        'prueba_vencida' => [
            'nombre' => 'La prueba ya venció',
            'asunto' => 'Tu prueba de Kael Tech terminó, ¿la retomamos?',
            'cuerpo' => "Hola, equipo de {negocio}:\n\nTu periodo de prueba terminó. Guardamos tu información por si quieres continuar: con activar un plan vuelves a trabajar justo donde lo dejaste.\n\nSi algo no te convenció, cuéntanos; nos ayuda a mejorar y quizá lo podamos resolver.\n\n{asesor}",
        ],
        'agradecimiento' => [
            'nombre' => 'Seguimiento libre (después de una llamada/demo)',
            'asunto' => 'Gracias por tu tiempo, {negocio}',
            'cuerpo' => "Hola, equipo de {negocio}:\n\nGracias por conversar con nosotros. Quedamos atentos a cualquier consulta y a ayudarte con lo que necesites para ponerte en marcha.\n\n{asesor}",
        ],
    ];

    /** Plantillas que usa el envio automatico, por regla. */
    public const AUTOMATICAS = [
        'auto_sin_uso'    => 'sin_uso',
        'auto_por_vencer' => 'por_vencer',
        'auto_vencida'    => 'prueba_vencida',
    ];

    public static function aplicar(string $texto, array $vars): string
    {
        $reemplazos = [];
        foreach ($vars as $clave => $valor) {
            $reemplazos['{' . $clave . '}'] = (string) $valor;
        }

        return strtr($texto, $reemplazos);
    }
}
