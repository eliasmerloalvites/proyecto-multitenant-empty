<?php

namespace App\Services\Exportacion;

use App\Models\Tenant\EmpresaFacturacion;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Genera el Excel de cualquier listado con el mismo diseno: banner con el
 * nombre de la empresa, titulo, filtros aplicados, encabezado de color,
 * filas alternadas, formatos de moneda/fecha, fila de totales con formulas,
 * filtros de columna, encabezado fijo y configuracion de impresion.
 *
 * Cada hoja se describe con un arreglo:
 *   nombre   Titulo de la pestana (max 31 caracteres)
 *   titulo   Titulo grande de la hoja
 *   filtros  Lista de textos que describen lo que se filtro (opcional)
 *   columnas Lista de ['titulo', 'tipo', 'ancho', 'total' (bool), 'centrar' (bool)]
 *   filas    Lista de arreglos con un valor por columna, en el mismo orden
 *   total_excluye  [indiceColumna, 'valor'] opcional: los totales ignoran las filas
 *                  cuyo valor en esa columna sea el indicado (ej. ventas anuladas)
 *   total_etiqueta Texto de la celda de la fila de totales
 */
class ExcelExporter
{
    public const TEXTO = 'texto';
    public const ENTERO = 'entero';
    public const DECIMAL = 'decimal';
    public const MONEDA = 'moneda';
    public const FECHA = 'fecha';
    public const PORCENTAJE = 'porcentaje';

    private const FORMATOS = [
        self::ENTERO => '#,##0',
        self::DECIMAL => '#,##0.00',
        self::MONEDA => '"S/" #,##0.00',
        self::FECHA => 'dd/mm/yyyy',
        self::PORCENTAJE => '0.0%',
    ];

    private const FILA_ENCABEZADO = 6;

    public static function descargar(string $nombreArchivo, array $hojas): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $empresa = self::nombreEmpresa();
        $colorBase = self::colorPrincipal();

        $spreadsheet->getProperties()
            ->setCreator($empresa)
            ->setLastModifiedBy($empresa)
            ->setTitle($hojas[0]['titulo'] ?? 'Reporte')
            ->setCompany($empresa);

        foreach ($hojas as $indice => $hoja) {
            self::construirHoja($spreadsheet, $hoja, $empresa, $colorBase, $indice);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Nombre del archivo con la empresa y la fecha, sin caracteres raros. */
    public static function nombreArchivo(string $base): string
    {
        return $base . '_' . Carbon::now('America/Lima')->format('Y-m-d_Hi') . '.xlsx';
    }

    private static function construirHoja(Spreadsheet $spreadsheet, array $hoja, string $empresa, string $colorBase, int $indice): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle(mb_substr($hoja['nombre'], 0, 31));
        $sheet->setShowGridlines(false);
        $sheet->getTabColor()->setRGB($colorBase);

        $columnas = $hoja['columnas'];
        $filas = $hoja['filas'];
        $numColumnas = count($columnas);
        $ultimaLetra = Coordinate::stringFromColumnIndex($numColumnas);
        $filaEnc = self::FILA_ENCABEZADO;
        $primeraDato = $filaEnc + 1;
        $ultimaDato = $filaEnc + max(count($filas), 1);

        // Banner superior: empresa, titulo, filtros y fecha de generacion.
        $sheet->setCellValue('A1', mb_strtoupper($empresa));
        $sheet->mergeCells("A1:{$ultimaLetra}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->getColor()->setRGB($colorBase);

        $sheet->setCellValue('A2', $hoja['titulo']);
        $sheet->mergeCells("A2:{$ultimaLetra}2");
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('1F2937');
        $sheet->getRowDimension(2)->setRowHeight(26);

        $filtros = $hoja['filtros'] ?? [];
        $sheet->setCellValue('A3', $filtros ? implode('   |   ', $filtros) : 'Sin filtros: se incluyen todos los registros');
        $sheet->mergeCells("A3:{$ultimaLetra}3");
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->getColor()->setRGB('4B5563');

        $sheet->setCellValue('A4', 'Generado el ' . Carbon::now('America/Lima')->format('d/m/Y H:i') . '   |   ' . count($filas) . ' registro(s)');
        $sheet->mergeCells("A4:{$ultimaLetra}4");
        $sheet->getStyle('A4')->getFont()->setSize(10)->getColor()->setRGB('6B7280');

        $sheet->getRowDimension(5)->setRowHeight(6);

        // Encabezado de la tabla.
        foreach ($columnas as $i => $columna) {
            $letra = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$letra}{$filaEnc}", $columna['titulo']);
            $sheet->getColumnDimension($letra)->setWidth($columna['ancho'] ?? 16);
        }

        $rangoEnc = "A{$filaEnc}:{$ultimaLetra}{$filaEnc}";
        $sheet->getStyle($rangoEnc)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $colorBase]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1F2937']]],
        ]);
        $sheet->getRowDimension($filaEnc)->setRowHeight(28);

        // Datos.
        if (empty($filas)) {
            $sheet->setCellValue("A{$primeraDato}", 'No hay datos para los filtros seleccionados.');
            $sheet->mergeCells("A{$primeraDato}:{$ultimaLetra}{$primeraDato}");
            $sheet->getStyle("A{$primeraDato}")->getFont()->setItalic(true)->getColor()->setRGB('6B7280');
            $sheet->getStyle("A{$primeraDato}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            foreach ($filas as $f => $fila) {
                $filaExcel = $primeraDato + $f;

                foreach ($columnas as $i => $columna) {
                    $letra = Coordinate::stringFromColumnIndex($i + 1);
                    self::escribirCelda($sheet, "{$letra}{$filaExcel}", $fila[$i] ?? null, $columna['tipo'] ?? self::TEXTO);
                }
            }

            $rangoDatos = "A{$primeraDato}:{$ultimaLetra}{$ultimaDato}";
            $sheet->getStyle($rangoDatos)->applyFromArray([
                'font' => ['size' => 10, 'color' => ['rgb' => '1F2937']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);

            // Filas alternadas y alineacion/formato por tipo de columna.
            for ($r = $primeraDato; $r <= $ultimaDato; $r++) {
                if (($r - $primeraDato) % 2 === 1) {
                    $sheet->getStyle("A{$r}:{$ultimaLetra}{$r}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F6FA');
                }
            }

            foreach ($columnas as $i => $columna) {
                $letra = Coordinate::stringFromColumnIndex($i + 1);
                $tipo = $columna['tipo'] ?? self::TEXTO;
                $estilo = $sheet->getStyle("{$letra}{$primeraDato}:{$letra}{$ultimaDato}");

                if (isset(self::FORMATOS[$tipo])) {
                    $estilo->getNumberFormat()->setFormatCode(self::FORMATOS[$tipo]);
                }

                if (in_array($tipo, [self::ENTERO, self::DECIMAL, self::MONEDA, self::PORCENTAJE], true)) {
                    $estilo->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } elseif ($tipo === self::FECHA || ! empty($columna['centrar'])) {
                    $estilo->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }

            $sheet->setAutoFilter("A{$filaEnc}:{$ultimaLetra}{$ultimaDato}");

            // Fila de totales (formulas, para que sigan vivas si el usuario filtra o edita).
            if (collect($columnas)->contains(fn ($c) => ! empty($c['total']))) {
                self::filaTotales($sheet, $hoja, $columnas, $primeraDato, $ultimaDato, $ultimaLetra, $colorBase);
            }
        }

        // Encabezado fijo y configuracion de impresion.
        $sheet->freezePane('A' . $primeraDato);
        $sheet->getPageSetup()
            ->setOrientation($numColumnas > 7 ? PageSetup::ORIENTATION_LANDSCAPE : PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setRowsToRepeatAtTopByStartAndEnd($filaEnc, $filaEnc);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.6)->setLeft(0.4)->setRight(0.4);
        $sheet->getHeaderFooter()->setOddFooter('&L' . $empresa . '&RPágina &P de &N');
    }

    private static function escribirCelda($sheet, string $coordenada, $valor, string $tipo): void
    {
        if ($valor === null || $valor === '') {
            return;
        }

        switch ($tipo) {
            case self::FECHA:
                // Solo la fecha (YYYY-MM-DD), sin hora ni zona horaria, para que
                // en Excel sea un dia exacto que se pueda filtrar y ordenar.
                $dia = substr((string) $valor, 0, 10);
                $serial = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dia) ? Date::stringToExcel($dia) : false;
                if ($serial === false) {
                    $sheet->setCellValueExplicit($coordenada, (string) $valor, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($coordenada, (int) $serial);
                }
                break;
            case self::ENTERO:
            case self::DECIMAL:
            case self::MONEDA:
            case self::PORCENTAJE:
                $sheet->setCellValue($coordenada, (float) $valor);
                break;
            default:
                // Siempre como texto: evita que Excel convierta codigos como
                // "00123" o "1E5" en numeros, o un valor que empiece con "="
                // en una formula.
                $sheet->setCellValueExplicit($coordenada, (string) $valor, DataType::TYPE_STRING);
        }
    }

    private static function filaTotales($sheet, array $hoja, array $columnas, int $primera, int $ultima, string $ultimaLetra, string $colorBase): void
    {
        $filaTotal = $ultima + 1;
        $excluye = $hoja['total_excluye'] ?? null;
        $etiqueta = $hoja['total_etiqueta'] ?? 'TOTAL';

        $sheet->setCellValue("A{$filaTotal}", $etiqueta);

        foreach ($columnas as $i => $columna) {
            if (empty($columna['total'])) {
                continue;
            }

            $letra = Coordinate::stringFromColumnIndex($i + 1);
            $rango = "{$letra}{$primera}:{$letra}{$ultima}";

            if ($excluye) {
                $letraCond = Coordinate::stringFromColumnIndex($excluye[0] + 1);
                $rangoCond = "{$letraCond}{$primera}:{$letraCond}{$ultima}";
                $formula = "=SUMIF({$rangoCond},\"<>{$excluye[1]}\",{$rango})";
            } else {
                $formula = "=SUM({$rango})";
            }

            $sheet->setCellValue("{$letra}{$filaTotal}", $formula);

            $formato = self::FORMATOS[$columna['tipo'] ?? self::DECIMAL] ?? self::FORMATOS[self::DECIMAL];
            $sheet->getStyle("{$letra}{$filaTotal}")->getNumberFormat()->setFormatCode($formato);
            $sheet->getStyle("{$letra}{$filaTotal}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $sheet->getStyle("A{$filaTotal}:{$ultimaLetra}{$filaTotal}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7EF']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $colorBase]],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => $colorBase]],
            ],
        ]);
        $sheet->getRowDimension($filaTotal)->setRowHeight(22);
    }

    private static function nombreEmpresa(): string
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        return trim((string) ($empresa?->nombre_comercial ?: $empresa?->razon_social)) ?: (string) tenant('id');
    }

    /**
     * Color del encabezado: el de la marca del negocio (Configuracion >
     * Empresa) si es lo bastante oscuro para que el texto blanco se lea; si
     * no, un azul institucional.
     */
    private static function colorPrincipal(): string
    {
        $porDefecto = '1F4E79';
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        $color = ltrim((string) $empresa?->color_main, '#');

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $color)) {
            return $porDefecto;
        }

        [$r, $g, $b] = [hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2))];
        $luminancia = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminancia < 0.55 ? strtoupper($color) : $porDefecto;
    }
}
