@extends('tenant_' . tenant('tipo_negocio') . '.layout.appAdminLte')
@section('titulo', 'Nueva Venta')

@section('contenido')

    <style>
        :root {
            --primary: #6C3BFF;
            --primary-light: #8B5CF6;
            --success: #22C55E;
            --danger: #EF4444;
            --dark: #111827;
            --gray: #6B7280;
            --border: #E5E7EB;
            --bg: #F4F7FB;
        }

        body {
            background: #F4F7FB;
            font-family: 'Inter', sans-serif;
        }

        .pos-wrapper {
            padding: 10px;
        }

        .cotizacion-origen-aviso {
            background: #F9F7FF;
            border: 1px solid #DCD1FF;
            color: #4C1D95;
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 13px;
        }

        /* =========================
                            LAYOUT
                            ========================= */

        .pos-panel {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #EEF2F7;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .04);
            height: calc(100vh - 70px);
            display: flex;
            gap: 14px;
            overflow: hidden;
        }

        .pos-left {
            height: calc(100vh - 90px);
            display: flex;
            flex-direction: column;
            padding: 12px;
        }

        .pos-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
            padding: 10px;
        }

        /* =========================
                            HEADER
                            ========================= */

        .pos-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .pos-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--dark);
        }

        .pos-subtitle {
            font-size: 12px;
            color: var(--gray);
        }

        /* =========================
                            SEARCH
                            ========================= */

        .search-box {
            height: 48px;
            background: #F9FAFB;
            border: 1px solid var(--border);
            border-radius: 14px;
            display: flex;
            align-items: center;
            padding: 0 14px;
        }

        .search-box input {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            font-size: 14px;
            margin-left: 10px;
        }

        /* =========================
                            CATEGORIES
                            ========================= */

        .categories {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            overflow-y: visible;
            padding: 4px 0 5px;
            scrollbar-width: none;
        }

        .categories::-webkit-scrollbar {
            display: none;
        }

        .category-btn {
            border: none;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            height: 40px;
            padding: 0 18px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .category-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);

        }

        /* =========================
                            PRODUCTS
                            ========================= */

        .products-grid {
            flex: 1;
            align-content: start;
            overflow-y: auto;
            overflow-x: hidden;
            display: grid;
            grid-template-columns:
                repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px;
            padding-right: 4px;
        }

        .product-card {
            background: #fff;
            border: 1px solid #EEF2F7;
            border-radius: 16px;
            padding: 10px;
            transition: .2s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .06);
        }

        .product-image {
            width: 100%;
            height: 110px;
            background: #F9FAFB;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .product-image img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .product-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.3;
            height: 34px;
            overflow: hidden;
        }

        .product-description {
            font-size: 11px;
            color: var(--gray);
            margin-top: 3px;
            height: 16px;
            overflow: hidden;
        }

        .product-footer {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-price {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
        }

        .product-stock {
            font-size: 10px;
            color: var(--success);
            font-weight: 700;
        }

        /* =========================
                            CART
                            ========================= */

        .cart-list {
            flex: 1;
            overflow-y: auto;
            padding-right: 4px;
        }

        .cart-item {
            display: flex;
            gap: 10px;
            background: #fff;
            border: 1px solid #EEF2F7;
            border-radius: 14px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .cart-image {
            width: 55px;
            height: 55px;
            background: #F9FAFB;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .cart-image img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .cart-info {
            flex: 1;
        }

        .cart-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.3;
        }

        .cart-price {
            font-size: 11px;
            color: var(--gray);
            margin-top: 2px;
        }

        .cart-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            background: #F9FAFB;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: #fff;
            font-weight: 700;
        }

        .qty-value {
            width: 28px;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
        }

        .cart-total {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
        }

        .btn-remove {
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 8px;
            background: #FEF2F2;
            color: var(--danger);
            font-size: 12px;
        }

        /* =========================
                            TOTAL
                            ========================= */

        .total-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 16px;
            padding: 14px;
            color: #fff;
            margin-top: 10px;
        }

        .total-label {
            font-size: 11px;
            opacity: .8;
        }

        .total-amount {
            font-size: 30px;
            font-weight: 900;
        }

        /* =========================
                            PAYMENT
                            ========================= */

        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
        }

        .payment-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px;
        }

        .payment-box label {
            font-size: 10px;
            color: var(--gray);
            display: block;
            margin-bottom: 2px;
        }

        .payment-box input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 18px;
            font-weight: 800;
        }

        /* =========================
                            SCROLL
                            ========================= */

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 10px;
        }


        .load-more-container {
            padding: 20px 0;
            display: flex;
            justify-content: center;
        }

        .btn-load-more {
            border: none;
            background: #fff;
            border: 1px solid #E5E7EB;
            height: 42px;
            padding: 0 20px;
            border-radius: 14px;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn-load-more:hover {
            background: #F9FAFB;
        }


        .checkout-card {
            background: linear-gradient(135deg, #6C3BFF, #8B5CF6);
            border-radius: 24px;
            padding: 20px;
            color: #fff;
            box-shadow: 0 10px 30px rgba(108, 59, 255, .25);
        }

        .checkout-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .checkout-label {
            font-size: 12px;
            opacity: .8;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .checkout-total {
            font-size: 42px;
            font-weight: 900;
            line-height: 1;
        }

        .checkout-items {
            background: rgba(255, 255, 255, .18);
            padding: 8px 12px;
            border-radius: 14px;
            font-size: 12px;
            font-weight: 700;
        }

        .btn-checkout {
            width: 100%;
            height: 54px;
            border: none;
            border-radius: 18px;
            background: #fff;
            color: #6C3BFF;
            font-size: 16px;
            font-weight: 800;
            transition: .2s ease;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
        }



        .checkout-modal {

            border: none;
            border-radius: 28px;

            overflow: hidden;

            background: #fff;

        }

        .checkout-header {

            border: none;

            padding: 22px 26px 10px;

        }

        .checkout-title {

            font-size: 32px;
            font-weight: 800;

            color: #111827;

            margin: 0;

        }

        .checkout-subtitle {

            font-size: 14px;

            color: #6B7280;

            margin-top: 2px;

        }

        .checkout-close {

            border: none;
            background: none;

            font-size: 34px;

            color: #9CA3AF;

            opacity: 1;

            outline: none !important;

        }

        .checkout-close:hover {

            color: #111827;

        }

        .checkout-body {

            padding: 10px 26px 26px;

        }

        /* =========================================
                            LEFT / RIGHT
                            ========================================= */

        .checkout-block {

            margin-bottom: 18px;

        }

        /* Antes esta regla era ".checkout-label { color:#ffffff }" sin
           calificar: como tiene la misma especificidad que la definicion
           de la linea 434 (usada dentro de ".checkout-card", el cartel
           morado del total, que SI necesita texto blanco), ganaba en
           todos lados por venir despues en el archivo — dejando cada
           etiqueta del modal de checkout (Cliente, Metodos de Pago,
           Adelanto, Numero de cuotas, etc.) en blanco sobre el fondo
           blanco del modal, es decir invisible. Se acota a ".checkout-body"
           para que solo afecte las etiquetas del modal, sin tocar el
           cartel morado.
        */
        .checkout-body .checkout-label {

            display: block;

            font-size: 13px;
            font-weight: 700;

            color: #111827;

            margin-bottom: 8px;

        }

        /* =========================================
                            INPUTS
                            ========================================= */

        .checkout-input {

            width: 100%;

            height: 52px;

            border: 1px solid #E5E7EB;

            border-radius: 18px;

            padding: 0 18px;

            font-size: 15px;

            outline: none;

            transition: .2s ease;

            background: #fff;

        }

        textarea.checkout-input {

            height: auto;

            padding-top: 14px;

            resize: none;

        }

        .checkout-input:focus {

            border-color: #7C3AED;

            box-shadow:
                0 0 0 4px rgba(124, 58, 237, .08);

        }

        /* =========================================
                            PAGO DIVIDIDO (multi-metodo)
                            ========================================= */

        .payment-method-row {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .payment-method-select {
            flex: 1.3;
            height: 48px;
            border: 1px solid #E5E7EB;
            border-radius: 14px;
            padding: 0 12px;
            font-size: 14px;
            background: #fff;
            outline: none;
            transition: .2s ease;
        }

        .payment-method-amount {
            flex: 1;
            height: 48px;
            border: 1px solid #E5E7EB;
            border-radius: 14px;
            padding: 0 12px;
            font-size: 14px;
            background: #fff;
            outline: none;
            text-align: right;
            transition: .2s ease;
        }

        .payment-method-select:focus,
        .payment-method-amount:focus {
            border-color: #7C3AED;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, .08);
        }

        .btn-remove-payment {
            width: 40px;
            height: 48px;
            border: none;
            border-radius: 12px;
            background: #FEE2E2;
            color: #DC2626;
            font-size: 14px;
            flex-shrink: 0;
            transition: .2s ease;
        }

        .btn-remove-payment:hover { background: #FCA5A5; }

        .btn-add-payment {
            width: 100%;
            height: 44px;
            border: 1.5px dashed #C4B5FD;
            border-radius: 14px;
            background: #F5F3FF;
            color: #7C3AED;
            font-size: 13px;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn-add-payment:hover { background: #EDE9FE; }

        .payment-balance-box {
            background: #F9FAFB;
            border: 1.5px solid #E5E7EB;
            border-radius: 18px;
            padding: 14px 18px;
            transition: .2s ease;
        }

        .payment-balance-box.is-ok { border-color: #86EFAC; background: #F0FDF4; }
        .payment-balance-box.is-error { border-color: #FCA5A5; background: #FEF2F2; }

        .payment-balance-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #6B7280;
            margin-bottom: 4px;
        }

        .payment-balance-row span:last-child {
            font-weight: 700;
            color: #111827;
        }

        .payment-balance-diff {
            margin-top: 8px;
            font-size: 14px;
            font-weight: 800;
            text-align: right;
        }

        /* =========================================
                            CLIENT SELECTOR
                            ========================================= */

        .client-selector {

            width: 100%;

            height: 72px;

            border: none;

            background: #F9FAFB;

            border: 1px solid #E5E7EB;

            border-radius: 20px;

            padding: 0 18px;

            display: flex;

            justify-content: space-between;
            align-items: center;

            transition: .2s ease;

        }

        .client-selector:hover {

            border-color: #7C3AED;

            background: #fff;

        }

        .client-name {

            font-size: 18px;
            font-weight: 800;

            color: #111827;

        }

        .client-subtitle {

            font-size: 13px;

            color: #6B7280;

            margin-top: 2px;

        }

        .client-selector i {

            font-size: 18px;

            color: #6B7280;

        }

        /* =========================================
                            VOUCHER SWITCH
                            ========================================= */

        /* Aviso cuando el tenant aun no puede emitir comprobantes */
        .facturacion-pendiente {
            margin-top: 10px;
            padding: 12px 14px;
            background: #FEF6E7;
            border: 1px solid #F0C36D;
            border-left: 3px solid #D68910;
            border-radius: 10px;
        }

        .facturacion-pendiente-titulo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 13px;
            color: #8A5A12;
            margin-bottom: 6px;
        }

        .facturacion-pendiente-texto {
            margin: 0 0 6px;
            font-size: 12px;
            color: #6B5320;
        }

        .facturacion-pendiente-lista {
            margin: 0 0 10px;
            padding-left: 18px;
            font-size: 12px;
            color: #6B5320;
        }

        .facturacion-pendiente-lista li { margin-bottom: 3px; }

        .facturacion-pendiente-enlace {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            color: #8A5A12;
            text-decoration: underline;
        }

        .facturacion-pendiente-enlace:hover { color: #5E3D0C; }

        .voucher-switch {

            background: #F3F4F6;

            border-radius: 18px;

            padding: 4px;

            display: flex;

            gap: 6px;

        }

        .voucher-option {

            flex: 1;

            height: 48px;

            border: none;

            border-radius: 14px;

            background: transparent;

            font-size: 14px;
            font-weight: 800;

            color: #6B7280;

            transition: .2s ease;

        }

        .voucher-option.active {

            background: #7C3AED;

            color: #fff;

            box-shadow:
                0 8px 20px rgba(124, 58, 237, .18);

        }

        /* =========================================
                            PAYMENT SUMMARY
                            ========================================= */

        .payment-summary {

            background: #fff;

            border-radius: 24px;

            height: 100%;

            display: flex;
            flex-direction: column;

        }

        /* =========================================
                            TOTAL CARD
                            ========================================= */

        .payment-total-card {

            background: linear-gradient(135deg,
                    #6D28D9,
                    #8B5CF6);

            border-radius: 24px;

            padding: 24px;

            color: #fff;

            margin-bottom: 18px;

            box-shadow:
                0 14px 30px rgba(109, 40, 217, .20);

        }

        .payment-total-label {

            font-size: 12px;
            font-weight: 700;

            opacity: .85;

            margin-bottom: 8px;

        }

        .payment-total-value {

            font-size: 52px;
            font-weight: 900;

            line-height: 1;

            margin-bottom: 16px;

        }

        .payment-items {

            display: inline-flex;

            align-items: center;
            justify-content: center;

            height: 34px;

            padding: 0 14px;

            border-radius: 999px;

            background: rgba(255, 255, 255, .18);

            font-size: 12px;
            font-weight: 700;

        }

        /* =========================================
                            MONEY INPUT
                            ========================================= */

        .checkout-money {

            font-size: 34px;
            font-weight: 800;

            height: 70px;

        }

        /* =========================================
                    VENTA AL CREDITO (solo generico)
                    ========================================= */

        .checkout-help-text {
            font-size: 12px;
            color: #6B7280;
            margin-top: 6px;
            margin-bottom: 10px;
        }

        .checkout-checkbox-row {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .checkout-checkbox-row label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #374151;
            margin: 0;
            cursor: pointer;
        }

        .checkout-checkbox-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        #cuotasConfig .row {
            margin: 0 -6px;
        }

        #cuotasConfig .col-6 {
            padding: 0 6px;
        }

        #cuotasPreview {
            margin-top: 4px;
            max-height: 160px;
            overflow-y: auto;
        }

        .cuota-preview-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #EEF2F7;
            border-radius: 10px;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .cuota-preview-row .cuota-preview-numero {
            font-weight: 700;
            color: #6C3BFF;
        }

        .cuota-preview-row .cuota-preview-monto {
            font-weight: 700;
        }

        /* =========================================
                            FOOTER BUTTON
                            ========================================= */

        .checkout-footer {

            margin-top: auto;

            padding-top: 12px;

            position: sticky;

            bottom: 0;

            background: #fff;

        }

        .btn-finish-sale {

            width: 100%;

            height: 58px;

            border: none;

            border-radius: 20px;

            background: linear-gradient(135deg,
                    #6D28D9,
                    #8B5CF6);

            color: #fff;

            font-size: 16px;
            font-weight: 900;

            transition: .2s ease;

            box-shadow:
                0 12px 24px rgba(109, 40, 217, .20);

        }

        .btn-finish-sale:hover {

            transform: translateY(-2px);

        }

        /* =========================================
                            RESPONSIVE
                            ========================================= */

        @media(max-width:991px) {

            .payment-summary {

                margin-top: 18px;

            }

            .checkout-title {

                font-size: 24px;

            }

            .payment-total-value {

                font-size: 42px;

            }

        }



        /* CLIENTE */
        .client-selector {

            width: 100%;

            min-height: 74px;

            border: 1px solid #E5E7EB;

            border-radius: 20px;

            background: #fff;

            padding: 16px 18px;

            display: flex;

            justify-content: space-between;
            align-items: center;

            cursor: pointer;

            transition: .2s ease;

        }

        .client-selector:hover {

            border-color: #7C3AED;

            box-shadow:
                0 8px 20px rgba(124, 58, 237, .08);

            transform: translateY(-1px);

        }

        .client-left {

            display: flex;

            align-items: center;

            gap: 14px;

        }

        .client-avatar {

            width: 46px;
            height: 46px;

            border-radius: 14px;

            background: #F3F4F6;

            display: flex;

            align-items: center;
            justify-content: center;

            color: #6B7280;

            font-size: 18px;

        }

        .client-name {

            font-size: 18px;
            font-weight: 800;

            color: #111827;

            line-height: 1.2;

        }

        .client-subtitle {

            font-size: 13px;

            color: #6B7280;

            margin-top: 4px;

        }

        .client-action {

            width: 42px;
            height: 42px;

            border-radius: 14px;

            background: #F3F4F6;

            display: flex;

            align-items: center;
            justify-content: center;

            color: #6B7280;

            transition: .2s ease;

        }

        .client-selector:hover .client-action {
            background: #7C3AED;
            color: #fff;
        }


        /* =========================================
                    MODAL CLIENTES
                    ========================================= */

        .client-modal .modal-content {

            border: none;

            border-radius: 26px;

            overflow: hidden;

        }

        .client-modal-header {

            border: none;

            padding: 22px 24px 12px;

        }

        .client-modal-title {

            font-size: 30px;
            font-weight: 800;

            color: #111827;

            margin: 0;

        }

        .client-modal-subtitle {

            font-size: 14px;

            color: #6B7280;

            margin-top: 4px;

        }

        .client-modal-body {

            padding: 0 24px 24px;

        }

        /* =========================================
                    SEARCH
                    ========================================= */

        .client-search {

            position: relative;

            margin-bottom: 18px;

        }

        .client-search i {

            position: absolute;

            left: 18px;
            top: 50%;

            transform: translateY(-50%);

            color: #9CA3AF;

            font-size: 16px;

        }

        .client-search input {

            width: 100%;

            height: 54px;

            border: 1px solid #E5E7EB;

            border-radius: 18px;

            padding-left: 48px;
            padding-right: 16px;

            font-size: 15px;

            outline: none;

            transition: .2s ease;

        }

        .client-search input:focus {

            border-color: #7C3AED;

            box-shadow:
                0 0 0 4px rgba(124, 58, 237, .08);

        }

        /* =========================================
                    ACTIONS
                    ========================================= */

        .client-actions {

            display: flex;

            justify-content: space-between;
            align-items: center;

            margin-bottom: 18px;

        }

        .client-counter {

            font-size: 14px;
            font-weight: 700;

            color: #6B7280;

        }

        .btn-new-client {

            height: 42px;

            border: none;

            border-radius: 14px;

            padding: 0 16px;

            background: linear-gradient(135deg,
                    #6D28D9,
                    #8B5CF6);

            color: #fff;

            font-size: 14px;
            font-weight: 800;

            box-shadow:
                0 10px 20px rgba(109, 40, 217, .18);

            transition: .2s ease;

        }

        .btn-new-client:hover {

            transform: translateY(-2px);

        }

        /* =========================================
                    LIST
                    ========================================= */

        .client-list {

            max-height: 500px;

            overflow-y: auto;

            padding-right: 4px;

        }

        .client-item {

            border: 1px solid #E5E7EB;

            border-radius: 20px;

            padding: 16px 18px;

            display: flex;

            justify-content: space-between;
            align-items: center;

            margin-bottom: 12px;

            transition: .2s ease;

            cursor: pointer;

            background: #fff;

        }

        .client-item:hover {

            border-color: #7C3AED;

            transform: translateY(-1px);

            box-shadow:
                0 10px 20px rgba(124, 58, 237, .08);

        }

        .client-item-left {

            display: flex;

            align-items: center;

            gap: 14px;

        }

        .client-item-avatar {

            width: 52px;
            height: 52px;

            border-radius: 16px;

            background: #F3F4F6;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 18px;

            font-weight: 800;

            color: #6B7280;

        }

        .client-item-name {

            font-size: 17px;
            font-weight: 800;

            color: #111827;

            line-height: 1.2;

        }

        .client-item-document {

            font-size: 13px;

            color: #6B7280;

            margin-top: 4px;

        }

        .client-item-phone {

            font-size: 13px;

            color: #6B7280;

            margin-top: 2px;

        }

        /* =========================================
                    BTN SELECT
                    ========================================= */

        .btn-select-client {

            min-width: 120px;

            height: 42px;

            border: none;

            border-radius: 14px;

            background: #F3F4F6;

            color: #111827;

            font-size: 14px;
            font-weight: 800;

            transition: .2s ease;

        }

        .btn-select-client:hover {

            background: #7C3AED;

            color: #fff;

        }

        /* =========================================
                    SCROLL
                    ========================================= */

        .client-list::-webkit-scrollbar {

            width: 6px;

        }

        .client-list::-webkit-scrollbar-thumb {

            background: #E5E7EB;

            border-radius: 999px;

        }


        /* =========================================
            MINI MODAL
            ========================================= */

        .mini-client-modal .modal-content {

            border: none;

            border-radius: 24px;

            overflow: hidden;

        }

        .mini-client-header {

            border: none;

            padding: 22px 24px 12px;

        }

        .mini-client-title {

            font-size: 28px;
            font-weight: 800;

            color: #111827;

            margin: 0;

        }

        .mini-client-subtitle {

            font-size: 14px;

            color: #6B7280;

            margin-top: 4px;

        }

        .mini-client-body {

            padding: 0 24px 24px;

        }

        /* =========================================
            FORM
            ========================================= */

        .mini-client-group {

            margin-bottom: 16px;

        }

        .mini-client-label {

            display: block;

            font-size: 13px;
            font-weight: 700;

            color: #6B7280;

            margin-bottom: 8px;

        }

        .mini-client-input {

            width: 100%;

            height: 52px;

            border: 1px solid #E5E7EB;

            border-radius: 16px;

            padding: 0 16px;

            font-size: 15px;

            outline: none;

            transition: .2s ease;

        }

        .mini-client-input:focus {

            border-color: #7C3AED;

            box-shadow:
                0 0 0 4px rgba(124, 58, 237, .08);

        }

        textarea.mini-client-input {

            height: auto;

            padding-top: 14px;

            resize: none;

        }

        /* =========================================
            BTN SAVE
            ========================================= */

        .btn-save-client {

            width: 100%;

            height: 56px;

            border: none;

            border-radius: 18px;

            background: linear-gradient(135deg,
                    #6D28D9,
                    #8B5CF6);

            color: #fff;

            font-size: 15px;
            font-weight: 900;

            margin-top: 6px;

            transition: .2s ease;

            box-shadow:
                0 12px 24px rgba(109, 40, 217, .18);

        }

        .btn-save-client:hover {

            transform: translateY(-2px);

        }
    </style>

    <div class="container-fluid pos-wrapper">

        <!-- AVISO: venta cargada desde una Cotizacion (exclusivo de generico) -->
        <input type="hidden" id="cotizacionOrigenId">
        <div id="avisoCotizacionOrigen" class="cotizacion-origen-aviso" style="display:none;">
            <div>
                <i class="fas fa-file-signature mr-1"></i>
                <span id="cotizacionOrigenTexto"></span>
            </div>
            <button type="button" class="btn btn-sm btn-light border" onclick="limpiarCotizacionOrigen()">
                <i class="fas fa-times mr-1"></i> Quitar referencia
            </button>
        </div>

        <div class="row g-2">
            <!-- LEFT -->
            <div class="col-lg-4">
                <div class="pos-panel pos-left">
                    <!-- HEADER -->
                    <div class="pos-header">
                        <div>
                            <div class="pos-title">Venta Actual</div>
                        </div>
                        <button class="btn btn-sm btn-danger">Limpiar</button>
                    </div>
                    <!-- CART -->
                    <div class="cart-list" id="cartItems">
                    </div>
                    <div class="total-card">
                        <div class="checkout-top">
                            <div>
                                <div class="checkout-label">TOTAL A PAGAR</div>
                                <div class="checkout-total" id="cartTotal">S/ 0.00</div>
                            </div>
                            <div class="checkout-items" id="checkoutItems">0 productos</div>
                        </div>
                        <button class="btn-checkout" onclick="openCheckout()">
                            <i class="fas fa-cash-register mr-2"></i>
                            COBRAR
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-lg-8">
                <div class="pos-panel pos-right">
                    <!-- HEADER -->
                    <div class="pos-header">
                        <div>
                            <div class="pos-title">Productos</div>
                            <div class="pos-subtitle">Catálogo disponible</div>
                        </div>
                    </div>

                    <!-- SEARCH -->
                    <div class="search-box">
                        <i class="fas fa-search text-muted"></i>
                        <input class="search-input" type="text" placeholder="Buscar producto...">
                    </div>

                    <!-- CATEGORIES -->
                    <div class="categories">
                        <button class="category-btn" data-id="all" active>
                            Todas
                        </button>
                        @foreach ($categoria as $t => $val)
                            <button class="category-btn" data-id="{{ $val->CAT_Id }}">
                                {{ $val->CAT_Nombre }}
                            </button>
                        @endforeach
                    </div>

                    <!-- PRODUCTS -->
                    <div class="products-grid" id="productsGrid">

                    </div>
                    <div class="load-more-container">
                        <button id="btnLoadMore" class="btn-load-more">
                            Cargar más
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content checkout-modal">
                <!-- HEADER -->
                <div class="modal-header checkout-header">
                    <div>
                        <h4 class="checkout-title">
                            <i class="fas fa-cash-register mr-2"></i>
                            Finalizar Venta
                        </h4>
                        <div class="checkout-subtitle">
                            Completa la información de pago
                        </div>
                    </div>

                    <button type="button" class="close checkout-close" data-dismiss="modal">
                        <span>&times;</span>

                    </button>

                </div>

                <!-- BODY -->
                <div class="modal-body checkout-body">
                    <div class="row">
                        <!-- LEFT -->
                        <div class="col-lg-7">
                            <!-- COMPROBANTE -->
                            <div class="checkout-block">
                                <label class="checkout-label">Tipo Comprobante</label>
                                <div class="voucher-switch">
                                    <button class="voucher-option active" onclick="changeVoucher(this,'NOTA')">
                                        Nota Venta
                                    </button>
                                    @if ($puedeFacturar)
                                        <button class="voucher-option" onclick="changeVoucher(this,'BOLETA')">
                                            Boleta
                                        </button>
                                        <button class="voucher-option" onclick="changeVoucher(this,'FACTURA')">
                                            Factura
                                        </button>
                                    @endif
                                </div>

                                @unless ($puedeFacturar)
                                    <div class="facturacion-pendiente">
                                        <div class="facturacion-pendiente-titulo">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            Boleta y factura no disponibles
                                        </div>
                                        <p class="facturacion-pendiente-texto">
                                            Para emitir comprobantes electronicos falta completar:
                                        </p>
                                        <ul class="facturacion-pendiente-lista">
                                            @foreach ($problemasFacturacion as $problema)
                                                <li>{{ $problema }}</li>
                                            @endforeach
                                        </ul>
                                        <a href="{{ route('tenant.configuracion.empresa.index') }}"
                                           class="facturacion-pendiente-enlace" target="_blank">
                                            Completar datos de facturacion
                                        </a>
                                    </div>
                                @endunless
                            </div>

                            <!-- TIPO DE VENTA (Contado / Crédito) -->
                            <div class="checkout-block">
                                <label class="checkout-label">Tipo de Venta</label>
                                <div class="voucher-switch">
                                    <button type="button" class="voucher-option tipoventa-option active" onclick="changeTipoVenta(this,'CONTADO')">
                                        Contado
                                    </button>
                                    <button type="button" class="voucher-option tipoventa-option" onclick="changeTipoVenta(this,'CREDITO')">
                                        Crédito
                                    </button>
                                </div>
                            </div>

                            <!-- DETALLE DE CREDITO (solo si Tipo de Venta = Crédito) -->
                            <div class="checkout-block" id="creditoBlock" style="display:none;">
                                <label class="checkout-label">Adelanto (S/)</label>
                                <input type="number" step="0.01" min="0" id="inputAdelanto" class="checkout-input checkout-money"
                                    value="0.00" oninput="onAdelantoChange()">
                                <div class="checkout-help-text">
                                    Deja en 0.00 para pasar toda la venta a crédito. Si el cliente
                                    deja un adelanto, indícalo aquí (no puede superar el total).
                                </div>

                                <div class="checkout-checkbox-row">
                                    <label>
                                        <input type="checkbox" id="chkDefinirCuotas" onchange="onDefinirCuotasChange()">
                                        Definir cuotas de pago
                                    </label>
                                </div>

                                <div id="cuotasConfig" style="display:none;">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="checkout-label">Número de cuotas</label>
                                            <input type="number" step="1" min="1" max="60" id="inputNumCuotas" class="checkout-input" value="1" oninput="actualizarPreviewCuotas()">
                                        </div>
                                        <div class="col-6">
                                            <label class="checkout-label">Cada cuántos días vence cada cuota</label>
                                            <input type="number" step="1" min="1" max="365" id="inputFrecuenciaDias" class="checkout-input" value="30" oninput="actualizarPreviewCuotas()">
                                        </div>
                                    </div>
                                    <div class="checkout-help-text">
                                        El saldo (total menos adelanto) se reparte en partes iguales entre
                                        las cuotas; la última absorbe el redondeo. Así quedarían con estos valores:
                                    </div>
                                    <div id="cuotasPreview"></div>
                                </div>
                            </div>

                            <div class="checkout-block">
                                <input type="hidden" id="cliente_id">
                                <label class="checkout-label">Cliente</label>
                                <div class="client-selector" onclick="openClientModal()">
                                    <!-- LEFT -->
                                    <div class="client-left">
                                        <div class="client-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="client-name" id="clientName">
                                                SIN CLIENTE
                                            </div>
                                            <div class="client-subtitle" id="clientSubtitle">
                                                Venta rápida (opcional)
                                            </div>
                                        </div>
                                    </div>
                                    <!-- RIGHT -->
                                    <div class="client-action">
                                        <i class="fas fa-search"></i>
                                    </div>

                                </div>

                            </div>



                            <!-- METODOS DE PAGO (uno o varios a la vez) -->
                            <div class="checkout-block" id="paymentMethodsBlock">

                                <label class="checkout-label" id="paymentMethodsLabel">

                                    Métodos de Pago

                                </label>

                                <div id="paymentRows"></div>

                                <button type="button" class="btn-add-payment" onclick="agregarLineaPago()">
                                    <i class="fas fa-plus-circle mr-1"></i> Agregar otro método de pago
                                </button>

                            </div>

                            <!-- OBSERVACION -->
                            <div class="checkout-block">

                                <label class="checkout-label">

                                    Observación

                                </label>

                                <textarea class="checkout-input" rows="3" placeholder="Observación opcional..."></textarea>

                            </div>

                        </div>

                        <!-- RIGHT -->
                        <div class="col-lg-5">

                            <div class="payment-summary">

                                <!-- TOTAL -->
                                <div class="payment-total-card">

                                    <div class="payment-total-label">

                                        TOTAL A PAGAR

                                    </div>

                                    <div class="payment-total-value" id="checkoutTotal"></div>
                                    <div class="payment-items" id="checkoutItems2"></div>

                                </div>

                                <!-- BALANCE DE PAGO -->
                                <div class="checkout-block" id="paymentBalanceBoxContainer">

                                    <div class="payment-balance-box" id="paymentBalanceBox">

                                        <div class="payment-balance-row">
                                            <span>Asignado</span>
                                            <span id="paymentAssignedValue">S/ 0.00</span>
                                        </div>

                                        <div class="payment-balance-row">
                                            <span id="paymentTotalLabel">Total a pagar</span>
                                            <span id="paymentTotalValue">S/ 0.00</span>
                                        </div>

                                        <div class="payment-balance-diff" id="paymentBalanceDiff">Falta S/ 0.00</div>

                                    </div>

                                </div>

                                <!-- VUELTO (solo aparece si hay una línea en Efectivo con exceso) -->
                                <div class="checkout-block" id="changeContainer" style="display:none;">

                                    <label class="checkout-label">

                                        Vuelto (Efectivo)

                                    </label>

                                    <input type="text" id="inputVuelto" class="checkout-input checkout-money"
                                        value="0.00" readonly>

                                </div>

                                <!-- FOOTER -->
                                <div class="checkout-footer">

                                    <button class="btn-finish-sale" onclick="finalizarVenta()">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        FINALIZAR VENTA
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="modal fade client-modal" id="modalClientes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <!-- HEADER -->
                <div class="modal-header client-modal-header">
                    <div>
                        <h4 class="client-modal-title">
                            <i class="fas fa-users mr-2"></i>
                            Seleccionar Cliente
                        </h4>
                        <div class="client-modal-subtitle">
                            Busca un cliente o crea uno nuevo
                        </div>
                    </div>
                    <button type="button" class="close checkout-close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body client-modal-body">
                    <!-- SEARCH -->
                    <div class="client-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchClient" placeholder="Buscar por nombre, DNI o celular...">
                    </div>
                    <!-- ACTIONS -->
                    <div class="client-actions">
                        <div class="client-counter">
                            5 clientes encontrados
                        </div>

                        <button class="btn-new-client" onclick="openNewClientModal()">
                            <i class="fas fa-plus mr-2"></i>
                            Nuevo Cliente
                        </button>
                    </div>

                    <!-- LIST -->
                    <div class="client-list" id="clientList"></div>
                </div>

            </div>

        </div>

    </div>

    <div class="modal fade mini-client-modal" id="modalNuevoCliente" tabindex="-1"aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- HEADER -->
                <div class="modal-header mini-client-header">

                    <div>

                        <h4 class="mini-client-title">

                            <i class="fas fa-user-plus mr-2"></i>

                            Nuevo Cliente

                        </h4>

                        <div class="mini-client-subtitle">

                            Registro rápido desde POS

                        </div>

                    </div>

                    <button type="button" class="close checkout-close" data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <!-- BODY -->
                <div class="modal-body mini-client-body">

                    <form id="formNuevoCliente">

                        <!-- DOCUMENTO -->
                        <div class="mini-client-group">

                            <label class="mini-client-label" id="labelDocumento">

                                DNI

                            </label>

                            <input type="text" class="mini-client-input" id="nuevoDocumento"
                                placeholder="Ingrese documento">

                        </div>

                        <!-- NOMBRE -->
                        <div class="mini-client-group">

                            <label class="mini-client-label" id="labelNombre">

                                Nombre Cliente

                            </label>

                            <input type="text" class="mini-client-input" id="nuevoNombre"
                                placeholder="Ingrese nombre">

                        </div>

                        <!-- CELULAR -->
                        <div class="mini-client-group">

                            <label class="mini-client-label">

                                Celular

                            </label>

                            <input type="text" class="mini-client-input" id="nuevoCelular" placeholder="Celular">

                        </div>

                        <!-- DIRECCION -->
                        <div class="mini-client-group" id="direccionGroup" style="display:none;">

                            <label class="mini-client-label">

                                Dirección

                            </label>

                            <textarea class="mini-client-input" rows="3" id="nuevoDireccion" placeholder="Dirección fiscal"></textarea>

                        </div>

                        <!-- BTN -->
                        <button type="button" class="btn-save-client" onclick="saveClient()">

                            <i class="fas fa-save mr-2"></i>

                            GUARDAR CLIENTE

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let lastPage = 1;
        let currentCategory = 'all';
        let currentSearch = '';
        let cart = [];
        let voucherType = 'NOTA';

        // Ambiente de facturacion del tenant: en pruebas el comprobante que
        // devuelve SUNAT no tiene validez tributaria.
        const FACTURACION_EN_PRUEBAS = @json($facturacionEnPruebas ?? true);

        // Pago dividido: metodos disponibles para cada linea + los ids
        // especiales que necesita la logica (Efectivo da vuelto, Mixto se
        // guarda en la venta cuando se usan 2+ metodos a la vez).
        // "Mixto" es una etiqueta interna (se guarda en venta.MEP_Id cuando
        // la venta usa 2+ métodos reales): no tiene sentido que el cajero la
        // elija como si fuera un método de cobro, así que no aparece en las
        // opciones de cada línea de pago.
        const METODOS_PAGO = @json($metodo_pago->reject(fn($m) => $m->MEP_Pago === 'Mixto')->map(fn($m) => ['id' => $m->MEP_Id, 'nombre' => $m->MEP_Pago])->values());
        const MIXTO_MEP_ID = @json(optional($metodo_pago->firstWhere('MEP_Pago', 'Mixto'))->MEP_Id);
        const EFECTIVO_MEP_ID = @json(optional($metodo_pago->firstWhere('MEP_Pago', 'Efectivo'))->MEP_Id);
        // Venta al credito (exclusivo de generico): se usa como
        // venta.MEP_Id cuando la venta es al credito y no hay ningun
        // adelanto (0 metodos reales usados).
        const CREDITO_MEP_ID = @json(optional($metodo_pago->firstWhere('MEP_Pago', 'Crédito'))->MEP_Id);
        let paymentLines = [];
        // 'CONTADO' o 'CREDITO'. Controla si finalizarVenta() sigue el
        // flujo de siempre o el de venta a credito (cuenta por cobrar).
        let tipoVenta = 'CONTADO';

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        const showToast = (icon, title) => {
            Toast.fire({
                icon,
                title
            });
        };

        // Extrae un mensaje legible de una respuesta AJAX fallida: primero
        // busca el formato propio de este proyecto ({error: "..."}), y si
        // no esta, cae al formato por defecto de Laravel cuando falla una
        // validacion ({message: "...", errors: {campo: ["..."]}}), para no
        // esconder el motivo real detras de un mensaje generico.
        function extraerMensajeError(xhr, mensajePorDefecto) {
            let data = xhr.responseJSON;

            if (!data) {
                return mensajePorDefecto;
            }

            if (data.error) {
                return data.error;
            }

            if (data.errors) {
                let primerCampo = Object.keys(data.errors)[0];
                if (primerCampo && data.errors[primerCampo] && data.errors[primerCampo][0]) {
                    return data.errors[primerCampo][0];
                }
            }

            if (data.message) {
                return data.message;
            }

            return mensajePorDefecto;
        }

        $(document).ready(function() {

            $('body').addClass('sidebar-collapse');
            $('.category-btn').on('click', function() {
                $('.category-btn').removeClass('active');
                $(this).addClass('active');
                currentCategory = $(this).data('id');
                currentPage = 1;
                loadProducts();
            });
            $('.search-input').on('keyup', function() {
                currentSearch = $(this).val();
                currentPage = 1;
                loadProducts();
            });
            $('#btnLoadMore').on('click', function() {
                currentPage++;
                loadProducts(currentPage);
            });
            loadProducts();

            $('#searchClient').on('keyup', function() {
                let value = $(this).val();
                loadClients(value);
            });

        })

        function openNewClientModal() {
            $('#modalNuevoCliente').modal('show');
            prepareClientForm();
        }

        function prepareClientForm() {
            // FACTURA
            if (voucherType == 'FACTURA') {
                $('#labelDocumento').html('RUC');
                $('#labelNombre').html('Razón Social');
                $('#nuevoDocumento').attr('placeholder','Ingrese RUC');
                $('#nuevoNombre').attr('placeholder','Razón Social');
                $('#direccionGroup').show();
            }
            // BOLETA / NOTA
            else {
                $('#labelDocumento').html('DNI');
                $('#labelNombre').html('Nombre Cliente');
                $('#nuevoDocumento').attr('placeholder', 'Ingrese DNI');
                $('#nuevoNombre').attr('placeholder','Nombre completo');
                $('#direccionGroup').hide();
            }

        }

        function saveClient(){
            let documento = $('#nuevoDocumento').val();
            let nombre = $('#nuevoNombre').val();
            let celular = $('#nuevoCelular').val();
            let direccion = $('#nuevoDireccion').val();
            // VALIDACION SIMPLE
            if(documento == '' || nombre == ''){
                showToast('warning', 'Complete los datos');
                return;
            }
            let tipoDocumento = documento.length > 8 ? 'RUC' : 'DNI';
            $.ajax({
                url: "{{ route('tenant.ventas.venta.createCliente') }}",
                method: 'POST',
                data: {
                    CLI_TipoDocumento: tipoDocumento,
                    CLI_NumDocumento: documento,
                    CLI_Nombre: nombre,
                    CLI_Celular: celular,
                    CLI_Direccion: direccion,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response){
                    selectClient(
                        response.CLI_Nombre,
                        response.CLI_NumDocumento,
                        response.CLI_Id
                    );
                    // CERRAR MODALES
                    $('#modalNuevoCliente').modal('hide');
                    $('#modalClientes').modal('hide');
                }

            });

        }

        function openClientModal() {
            loadClients();
            $('#modalClientes').modal('show');
            // FACTURA
            if (voucherType == 'FACTURA') {
                $('#searchClient').attr(
                    'placeholder',
                    'Buscar cliente con RUC...'
                );
            } else {
                $('#searchClient').attr(
                    'placeholder',
                    'Buscar cliente...'
                );

            }
        }

        function selectClient(name, document, id) {
            $('#clientName').html(name);
            $('#clientSubtitle').html(document);
            $('#cliente_id').val(id);
            $('#modalClientes').modal('hide');
        }

        function toggleLoadMore() {
            if (currentPage >= lastPage) {
                $('#btnLoadMore').hide();
            } else {
                $('#btnLoadMore').show();
            }
        }

        function loadClients(search = '') {
            $.ajax({
                url: "{{ route('tenant.ventas.venta.searchClientes') }}",
                method: "GET",
                data: {
                    search: search
                },
                success: function(response) {
                    renderClients(response);
                }
            });

        }

        function renderClients(clients) {
            let html = '';
            clients.forEach(client => {
                let initials = client.CLI_Nombre.substring(0, 2).toUpperCase();
                html += `
                <div class="client-item">
                    <div class="client-item-left">
                        <div class="client-item-avatar">
                            ${initials}
                        </div>
                        <div>
                            <div class="client-item-name">
                                ${client.CLI_Nombre}
                            </div>
                            <div class="client-item-document">
                                ${client.CLI_NumDocumento ?? '-'}
                            </div>
                            <div class="client-item-phone">
                                ${client.CLI_Celular ?? '-'}
                            </div>
                        </div>
                    </div>
                    <button class="btn-select-client"
                        onclick="selectClient(
                            '${client.CLI_Nombre}',
                            '${client.CLI_NumDocumento}',
                            '${client.CLI_Id}'
                        )">
                        Seleccionar

                    </button>
                </div>

                `;

            });

            $('#clientList').html(html);

        }


        function loadProducts(page = 1) {
            $.ajax({
                url: "{{ route('tenant.ventas.venta.productos') }}",
                method: "GET",
                data: {
                    page: page,
                    categoria: currentCategory,
                    search: currentSearch
                },
                success: function(response) {
                    lastPage = response.last_page;
                    renderProducts(response.data, page);
                    toggleLoadMore();
                }
            });
        }

        function renderProducts(products, page) {
            let html = '';
            products.forEach(product => {
                let image = product.PRO_Imagen ?
                    `/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/producto/${product.PRO_Imagen}` :
                    `/images/imagen_default.png`;
                html += `
                <div class="product-card">
                    <div class="product-image">
                        <img src="${image}">
                    </div>

                    <div class="product-name">${product.PRO_Nombre}</div>
                    <div class="product-description">${product.PRO_Descripcion ?? ''}</div>
                    <div class="product-footer">
                        <div>
                            <div class="product-price">S/ ${product.PRO_PrecioBaseVenta}</div>
                            <div class="product-stock">Stock ${product.PRO_Cantidad}</div>
                        </div>
                        <button class="btn btn-sm btn-primary rounded-circle"
                            onclick='addToCart(${JSON.stringify(product)})'>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                `;
            });

            if (page == 1) {
                $('#productsGrid').html(html);
            } else {
                $('#productsGrid').append(html);
            }

        }

        function addToCart(product) {
            // BUSCAR SI YA EXISTE
            let existing = cart.find(item => item.PRO_Id == product.PRO_Id);
            // SI EXISTE
            if (existing) {
                existing.quantity++;
            } else {
                // NUEVO PRODUCTO
                cart.push({
                    ...product,
                    quantity: 1
                });
            }

            renderCart();

        }

        function renderCart() {
            let html = '';
            let total = 0;
            let totalItems = 0;
            cart.forEach(item => {

                let subtotal = item.quantity * parseFloat(item.PRO_PrecioBaseVenta);
                total += subtotal;
                totalItems += item.quantity;

                let image = item.PRO_Imagen ?
                    `/storage/{{ tenant('tipo_negocio') }}/{{ tenant('id') }}/archivos/producto/${item.PRO_Imagen}` :
                    `/images/imagen_default.png`;

                html += `
                <div class="cart-item">
                    <div class="cart-image">
                        <img src="${image}">
                    </div>
                    <div class="cart-info">
                        <div class="cart-name">${item.PRO_Nombre}</div>
                        <div class="cart-price">Unit. S/ ${item.PRO_PrecioBaseVenta}</div>
                        <div class="cart-bottom">
                            <div class="qty-control">
                                <button class="qty-btn" onclick="decreaseQty(${item.PRO_Id})">-</button>
                                <span class="qty-value" >${item.quantity}</span>
                                <button class="qty-btn" onclick="increaseQty(${item.PRO_Id})">+</button>
                            </div>
                            <div class="cart-total">S/ ${subtotal.toFixed(2)}</div>
                        </div>
                    </div>
                    <button class="btn-remove" onclick="removeCart(${item.PRO_Id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                `;

            });

            $('#cartItems').html(html);
            $('#checkoutItems').html(`${totalItems} productos`);
            $('#checkoutItems2').html(`${totalItems} productos`);
            // TOTAL
            $('#cartTotal').html(`S/ ${total.toFixed(2)}`);
            $('#checkoutTotal').html(`S/ ${total.toFixed(2)}`);


        }

        function increaseQty(id) {
            let item = cart.find(x => x.PRO_Id == id);
            item.quantity++;
            renderCart();
        }

        function decreaseQty(id) {
            let item = cart.find(x => x.PRO_Id == id);
            if (item.quantity > 1) {
                item.quantity--;
            } else {
                cart = cart.filter(x => x.PRO_Id != id);
            }
            renderCart();
        }

        function removeCart(id) {
            cart = cart.filter(x => x.PRO_Id != id);
            renderCart();
        }


        function changeVoucher(button, type) {
            voucherType = type;
            $('.voucher-option').removeClass('active');
            $(button).addClass('active');
            // RESET CLIENT UI
            updateClientSection();
        }

        function updateClientSection() {

            let clientId = $('#cliente_id').val();

            // NOTA VENTA
            if (voucherType == 'NOTA') {
                // SIN CLIENTE
                if (!clientId) {
                    $('#clientName').html('SIN CLIENTE');
                    $('#clientSubtitle').html('Venta rápida (opcional)');
                }
            }

            // BOLETA
            if (voucherType == 'BOLETA') {
                if (!clientId) {
                    $('#clientName').html('BUSCAR CLIENTE');
                    $('#clientSubtitle').html('DNI recomendado');
                }
            }

            // FACTURA
            if (voucherType == 'FACTURA') {
                if (!clientId) {
                    $('#clientName').html('CLIENTE OBLIGATORIO');
                    $('#clientSubtitle').html('Seleccione cliente con RUC');
                }
            }

            // CREDITO (exclusivo de generico): siempre requiere cliente,
            // sin importar el tipo de comprobante.
            if (tipoVenta == 'CREDITO' && voucherType != 'FACTURA') {
                if (!clientId) {
                    $('#clientName').html('CLIENTE OBLIGATORIO');
                    $('#clientSubtitle').html('Venta al crédito: requiere cliente');
                }
            }
        }


        // ==================================================
        // PAGO DIVIDIDO: una venta puede pagarse con 1 o varios
        // métodos a la vez, siempre que la suma cuadre con el total.
        // ==================================================

        function renderPaymentRows() {
            let html = '';

            paymentLines.forEach((line, idx) => {
                let opciones = METODOS_PAGO.map(m =>
                    `<option value="${m.id}" ${String(m.id) === String(line.metodo) ? 'selected' : ''}>${m.nombre}</option>`
                ).join('');

                html += `
                <div class="payment-method-row">
                    <select class="payment-method-select" onchange="actualizarLineaPago(${idx}, 'metodo', this.value)">
                        ${opciones}
                    </select>
                    <input type="number" step="0.01" min="0" class="payment-method-amount" placeholder="0.00"
                        value="${line.monto}" oninput="actualizarLineaPago(${idx}, 'monto', this.value)">
                    ${paymentLines.length > 1 ? `<button type="button" class="btn-remove-payment" onclick="quitarLineaPago(${idx})"><i class="fas fa-times"></i></button>` : ''}
                </div>`;
            });

            $('#paymentRows').html(html);
            recalcularBalancePago();
        }

        function agregarLineaPago() {
            // Por defecto sugiere un método que todavía no se haya usado en
            // esta venta, para que la mayoría de las veces no haga falta ni
            // tocar el select.
            let usados = paymentLines.map(l => String(l.metodo));
            let siguiente = METODOS_PAGO.find(m => !usados.includes(String(m.id))) || METODOS_PAGO[0];

            paymentLines.push({
                metodo: siguiente ? siguiente.id : EFECTIVO_MEP_ID,
                monto: '0.00'
            });

            renderPaymentRows();
        }

        function quitarLineaPago(idx) {
            if (paymentLines.length <= 1) return;
            paymentLines.splice(idx, 1);
            renderPaymentRows();
        }

        function actualizarLineaPago(idx, campo, valor) {
            paymentLines[idx][campo] = valor;
            recalcularBalancePago();
        }

        // Compara lo asignado en las líneas de pago contra el total del
        // carrito. Habilita "FINALIZAR VENTA" solo cuando cuadra exacto, o
        // cuando sobra dinero y hay una línea en Efectivo (eso se vuelve
        // vuelto). Devuelve los números ya calculados para que
        // finalizarVenta() no tenga que recalcular nada por su cuenta.
        function recalcularBalancePago() {
            // Venta al credito (exclusivo de generico): las lineas de pago
            // representan solo el adelanto, no el total de la venta.
            let total = tipoVenta === 'CREDITO'
                ? (parseFloat($('#inputAdelanto').val()) || 0)
                : (parseFloat($('#cartTotal').text().replace('S/', '').trim()) || 0);
            let asignado = paymentLines.reduce((acc, l) => acc + (parseFloat(l.monto) || 0), 0);
            let diferencia = Math.round((asignado - total) * 100) / 100;

            $('#paymentAssignedValue').text('S/ ' + asignado.toFixed(2));
            $('#paymentTotalValue').text('S/ ' + total.toFixed(2));
            $('#paymentTotalLabel').text(tipoVenta === 'CREDITO' ? 'Adelanto a cubrir' : 'Total a pagar');

            let hayEfectivo = paymentLines.some(l =>
                String(l.metodo) === String(EFECTIVO_MEP_ID) && (parseFloat(l.monto) || 0) > 0
            );

            let box = $('#paymentBalanceBox');
            let diffEl = $('#paymentBalanceDiff');
            let puedeFinalizar = false;

            if (Math.abs(diferencia) <= 0.01) {
                diffEl.text('Cuadra exacto ✓').css('color', '#22C55E');
                box.removeClass('is-error').addClass('is-ok');
                $('#changeContainer').slideUp(100);
                puedeFinalizar = true;
                diferencia = 0;
            } else if (diferencia < 0) {
                diffEl.text('Falta S/ ' + Math.abs(diferencia).toFixed(2)).css('color', '#EF4444');
                box.removeClass('is-ok').addClass('is-error');
                $('#changeContainer').slideUp(100);
                puedeFinalizar = false;
            } else if (hayEfectivo && tipoVenta !== 'CREDITO') {
                diffEl.text('Vuelto: S/ ' + diferencia.toFixed(2)).css('color', '#22C55E');
                box.removeClass('is-error').addClass('is-ok');
                $('#inputVuelto').val(diferencia.toFixed(2));
                $('#changeContainer').slideDown(100);
                puedeFinalizar = true;
            } else if (tipoVenta === 'CREDITO') {
                // Un adelanto no da vuelto: si sobra, es un error a corregir.
                diffEl.text('Sobran S/ ' + diferencia.toFixed(2) + ' respecto al adelanto indicado')
                    .css('color', '#EF4444');
                box.removeClass('is-ok').addClass('is-error');
                $('#changeContainer').slideUp(100);
                puedeFinalizar = false;
            } else {
                diffEl.text('Sobran S/ ' + diferencia.toFixed(2) + ' — agrega una línea en Efectivo para dar vuelto')
                    .css('color', '#EF4444');
                box.removeClass('is-ok').addClass('is-error');
                $('#changeContainer').slideUp(100);
                puedeFinalizar = false;
            }

            $('.btn-finish-sale').prop('disabled', !puedeFinalizar);

            return { total, asignado, diferencia, puedeFinalizar };
        }

        // ==================================================
        // VENTA AL CREDITO (exclusivo de generico): adelanto opcional +
        // cuotas opcionales. No reemplaza el flujo de contado de arriba.
        // ==================================================

        function changeTipoVenta(button, tipo) {
            tipoVenta = tipo;
            $('.tipoventa-option').removeClass('active');
            $(button).addClass('active');
            actualizarUICredito();
        }

        function actualizarUICredito() {
            if (tipoVenta === 'CREDITO') {
                $('#creditoBlock').slideDown(100);
            } else {
                $('#creditoBlock').slideUp(100);
                $('#chkDefinirCuotas').prop('checked', false);
                $('#cuotasConfig').hide();
                $('#inputAdelanto').val('0.00');
            }

            updateClientSection();
            onAdelantoChange();
        }

        function onDefinirCuotasChange() {
            if ($('#chkDefinirCuotas').is(':checked')) {
                $('#cuotasConfig').slideDown(100);
                actualizarPreviewCuotas();
            } else {
                $('#cuotasConfig').slideUp(100);
            }
        }

        // El adelanto de una venta al credito nunca puede superar el
        // total del carrito; si el adelanto es 0, no hace falta indicar
        // ningun metodo de pago (se va todo a la cuenta por cobrar).
        function onAdelantoChange() {
            if (tipoVenta !== 'CREDITO') {
                return;
            }

            let total = parseFloat($('#cartTotal').text().replace('S/', '').trim()) || 0;
            let adelanto = parseFloat($('#inputAdelanto').val()) || 0;

            if (adelanto > total) {
                adelanto = total;
                $('#inputAdelanto').val(adelanto.toFixed(2));
            }

            if (adelanto <= 0) {
                $('#paymentMethodsBlock').hide();
                $('#paymentBalanceBoxContainer').hide();
                $('#changeContainer').hide();
                $('.btn-finish-sale').prop('disabled', false);
            } else {
                $('#paymentMethodsBlock').show();
                $('#paymentBalanceBoxContainer').show();
                recalcularBalancePago();
            }

            actualizarPreviewCuotas();
        }

        // Vista previa de como quedarian las cuotas (numero, fecha de
        // vencimiento y monto) ANTES de finalizar la venta, calculada con
        // la misma logica que usa el servidor al guardar (reparto en
        // partes iguales, la ultima cuota absorbe el redondeo). Es solo
        // informativa: el servidor vuelve a calcular todo por su cuenta.
        function actualizarPreviewCuotas() {
            if (tipoVenta !== 'CREDITO' || !$('#chkDefinirCuotas').is(':checked')) {
                $('#cuotasPreview').html('');
                return;
            }

            let total = parseFloat($('#cartTotal').text().replace('S/', '').trim()) || 0;
            let adelanto = parseFloat($('#inputAdelanto').val()) || 0;
            let saldo = Math.max(0, Math.round((total - adelanto) * 100) / 100);
            let numCuotas = parseInt($('#inputNumCuotas').val()) || 0;
            let frecuenciaDias = parseInt($('#inputFrecuenciaDias').val()) || 0;

            if (numCuotas < 1 || frecuenciaDias < 1) {
                $('#cuotasPreview').html('');
                return;
            }

            let montoBase = Math.round((saldo / numCuotas) * 100) / 100;
            let acumulado = 0;
            let hoy = new Date();
            let html = '';

            for (let i = 1; i <= numCuotas; i++) {
                let esUltima = i === numCuotas;
                let monto = esUltima ? Math.round((saldo - acumulado) * 100) / 100 : montoBase;
                acumulado = Math.round((acumulado + monto) * 100) / 100;

                let vencimiento = new Date(hoy);
                vencimiento.setDate(vencimiento.getDate() + i * frecuenciaDias);
                let fechaTexto = vencimiento.toLocaleDateString('es-PE');

                html += `
                <div class="cuota-preview-row">
                    <span class="cuota-preview-numero">Cuota ${i}</span>
                    <span>${fechaTexto}</span>
                    <span class="cuota-preview-monto">S/ ${monto.toFixed(2)}</span>
                </div>`;
            }

            $('#cuotasPreview').html(html);
        }

        function openCheckout() {
            if(cart.length == 0){
                showToast('warning', 'No hay productos en el carrito');
                return;
            }

            // Arranca en Contado con una sola línea en Efectivo por el
            // total completo: una venta simple sigue siendo de un solo
            // clic, igual que antes.
            let total = parseFloat($('#cartTotal').text().replace('S/', '').trim()) || 0;
            paymentLines = [{ metodo: EFECTIVO_MEP_ID, monto: total.toFixed(2) }];
            renderPaymentRows();

            // Reset del bloque de Crédito por si quedó abierto de una
            // venta anterior.
            tipoVenta = 'CONTADO';
            $('.tipoventa-option').removeClass('active').first().addClass('active');
            $('#creditoBlock').hide();
            $('#chkDefinirCuotas').prop('checked', false);
            $('#cuotasConfig').hide();
            $('#inputAdelanto').val('0.00');
            $('#cuotasPreview').html('');

            $('#modalCheckout').modal('show');
        }

        function finalizarVenta(){
            if(cart.length == 0){
                showToast('warning', 'No hay productos en el carrito');
                return;
            }

            // Venta al credito (exclusivo de generico) sigue un flujo
            // aparte: cliente obligatorio, adelanto opcional, cuotas
            // opcionales, y crea una cuenta por cobrar al final.
            if (tipoVenta === 'CREDITO') {
                finalizarVentaCredito();
                return;
            }

            // FACTURA requiere cliente
            if(voucherType == 'FACTURA' && !$('#cliente_id').val()){
                showToast('warning', 'Debe seleccionar cliente');
                return;
            }

            // Los métodos de pago deben sumar exactamente el total (o
            // sobrar solo si hay una línea en Efectivo, que absorbe el
            // vuelto). recalcularBalancePago() ya deja los botones/colores
            // correctos; aquí solo se vuelve a comprobar antes de enviar.
            let balance = recalcularBalancePago();
            if (!balance.puedeFinalizar) {
                showToast('warning', 'Los métodos de pago no cuadran con el total a pagar');
                return;
            }

            // Si se usan 2+ métodos hace falta el metodo_pago "Mixto" (lo
            // crea una migración); sin él no hay a qué MEP_Id asociar la
            // venta y el guardado fallaría con un error de base de datos
            // poco claro para el cajero.
            if (paymentLines.length > 1 && !MIXTO_MEP_ID) {
                Swal.fire({
                    icon: 'error',
                    title: 'Falta configuración',
                    text: 'No se encontró el método de pago "Mixto". Pide al administrador que corra las migraciones pendientes antes de dividir un pago en varios métodos.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            //  DATA

            let data = {
                cliente_id: $('#cliente_id').val(),
                comprobante: voucherType,
                metodo_pago: paymentLines.length === 1 ? paymentLines[0].metodo : MIXTO_MEP_ID,
                pago_recibido: balance.asignado.toFixed(2),
                vuelto: balance.diferencia.toFixed(2),
                observacion: $('#observacion').val(),
                productos: cart,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // LOADING
            $('.btn-finish-sale').prop('disabled', true)
                .html(`Procesando venta...`);

            // AJAX
            $.ajax({
                url: "{{ route('tenant.ventas.venta.store') }}",
                method: "POST",
                data: data,
                success: function(response){
                    // Guarda el detalle de cuánto se pagó con cada método
                    // (no bloquea el éxito de la venta si esto llegara a
                    // fallar: la venta ya quedó registrada).
                    guardarDetallePago(response.venta_id, balance.diferencia);

                    // Si esta venta partio de "Convertir a Venta" de una
                    // cotizacion, la marca como convertida (no bloquea el
                    // exito de la venta si esto llegara a fallar).
                    marcarCotizacionConvertida(response.venta_id);

                    // SUCCESS
                    // En el ambiente de pruebas de SUNAT el comprobante no
                    // tiene validez tributaria, asi que no se ofrece el enlace
                    // de impresion: solo se avisa en que quedo la venta.
                    var esElectronico = (voucherType === 'BOLETA' || voucherType === 'FACTURA');
                    var enPruebas = FACTURACION_EN_PRUEBAS;
                    var pie;

                    if (esElectronico && enPruebas) {
                        pie = '<span style="color:#B45309;"><i class="fa fa-flask"></i> ' +
                              'Ambiente de pruebas: el comprobante se envio a SUNAT en modo BETA ' +
                              'y no tiene validez tributaria, por eso no se puede imprimir.</span>';
                    } else {
                        // Usa la ruta "-seguro" (exclusiva de generico): la
                        // original revienta con toda venta al credito porque
                        // consulta una tabla que no existe ('cuentas_por_cobrar').
                        pie = '<a title="TICKET" target="_blank" href="/tenant/ventas/venta/' + response.venta_id +
                              '/ticket-seguro" class="btn btn-danger btn-sm" style="margin-left: 5px;">' +
                              '<i class="fa fa fa-print"></i> ¿Desea imprimir documento?</a>';
                    }

                    Swal.fire({
                            icon: "success",
                            title: "Venta Generada",
                            text: esElectronico
                                ? "Se realizo el pago. El comprobante se esta enviando a SUNAT."
                                : "Se realizo el pago correctamente!",
                            confirmButtonText: "Aceptar",
                            footer: pie,
                            allowOutsideClick: false, // Deshabilita clics fuera del alert
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                //location.reload();
                                resetPOS();
                                $('#modalCheckout').modal('hide');
                            } else if (result.isDenied) {
                                $('#modalCheckout').modal('hide');
                                // Swal.fire("Changes are not saved", "", "info");
                            }
                        });

                    // LIMPIAR
                    //resetPOS();
                    //  CERRAR MODAL
                    // $('#modalCheckout').modal('hide');
                    // OPCIONAL
                    // imprimirTicket(response.id);
                },
                error: function(xhr){
                    // El servidor explica por que no se pudo registrar
                    // (cliente sin RUC, caja cerrada, sin stock...).
                    var motivo = (xhr.responseJSON && xhr.responseJSON.error)
                        ? xhr.responseJSON.error
                        : 'Error al registrar venta';
                    Swal.fire({
                        icon: 'error',
                        title: 'No se registro la venta',
                        text: motivo,
                        confirmButtonText: 'Entendido'
                    });
                },
                complete: function(){
                    $('.btn-finish-sale').prop('disabled', false).html(`FINALIZAR VENTA`);
                }
            });
        }

        // Guarda cuánto se pagó con cada método (tabla venta_pago). La
        // línea en Efectivo, si la hay, guarda su monto ya neto de vuelto
        // (lo que realmente entra a la venta), no el efectivo bruto
        // entregado — así la suma guardada siempre da exacto el total.
        function guardarDetallePago(ventaId, vuelto) {
            let vueltoPendiente = vuelto > 0 ? vuelto : 0;

            let pagos = paymentLines.map(l => {
                let monto = parseFloat(l.monto) || 0;
                if (vueltoPendiente > 0 && String(l.metodo) === String(EFECTIVO_MEP_ID)) {
                    monto = monto - vueltoPendiente;
                    vueltoPendiente = 0;
                }
                return { metodo_pago_id: l.metodo, monto: monto.toFixed(2) };
            }).filter(p => parseFloat(p.monto) > 0);

            $.ajax({
                url: "{{ route('tenant.ventas.venta.pagos.store') }}",
                method: "POST",
                data: {
                    venta_id: ventaId,
                    pagos: pagos,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr) {
                    console.warn('No se pudo guardar el detalle de pago dividido de la venta ' + ventaId, xhr);
                    showToast('warning', 'La venta se registró, pero no se pudo guardar el detalle por método de pago.');
                }
            });
        }

        // Flujo de "Finalizar Venta" cuando Tipo de Venta = Crédito
        // (exclusivo de generico). Llama al mismo tenant.ventas.venta.store
        // de siempre (sin tocarlo) con VEN_TipoPago=2, y si tiene éxito
        // registra la cuenta por cobrar (y el detalle del adelanto, si
        // hubo) en las rutas nuevas de este módulo.
        function finalizarVentaCredito(){
            if (!$('#cliente_id').val()) {
                showToast('warning', 'Una venta al crédito requiere seleccionar un cliente');
                return;
            }

            let total = parseFloat($('#cartTotal').text().replace('S/', '').trim()) || 0;
            let adelanto = parseFloat($('#inputAdelanto').val()) || 0;

            if (adelanto < 0 || adelanto > total + 0.01) {
                showToast('warning', 'El adelanto debe estar entre 0 y el total de la venta');
                return;
            }

            let tieneCuotas = $('#chkDefinirCuotas').is(':checked');
            let numCuotas = parseInt($('#inputNumCuotas').val()) || 0;
            let frecuenciaDias = parseInt($('#inputFrecuenciaDias').val()) || 0;

            if (tieneCuotas && (numCuotas < 1 || numCuotas > 60)) {
                showToast('warning', 'El número de cuotas debe estar entre 1 y 60');
                return;
            }

            if (tieneCuotas && (frecuenciaDias < 1 || frecuenciaDias > 365)) {
                showToast('warning', 'La frecuencia de cuotas debe estar entre 1 y 365 días');
                return;
            }

            let balance = { asignado: 0, diferencia: 0, puedeFinalizar: true };

            if (adelanto > 0) {
                balance = recalcularBalancePago();
                if (!balance.puedeFinalizar) {
                    showToast('warning', 'Los métodos de pago del adelanto no cuadran con el adelanto indicado');
                    return;
                }

                if (paymentLines.length > 1 && !MIXTO_MEP_ID) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Falta configuración',
                        text: 'No se encontró el método de pago "Mixto". Pide al administrador que corra las migraciones pendientes antes de dividir un adelanto en varios métodos.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
            } else if (!CREDITO_MEP_ID) {
                Swal.fire({
                    icon: 'error',
                    title: 'Falta configuración',
                    text: 'No se encontró el método de pago "Crédito". Pide al administrador que corra las migraciones pendientes antes de vender al crédito sin adelanto.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            let metodoPago = adelanto <= 0
                ? CREDITO_MEP_ID
                : (paymentLines.length === 1 ? paymentLines[0].metodo : MIXTO_MEP_ID);

            let data = {
                cliente_id: $('#cliente_id').val(),
                comprobante: voucherType,
                VEN_TipoPago: 2,
                metodo_pago: metodoPago,
                pago_recibido: adelanto.toFixed(2),
                vuelto: '0.00',
                observacion: $('#observacion').val(),
                productos: cart,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $('.btn-finish-sale').prop('disabled', true).html(`Procesando venta...`);

            $.ajax({
                url: "{{ route('tenant.ventas.venta.store') }}",
                method: "POST",
                data: data,
                success: function(response){
                    // Ninguno de los pasos siguientes invalida la venta si
                    // llegara a fallar: la venta ya quedó registrada.
                    if (adelanto > 0) {
                        guardarDetallePago(response.venta_id, 0);
                    }

                    guardarCuentaCobrar(response.venta_id, adelanto, tieneCuotas, numCuotas, frecuenciaDias);

                    // Si esta venta partio de "Convertir a Venta" de una
                    // cotizacion, la marca como convertida.
                    marcarCotizacionConvertida(response.venta_id);

                    // Esta venta SIEMPRE es al credito (VEN_TipoPago=2), asi
                    // que usa la ruta "-seguro": la original revienta aqui
                    // seguro porque consulta una tabla que no existe
                    // ('cuentas_por_cobrar').
                    var pie = '<a title="TICKET" target="_blank" href="/tenant/ventas/venta/' + response.venta_id +
                              '/ticket-seguro" class="btn btn-danger btn-sm" style="margin-left: 5px;">' +
                              '<i class="fa fa fa-print"></i> ¿Desea imprimir documento?</a>';

                    Swal.fire({
                            icon: "success",
                            title: "Venta al Crédito Generada",
                            text: adelanto > 0
                                ? ('Se registró el adelanto de S/ ' + adelanto.toFixed(2) + '. El resto queda como cuenta por cobrar.')
                                : 'La venta completa quedó registrada como cuenta por cobrar.',
                            confirmButtonText: "Aceptar",
                            footer: pie,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            resetPOS();
                            $('#modalCheckout').modal('hide');
                        });
                },
                error: function(xhr){
                    var motivo = (xhr.responseJSON && xhr.responseJSON.error)
                        ? xhr.responseJSON.error
                        : 'Error al registrar venta';
                    Swal.fire({
                        icon: 'error',
                        title: 'No se registro la venta',
                        text: motivo,
                        confirmButtonText: 'Entendido'
                    });
                },
                complete: function(){
                    $('.btn-finish-sale').prop('disabled', false).html(`FINALIZAR VENTA`);
                }
            });
        }

        // Crea la cuenta por cobrar (y su plan de cuotas, si se definió)
        // justo despues de que la venta al credito ya se guardo.
        function guardarCuentaCobrar(ventaId, adelanto, tieneCuotas, numCuotas, frecuenciaDias) {
            let adelantoPagos = [];

            if (adelanto > 0) {
                adelantoPagos = paymentLines
                    .map(l => ({ metodo_pago_id: l.metodo, monto: (parseFloat(l.monto) || 0).toFixed(2) }))
                    .filter(p => parseFloat(p.monto) > 0);
            }

            $.ajax({
                url: "{{ route('tenant.ventas.venta.cuentacobrar.store') }}",
                method: "POST",
                data: {
                    venta_id: ventaId,
                    adelanto_pagos: adelantoPagos,
                    tiene_cuotas: tieneCuotas ? 1 : 0,
                    num_cuotas: tieneCuotas ? numCuotas : null,
                    frecuencia_dias: tieneCuotas ? frecuenciaDias : null,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr) {
                    console.warn('No se pudo registrar la cuenta por cobrar de la venta ' + ventaId, xhr);
                    var motivo = extraerMensajeError(xhr, 'No se pudo crear la cuenta por cobrar.');
                    showToast('warning', 'La venta se registró, pero: ' + motivo);
                }
            });
        }

        function resetPOS(){
            // CART
            cart = [];
            renderCart();
            // CLIENTE
            $('#cliente_id').val('');
            updateClientSection();
            // PAGO
            paymentLines = [{ metodo: EFECTIVO_MEP_ID, monto: '0.00' }];
            renderPaymentRows();
            // CREDITO (exclusivo de generico)
            tipoVenta = 'CONTADO';
            $('.tipoventa-option').removeClass('active').first().addClass('active');
            $('#creditoBlock').hide();
            $('#chkDefinirCuotas').prop('checked', false);
            $('#cuotasConfig').hide();
            $('#inputAdelanto').val('0.00');
            $('#cuotasPreview').html('');
            // COTIZACION DE ORIGEN (exclusivo de generico): una venta nueva
            // ya no esta ligada a la cotizacion que se pudo haber cargado
            // antes, aunque la pagina siga abierta.
            limpiarCotizacionOrigen();
        }

        // =====================================================
        // "CONVERTIR A VENTA" DE UNA COTIZACION (exclusivo de generico)
        // =====================================================
        //
        // Si se entra a esta pagina con ?desde_cotizacion=ID (boton
        // "Convertir a Venta" del modulo de Cotizaciones), se precarga el
        // carrito con los mismos productos/cantidades/precios de esa
        // cotizacion -- pero el cajero sigue pudiendo agregar, quitar o
        // modificar productos con total libertad antes de cobrar, como en
        // cualquier venta normal. Si la venta se completa, se avisa al
        // modulo de Cotizaciones para que la marque como convertida
        // (COT_Estado = Aprobada) y quede enlazada a esa venta.
        //
        // OJO con el descuento: VentaController::store() (compartido, no se
        // toca) guarda SIEMPRE DEV_Descuento = 0 -- no existe forma de
        // cargar un descuento por linea a traves de ese endpoint. Para que
        // el total de cada linea siga cuadrando exacto con lo que decia la
        // cotizacion, el descuento se "esconde" adentro del precio unitario
        // que se manda (precio efectivo = subtotal de la linea / cantidad),
        // en vez de perderse silenciosamente.
        function cargarDesdeCotizacion(cotizacionId) {
            $.get('{{ tenant_url('tenant.ventas.cotizacion.show.generico', ['cotizacion' => ':id']) }}'.replace(':id', cotizacionId))
                .done(function(data) {
                    var cot = data.cotizacion;

                    if (parseInt(cot.COT_Estado) !== 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cotización no disponible',
                            text: 'Esta cotización ya no está Pendiente (fue aprobada, rechazada o anulada), así que no se puede cargar en una venta nueva.'
                        });
                        return;
                    }

                    if (!data.detalles || data.detalles.length === 0) {
                        Swal.fire({ icon: 'warning', title: 'La cotización no tiene productos' });
                        return;
                    }

                    cart = data.detalles.map(function(d) {
                        var cantidad = parseFloat(d.DCOT_Cantidad);
                        var subtotalLinea = (cantidad * parseFloat(d.DCOT_PrecioUnitario)) - parseFloat(d.DCOT_Descuento || 0);
                        var precioEfectivo = cantidad > 0 ? (subtotalLinea / cantidad) : 0;

                        return {
                            PRO_Id: d.PRO_Id,
                            PRO_Nombre: d.PRO_Nombre,
                            PRO_Imagen: null,
                            PRO_PrecioBaseVenta: precioEfectivo.toFixed(2),
                            quantity: cantidad
                        };
                    });

                    renderCart();

                    if (cot.CLI_Id) {
                        selectClient(cot.CLI_Nombre || 'Cliente', cot.CLI_NumDocumento || '', cot.CLI_Id);
                    }

                    $('#cotizacionOrigenId').val(cot.COT_Id);
                    $('#cotizacionOrigenTexto').text(
                        'Cargada desde la Cotización #' + String(cot.COT_Id).padStart(6, '0') +
                        '. Puedes seguir agregando, quitando o modificando productos antes de cobrar.'
                    );
                    $('#avisoCotizacionOrigen').show();

                    showToast('success', 'Cotización cargada en el carrito');
                })
                .fail(function() {
                    Swal.fire({ icon: 'error', title: 'No se pudo cargar la cotización' });
                });
        }

        function limpiarCotizacionOrigen() {
            $('#cotizacionOrigenId').val('');
            $('#avisoCotizacionOrigen').hide();
        }

        // Se llama despues de una venta exitosa. No bloquea el flujo de la
        // venta si esto falla (la venta ya quedo registrada de por si) --
        // mismo criterio que guardarDetallePago()/guardarCuentaCobrar().
        function marcarCotizacionConvertida(ventaId) {
            var cotizacionId = $('#cotizacionOrigenId').val();

            if (!cotizacionId) {
                return;
            }

            $.ajax({
                url: '{{ tenant_url('tenant.ventas.cotizacion.marcarConvertida.generico', ['cotizacion' => ':id']) }}'.replace(':id', cotizacionId),
                method: 'POST',
                data: {
                    venta_id: ventaId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr) {
                    console.warn('No se pudo marcar la cotización ' + cotizacionId + ' como convertida', xhr);
                    showToast('warning', 'La venta se registró, pero no se pudo actualizar el estado de la cotización de origen.');
                }
            });
        }

        $(document).ready(function() {
            var cotizacionOrigen = new URLSearchParams(window.location.search).get('desde_cotizacion');

            if (cotizacionOrigen) {
                cargarDesdeCotizacion(cotizacionOrigen);
            }
        });

    </script>
@endpush
