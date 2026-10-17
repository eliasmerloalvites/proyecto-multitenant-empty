<?php

namespace App\Services\AppMovil;

/**
 * Normaliza celulares peruanos a la forma en que se guardan en la cuenta
 * movil: solo digitos con codigo de pais (51 + 9 digitos que empiezan en 9).
 *
 * En la base del taller los celulares vienen con formatos muy distintos
 * ("987654321", "9465623", "-", "+51 987 654 321"); por eso la cuenta movil
 * solo acepta un celular que se pueda normalizar sin ambiguedad. Para el MVP
 * solo se aceptan celulares de Peru.
 */
class NormalizadorCelular
{
    /** Devuelve "51987654321" o null si no es un celular peruano valido. */
    public static function normalizar(?string $celular): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $celular);

        if (strlen($digitos) === 9 && $digitos[0] === '9') {
            return '51' . $digitos;
        }

        if (strlen($digitos) === 11 && str_starts_with($digitos, '519')) {
            return $digitos;
        }

        return null;
    }

    /** "51987654321" -> "+51 *** *** 321", para mostrar sin exponer el numero. */
    public static function enmascarar(string $normalizado): string
    {
        return '+51 *** *** ' . substr($normalizado, -3);
    }
}
