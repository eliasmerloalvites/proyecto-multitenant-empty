<?php

namespace App\Services\AppMovil;

/**
 * Normaliza placas de motos: mayusculas y solo letras/numeros ("abc-123",
 * "ABC 123" y "ABC123" son la misma moto). En la base del taller las placas
 * vienen vacias, con guion, en minusculas, etc.; una placa solo se acepta si
 * tiene entre 5 y 7 caracteres (formatos peruanos: ABC-123, 1234-AB, ...).
 */
class NormalizadorPlaca
{
    /** Devuelve la placa normalizada o null si no parece una placa valida. */
    public static function normalizar(?string $placa): ?string
    {
        $limpia = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $placa));

        $longitud = strlen($limpia);

        return ($longitud >= 5 && $longitud <= 7) ? $limpia : null;
    }

    /** Expresion SQL que normaliza una columna de placa, para comparar sin importar el formato guardado. */
    public static function expresionSql(string $columna): string
    {
        return "UPPER(REPLACE(REPLACE(REPLACE({$columna}, '-', ''), ' ', ''), '.', ''))";
    }
}
