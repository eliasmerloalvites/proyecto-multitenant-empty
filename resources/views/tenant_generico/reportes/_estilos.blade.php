{{--
    CSS compartido por todas las paginas de Reportes. Se incluye una vez
    por pagina (dentro de su propio <style>, ya que @include no filtra
    etiquetas) via @include('tenant_generico.reportes._estilos').
--}}
<style>
    .rpt-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }

    .rpt-card .card-title {
        font-weight: 800;
        color: #1F2937;
    }

    .rpt-filtros {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        padding: 16px;
        margin-bottom: 16px;
    }

    .rpt-filtros label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 4px;
    }

    .rpt-kpi-row {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }

    .rpt-kpi-row.cuatro {
        grid-template-columns: repeat(4, 1fr);
    }

    @media (max-width: 1200px) {
        .rpt-kpi-row { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 576px) {
        .rpt-kpi-row,
        .rpt-kpi-row.cuatro { grid-template-columns: repeat(2, 1fr); }
    }

    .rpt-kpi-tile {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        padding: 14px 16px;
    }

    .rpt-kpi-tile .rpt-kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 4px;
    }

    .rpt-kpi-tile .rpt-kpi-valor {
        font-size: 19px;
        font-weight: 800;
        color: #1F2937;
    }

    .rpt-kpi-tile.destacado {
        background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
    }

    .rpt-kpi-tile.destacado .rpt-kpi-label,
    .rpt-kpi-tile.destacado .rpt-kpi-valor {
        color: #fff;
    }

    .rpt-chart-wrap {
        position: relative;
        height: 280px;
        width: 100%;
        overflow: hidden;
    }

    .rpt-chart-wrap.chico {
        height: 240px;
    }

    .rpt-chart-wrap canvas {
        max-width: 100%;
    }

    .rpt-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #6B7280;
        border-top: none;
    }

    .rpt-table td {
        font-size: 13px;
        vertical-align: middle;
    }

    .rpt-pill {
        display: inline-block;
        background: #F9F7FF;
        color: #6C3BFF;
        border-radius: 999px;
        padding: 2px 10px;
        font-size: 11px;
        font-weight: 700;
    }

    .rpt-empty {
        text-align: center;
        color: #9CA3AF;
        padding: 30px 0;
    }

    .rpt-btn-exportar {
        background: #F9F7FF;
        color: #6C3BFF;
        font-weight: 700;
        border: 1px solid #EDE9FE;
    }

    .rpt-btn-exportar:hover {
        background: #EDE9FE;
        color: #6C3BFF;
    }

    .rpt-destacado-card {
        border-radius: 16px;
        padding: 20px;
        color: #fff;
        background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
        box-shadow: 0 4px 14px rgba(108,59,255,.25);
        height: 100%;
    }

    .rpt-destacado-card.oro {
        background: linear-gradient(135deg, #F59E0B, #F97316);
        box-shadow: 0 4px 14px rgba(245,158,11,.25);
    }

    .rpt-destacado-card .rpt-destacado-etiqueta {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        opacity: .85;
        margin-bottom: 6px;
    }

    .rpt-destacado-card .rpt-destacado-nombre {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .rpt-destacado-card .rpt-destacado-metrica {
        display: inline-block;
        background: rgba(255,255,255,.18);
        border-radius: 10px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 700;
    }
</style>
