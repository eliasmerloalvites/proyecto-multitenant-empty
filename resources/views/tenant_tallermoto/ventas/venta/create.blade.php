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

        /* ==== CART =====   */

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

        .checkout-label {

            display: block;

            font-size: 13px;
            font-weight: 700;

            color: #ffffff;

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
                                    <button class="voucher-option" onclick="changeVoucher(this,'BOLETA')">
                                        Boleta
                                    </button>
                                    <button class="voucher-option" onclick="changeVoucher(this,'FACTURA')">
                                        Factura
                                    </button>
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



                            <!-- METODO -->
                            <div class="checkout-block">

                                <label class="checkout-label">

                                    Método Pago

                                </label>

                                <select id="paymentMethod" class="checkout-input" onchange="changePaymentMethod()">
                                    @foreach ($metodo_pago as $mt)
                                        <option value="{{ $mt->MEP_Id }}">{{ $mt->MEP_Pago }}</option>
                                    @endforeach

                                </select>

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

                                <!-- PAGO -->
                                <div class="checkout-block">

                                    <label class="checkout-label">

                                        Pago Recibido

                                    </label>

                                    <input type="number" id="inputPago" class="checkout-input checkout-money"
                                        placeholder="0.00" autofocus oninput="calculateChange()">

                                </div>

                                <!-- VUELTO -->
                                <div class="checkout-block" id="changeContainer">

                                    <label class="checkout-label">

                                        Vuelto

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
        }


        function calculateChange() {
            let total = 120.00;
            let payment = parseFloat($('#inputPago').val()) || 0;
            let change = payment - total;

            $('#inputVuelto').val(change.toFixed(2));
            if (change < 0) {
                $('#inputVuelto').css({
                    color: '#EF4444',
                    fontWeight: '800'
                });
            } else {
                $('#inputVuelto').css({
                    color: '#22C55E',
                    fontWeight: '800'
                });
            }
        }

        function changePaymentMethod() {
            let method = $('#paymentMethod').val();
            if (method == 'EFECTIVO') {
                $('#changeContainer').slideDown(150);
            } else {
                $('#changeContainer').slideUp(150);
            }
        }

        function openCheckout() {
            if(cart.length == 0){
                showToast('warning', 'No hay productos en el carrito');
                return;
            }
            $('#modalCheckout').modal('show');
        }

        function finalizarVenta(){
            if(cart.length == 0){
                showToast('warning', 'No hay productos en el carrito');
                return;
            }
            // FACTURA requiere cliente
            if(voucherType == 'FACTURA' && !$('#cliente_id').val()){
                showToast('warning', 'Debe seleccionar cliente');
                return;
            }

            //  DATA

            let data = {
                cliente_id: $('#cliente_id').val(),
                comprobante: voucherType,
                metodo_pago: $('#paymentMethod').val(),
                pago_recibido: $('#inputPago').val(),
                vuelto: $('#inputVuelto').val(),
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
                    // SUCCESS
                    Swal.fire({
                            icon: "success",
                            title: "Venta Generada",
                            text: "Se realizo el pago correctamente!",
                            confirmButtonText: "Aceptar",
                            footer: '<a title="TICKET" target="_blank"  href="/tenant/ventas/venta/' + response
                                .venta_id +'/ticket" class="btn btn-danger btn-sm" style="margin-left: 5px;"><i class="fa fa fa-print"></i> ¿Desea imprimir documento?</a>',
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
                error: function(){
                    showToast('error', 'Error al registrar venta');
                },
                complete: function(){
                    $('.btn-finish-sale').prop('disabled', false).html(`FINALIZAR VENTA`);
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
            // INPUTS
            $('#inputPago').val('');
            $('#inputVuelto').val('0.00');
        }


    </script>
@endpush
