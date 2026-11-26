<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Katalog Buku - Sistem Perpustakaan Kampus</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tema Warna Coklat Putih Perpustakaan PPRI -->
    <link rel="stylesheet" href="{{ asset('css/style-perpustakaan.css') }}">

    <style>
        :root {
            --sidebar-bg: #79583F;        /* Coklat kayu */
            --menu-active: #967455;       /* Coklat muda */
            --bg-main: #F7F3ED;           /* Cream */
            --card-bg: #FFFDF9;           /* Putih cream */
            --border-beige: #E9DFD3;      /* Beige */
            --btn-primary: #79583F;       /* Coklat kayu */
            --btn-hover: #674832;
            --text-primary: #3E3026;      /* Coklat gelap */
            --text-secondary: #8B7B6C;    /* Abu kecoklatan */
            --table-header: #F3ECE3;      /* Beige muda */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            color: #FFFDF9;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 15px rgba(62, 48, 38, 0.08);
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 253, 249, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background-color: var(--menu-active);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #FFFDF9;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .brand-text h2 {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.2;
            color: #FFFDF9;
        }

        .brand-text p {
            font-size: 11px;
            opacity: 0.85;
            color: #E9DFD3;
        }

        .user-profile-badge {
            margin: 16px 16px 8px 16px;
            padding: 12px 14px;
            background-color: rgba(0, 0, 0, 0.14);
            border: 1px solid rgba(233, 223, 211, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #967455, #E9DFD3);
            color: #3E3026;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .user-info h4 {
            font-size: 13px;
            font-weight: 700;
            color: #FFFDF9;
        }

        .user-info p {
            font-size: 11px;
            color: #E9DFD3;
        }

        .user-info .badge-role {
            display: inline-block;
            background-color: var(--menu-active);
            color: #FFFDF9;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 4px;
            margin-top: 3px;
        }

        .sidebar-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(233, 223, 211, 0.7);
            padding: 16px 20px 8px 20px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0 12px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-nav li {
            margin-bottom: 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: rgba(255, 253, 249, 0.85);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: rgba(150, 116, 85, 0.4);
            color: #FFFDF9;
        }

        .nav-link.active {
            background-color: var(--menu-active);
            color: #FFFFFF;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .nav-link i {
            font-size: 18px;
            flex-shrink: 0;
        }

        .nav-link .nav-desc {
            display: block;
            font-size: 10.5px;
            font-weight: 400;
            opacity: 0.8;
            margin-top: 1px;
        }

        .nav-tag-active {
            margin-left: auto;
            background-color: #FFFDF9;
            color: var(--sidebar-bg);
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 253, 249, 0.15);
            font-size: 11px;
            color: rgba(233, 223, 211, 0.85);
            line-height: 1.4;
        }

        /* Main Content Layout */
        .main-wrapper {
            margin-left: 280px;
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 280px);
        }

        .top-navbar {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-beige);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .toggle-btn {
            display: none;
            background: none;
            border: 1px solid var(--border-beige);
            font-size: 20px;
            color: var(--text-primary);
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .breadcrumb {
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .breadcrumb span {
            color: var(--text-primary);
            font-weight: 600;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-main {
            background-color: var(--btn-primary);
            color: #FFFDF9;
            border: 1px solid var(--btn-primary);
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 9px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-main:hover {
            background-color: var(--btn-hover);
            border-color: var(--btn-hover);
            color: #FFFDF9;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(121, 88, 63, 0.2);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-beige);
            padding: 9px 16px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 9px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: var(--table-header);
            border-color: var(--border-beige);
        }

        /* Page Content Area */
        .content-area {
            padding: 28px 32px 48px 32px;
            flex-grow: 1;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .page-header p {
            font-size: 13.5px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        /* Alert / Toast Messages */
        .alert-box {
            background-color: #FFFDF9;
            border: 1px solid var(--border-beige);
            border-left: 5px solid #2E7D32;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(62, 48, 38, 0.05);
            animation: fadeIn 0.3s ease;
        }

        .alert-box.alert-error {
            border-left-color: #D32F2F;
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .alert-content i {
            font-size: 20px;
            color: #2E7D32;
        }

        .alert-box.alert-error .alert-content i {
            color: #D32F2F;
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 16px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 26px;
        }

        .stat-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-beige);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(62, 48, 38, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(62, 48, 38, 0.06);
        }

        .stat-info p {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            margin-top: 4px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-icon.brown {
            background-color: var(--table-header);
            color: var(--sidebar-bg);
        }

        .stat-icon.wood {
            background-color: rgba(150, 116, 85, 0.15);
            color: var(--menu-active);
        }

        .stat-icon.cream {
            background-color: rgba(233, 223, 211, 0.45);
            color: var(--text-primary);
        }

        .stat-icon.warning {
            background-color: #FFF3E0;
            color: #E65100;
        }

        /* Filter & Search Bar */
        .filter-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-beige);
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 6px rgba(62, 48, 38, 0.03);
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .search-input-group {
            flex-grow: 1;
            min-width: 250px;
            position: relative;
        }

        .search-input-group i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            background-color: #FFFFFF;
            border: 1px solid var(--border-beige);
            color: var(--text-primary);
            padding: 9px 14px;
            font-size: 13.5px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .search-input-group .form-control {
            padding-left: 38px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--btn-primary);
            box-shadow: 0 0 0 3px rgba(121, 88, 63, 0.15);
        }

        .form-select {
            background-color: #FFFFFF;
            border: 1px solid var(--border-beige);
            color: var(--text-primary);
            padding: 9px 14px;
            font-size: 13.5px;
            border-radius: 8px;
            min-width: 180px;
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--btn-primary);
            box-shadow: 0 0 0 3px rgba(121, 88, 63, 0.15);
        }

        /* Table Card Container */
        .table-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-beige);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(62, 48, 38, 0.04);
        }

        .table-header-box {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-beige);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-header-box h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-badge-count {
            background-color: var(--table-header);
            color: var(--text-primary);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table thead th {
            background-color: var(--table-header);
            color: var(--text-primary);
            font-size: 12.5px;
            font-weight: 700;
            padding: 13px 16px;
            border-bottom: 2px solid var(--border-beige);
            white-space: nowrap;
        }

        .custom-table tbody tr {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-beige);
            transition: background-color 0.15s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #F8F4EE;
        }

        .custom-table td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: var(--text-primary);
            vertical-align: middle;
        }

        .book-title-cell {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 14px;
            margin-bottom: 3px;
        }

        .book-synopsis-preview {
            font-size: 12px;
            color: var(--text-secondary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            max-width: 320px;
        }

        .badge-isbn {
            background-color: #F3ECE3;
            color: var(--sidebar-bg);
            font-family: monospace;
            font-weight: 700;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid var(--border-beige);
            display: inline-block;
        }

        .badge-rak {
            background-color: rgba(150, 116, 85, 0.12);
            color: var(--menu-active);
            font-weight: 600;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid rgba(150, 116, 85, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .stock-available {
            background-color: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #C8E6C9;
        }

        .stock-warning {
            background-color: #FFF3E0;
            color: #E65100;
            border: 1px solid #FFE0B2;
        }

        .stock-out {
            background-color: #FFEBEE;
            color: #C62828;
            border: 1px solid #FFCDD2;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: 1px solid var(--border-beige);
            background-color: #FFFDF9;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-action.view:hover {
            background-color: var(--table-header);
            color: var(--sidebar-bg);
            border-color: var(--sidebar-bg);
        }

        .btn-action.edit:hover {
            background-color: var(--menu-active);
            color: #FFFDF9;
            border-color: var(--menu-active);
        }

        .btn-action.delete:hover {
            background-color: #D32F2F;
            color: #FFFDF9;
            border-color: #D32F2F;
        }

        /* Empty State */
        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 48px;
            color: var(--border-beige);
            margin-bottom: 12px;
            display: block;
        }

        .empty-state h4 {
            font-size: 16px;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(62, 48, 38, 0.45);
            backdrop-filter: blur(3px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        .modal-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-beige);
            border-radius: 14px;
            width: 100%;
            max-width: 580px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 15px 35px rgba(62, 48, 38, 0.2);
            animation: slideUp 0.25s ease;
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-beige);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.15s ease;
        }

        .modal-close-btn:hover {
            color: var(--text-primary);
        }

        .modal-body {
            padding: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-group label .required {
            color: #D32F2F;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-beige);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background-color: var(--table-header);
            border-bottom-left-radius: 14px;
            border-bottom-right-radius: 14px;
        }

        /* Detail Modal Specifics */
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px dashed var(--border-beige);
            font-size: 13.5px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            width: 140px;
            font-weight: 600;
            color: var(--text-secondary);
            flex-shrink: 0;
        }

        .detail-value {
            color: var(--text-primary);
            font-weight: 500;
            flex-grow: 1;
        }

        .synopsis-box {
            background-color: #F8F4EE;
            border: 1px solid var(--border-beige);
            border-radius: 8px;
            padding: 14px;
            margin-top: 8px;
            font-size: 13px;
            line-height: 1.5;
            color: var(--text-primary);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
            .toggle-btn {
                display: block;
            }
            .top-navbar {
                padding: 14px 20px;
            }
            .content-area {
                padding: 20px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content Wrapper (Background utama: #F7F3ED - Cream) -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="top-left">
                <button class="toggle-btn" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="breadcrumb">
                    <i class="bi bi-house-door"></i>
                    <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
                    <span>Katalog Buku</span>
                </div>
            </div>

            <div class="top-right">
                <a href="{{ route('katalog.buku') }}" class="btn-secondary" title="Muat Ulang Data">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
                <button type="button" class="btn-main" onclick="openAddModal()">
                    <i class="bi bi-plus-lg"></i> Tambah Buku Baru
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">
            <!-- Page Title -->
            <div class="page-header">
                <h1>Katalog Buku</h1>
                <p>Kelola data koleksi buku perpustakaan kampus, nomor induk (ISBN), letak rak, stok fisik, dan sinopsis buku secara terstruktur.</p>
            </div>

            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert-box" id="flashAlert">
                    <div class="alert-content">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button class="alert-close" onclick="document.getElementById('flashAlert').remove()">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-error" id="flashAlertError">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button class="alert-close" onclick="document.getElementById('flashAlertError').remove()">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-error" id="flashAlertValidation">
                    <div class="alert-content">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                        <div>
                            @foreach($errors->all() as $err)
                                <div>&bull; {{ $err }}</div>
                            @endforeach
                        </div>
                    </div>
                    <button class="alert-close" onclick="document.getElementById('flashAlertValidation').remove()">&times;</button>
                </div>
            @endif

            <!-- Metric & Stat Cards (Card / Form: #FFFDF9, Border: #E9DFD3) -->
            <div class="stats-grid">
                <!-- Stat 1 -->
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Judul Buku</p>
                        <h3>{{ $stats['total_judul'] }}</h3>
                    </div>
                    <div class="stat-icon brown">
                        <i class="bi bi-journals"></i>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Stok Fisik</p>
                        <h3>{{ $stats['total_stok'] }} <span style="font-size: 13px; font-weight: normal; color: var(--text-secondary);">eks</span></h3>
                    </div>
                    <div class="stat-icon wood">
                        <i class="bi bi-stack"></i>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Kategori / Rak</p>
                        <h3>{{ $stats['total_kategori'] }}</h3>
                    </div>
                    <div class="stat-icon cream">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Stok Kritis (≤ 2)</p>
                        <h3 style="color: {{ $stats['stok_kritis'] > 0 ? '#E65100' : 'var(--text-primary)' }};">
                            {{ $stats['stok_kritis'] }}
                        </h3>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="filter-card">
                <form action="{{ route('katalog.buku') }}" method="GET" class="filter-form" id="searchFilterForm">
                    <div class="search-input-group">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" id="liveSearchInput" class="form-control" 
                               placeholder="Cari judul buku, nomor induk/ISBN, penulis, atau penerbit..." 
                               value="{{ request('search') }}">
                    </div>

                    <!-- Kategori Rak Dropdown -->
                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori / Rak --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Status Stok Dropdown -->
                    <select name="status_stok" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status Stok --</option>
                        <option value="tersedia" {{ request('status_stok') == 'tersedia' ? 'selected' : '' }}>Stok Aman (> 2)</option>
                        <option value="kritis" {{ request('status_stok') == 'kritis' ? 'selected' : '' }}>Stok Kritis (1-2)</option>
                        <option value="habis" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
                    </select>

                    <button type="submit" class="btn-main" style="padding: 9px 16px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'kategori', 'status_stok']))
                        <a href="{{ route('katalog.buku') }}" class="btn-secondary" style="padding: 9px 14px;">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table Card (Header: #F3ECE3, Background: #FFFDF9, Border: #E9DFD3) -->
            <div class="table-card">
                <div class="table-header-box">
                    <h3>
                        <i class="bi bi-table"></i>
                        Daftar Koleksi Buku Perpustakaan
                    </h3>
                    <span class="table-badge-count">{{ count($buku) }} Buku Ditemukan</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table" id="booksTable">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 140px;">Nomor Induk / ISBN</th>
                                <th>Informasi Buku</th>
                                <th>Penulis & Penerbit</th>
                                <th>Letak Rak & Kategori</th>
                                <th style="width: 120px; text-align: center;">Stok Tersedia</th>
                                <th style="width: 110px; text-align: center;">Status</th>
                                <th style="width: 130px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buku as $index => $item)
                                <tr class="book-row" data-id="{{ $item->id_buku }}">
                                    <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <span class="badge-isbn">{{ $item->isbn ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="book-title-cell">{{ $item->judul }}</div>
                                        <div class="book-synopsis-preview" title="{{ $item->sinopsis ?? 'Belum ada sinopsis' }}">
                                            {{ $item->sinopsis ?? 'Sinopsis belum ditambahkan.' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-primary);">
                                            <i class="bi bi-person" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $item->penulis ?? '-' }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                            <i class="bi bi-building" style="color: var(--text-secondary); margin-right: 4px;"></i>{{ $item->penerbit ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-rak">
                                            <i class="bi bi-geo-alt"></i> {{ $item->kategori_rak ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; font-size: 15px;">
                                        {{ $item->stok_tersedia }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item->stok_tersedia > 2)
                                            <span class="stock-badge stock-available">
                                                <i class="bi bi-check-circle"></i> Tersedia
                                            </span>
                                        @elseif($item->stok_tersedia > 0)
                                            <span class="stock-badge stock-warning">
                                                <i class="bi bi-exclamation-circle"></i> Menipis
                                            </span>
                                        @else
                                            <span class="stock-badge stock-out">
                                                <i class="bi bi-x-circle"></i> Habis
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-group" style="justify-content: center;">
                                            <!-- Detail Button -->
                                            <button type="button" class="btn-action view" title="Lihat Detail Buku"
                                                    onclick="viewBookDetail({{ json_encode($item) }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <!-- Edit Button -->
                                            <button type="button" class="btn-action edit" title="Edit Informasi Buku"
                                                    onclick="editBookModal({{ json_encode($item) }})">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <!-- Delete Button -->
                                            <button type="button" class="btn-action delete" title="Hapus Buku"
                                                    onclick="confirmDeleteBook({{ $item->id_buku }}, '{{ addslashes($item->judul) }}')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <i class="bi bi-journal-x"></i>
                                            <h4>Tidak ada data buku yang sesuai</h4>
                                            <p>Coba gunakan kata kunci lain atau tambahkan data buku baru ke perpustakaan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL 1: Tambah Buku Baru (Insert) -->
    <div class="modal-overlay" id="modalAddBook">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-plus-circle" style="color: var(--btn-primary);"></i> Tambah Buku Baru</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalAddBook')">&times;</button>
            </div>
            <form action="{{ route('buku.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_isbn">Nomor Induk / ISBN</label>
                            <input type="text" name="isbn" id="add_isbn" class="form-control" placeholder="Contoh: 978-602-xxxx">
                        </div>
                        <div class="form-group">
                            <label for="add_stok">Jumlah Stok <span class="required">*</span></label>
                            <input type="number" name="stok_tersedia" id="add_stok" class="form-control" min="0" value="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="add_judul">Judul Buku <span class="required">*</span></label>
                        <input type="text" name="judul" id="add_judul" class="form-control" placeholder="Masukkan judul buku lengkap" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_penulis">Penulis / Pengarang</label>
                            <input type="text" name="penulis" id="add_penulis" class="form-control" placeholder="Nama penulis">
                        </div>
                        <div class="form-group">
                            <label for="add_penerbit">Penerbit</label>
                            <input type="text" name="penerbit" id="add_penerbit" class="form-control" placeholder="Nama penerbit">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="add_kategori_rak">Kategori & Letak Rak Fisik</label>
                        <input type="text" name="kategori_rak" id="add_kategori_rak" class="form-control" list="kategoriListDatalist" placeholder="Contoh: Rak Komputer A1, Rak Web Dev B2...">
                        <datalist id="kategoriListDatalist">
                            @if(isset($rakList))
                                @foreach($rakList as $r)
                                    <option value="{{ $r->nama_rak }} ({{ $r->kode_rak }})">
                                @endforeach
                            @endif
                            @foreach($kategoriList as $k)
                                <option value="{{ $k }}">
                            @endforeach
                            <option value="Rak Komputer A1">
                            <option value="Rak Web Dev B2">
                            <option value="Rak Sistem Informasi C1">
                            <option value="Rak Algoritma A2">
                            <option value="Rak Jaringan B1">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="add_sinopsis">Sinopsis / Ringkasan Buku</label>
                        <textarea name="sinopsis" id="add_sinopsis" rows="4" class="form-control" placeholder="Tuliskan deskripsi singkat atau sinopsis isi buku..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalAddBook')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-save"></i> Simpan Buku Baru</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Buku (Update) -->
    <div class="modal-overlay" id="modalEditBook">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-pencil-square" style="color: var(--menu-active);"></i> Edit Informasi Buku</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditBook')">&times;</button>
            </div>
            <form id="formEditBook" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_isbn">Nomor Induk / ISBN</label>
                            <input type="text" name="isbn" id="edit_isbn" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_stok">Jumlah Stok <span class="required">*</span></label>
                            <input type="number" name="stok_tersedia" id="edit_stok" class="form-control" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_judul">Judul Buku <span class="required">*</span></label>
                        <input type="text" name="judul" id="edit_judul" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_penulis">Penulis / Pengarang</label>
                            <input type="text" name="penulis" id="edit_penulis" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_penerbit">Penerbit</label>
                            <input type="text" name="penerbit" id="edit_penerbit" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_kategori_rak">Kategori & Letak Rak Fisik</label>
                        <input type="text" name="kategori_rak" id="edit_kategori_rak" class="form-control" list="kategoriListDatalist">
                    </div>

                    <div class="form-group">
                        <label for="edit_sinopsis">Sinopsis / Ringkasan Buku</label>
                        <textarea name="sinopsis" id="edit_sinopsis" rows="4" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditBook')">Batal</button>
                    <button type="submit" class="btn-main"><i class="bi bi-check2-circle"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Detail Buku (Read Detail) -->
    <div class="modal-overlay" id="modalDetailBook">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-info-circle" style="color: var(--sidebar-bg);"></i> Detail Koleksi Buku</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailBook')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 18px; text-align: center; padding: 12px; background: #F8F4EE; border-radius: 10px; border: 1px solid var(--border-beige);">
                    <h2 id="detail_judul" style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px;">-</h2>
                    <span id="detail_isbn_badge" class="badge-isbn">-</span>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-person"></i> Penulis</div>
                    <div class="detail-value" id="detail_penulis">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-building"></i> Penerbit</div>
                    <div class="detail-value" id="detail_penerbit">-</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-geo-alt"></i> Letak Rak</div>
                    <div class="detail-value">
                        <span class="badge-rak" id="detail_rak">-</span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label"><i class="bi bi-stack"></i> Stok Tersedia</div>
                    <div class="detail-value" id="detail_stok">-</div>
                </div>

                <div class="detail-row" style="flex-direction: column; gap: 4px; border-bottom: none;">
                    <div class="detail-label"><i class="bi bi-card-text"></i> Sinopsis / Deskripsi</div>
                    <div class="synopsis-box" id="detail_sinopsis">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDetailBook')">Tutup</button>
                <button type="button" class="btn-main" id="btnEditFromDetail"><i class="bi bi-pencil-square"></i> Edit Data Ini</button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Konfirmasi Hapus (Delete) -->
    <div class="modal-overlay" id="modalDeleteBook">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: #D32F2F;"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDeleteBook')">&times;</button>
            </div>
            <form id="formDeleteBook" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center; padding: 26px 20px;">
                    <i class="bi bi-trash3" style="font-size: 46px; color: #D32F2F; margin-bottom: 12px; display: inline-block;"></i>
                    <p style="font-size: 14.5px; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin menghapus buku berikut dari katalog?
                    </p>
                    <h4 id="deleteBookTitle" style="font-size: 15px; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 10px; border-radius: 8px; border: 1px solid #FFCDD2;"></h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 10px;">
                        Tindakan ini tidak dapat dibatalkan jika data dihapus dari database.
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalDeleteBook')">Batal</button>
                    <button type="submit" class="btn-main" style="background-color: #D32F2F; border-color: #D32F2F;">
                        <i class="bi bi-trash3-fill"></i> Ya, Hapus Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Responsive sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target) && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                }
            }
        });



        // Modal Helpers
        function openAddModal() {
            document.getElementById('modalAddBook').classList.add('active');
            document.getElementById('add_judul').focus();
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Close modal on Escape key or backdrop click
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
            }
        });

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                }
            });
        });

        // View Book Detail Modal
        let currentViewingBook = null;
        function viewBookDetail(book) {
            currentViewingBook = book;
            document.getElementById('detail_judul').textContent = book.judul || '-';
            document.getElementById('detail_isbn_badge').textContent = book.isbn || 'Tidak Ada ISBN';
            document.getElementById('detail_penulis').textContent = book.penulis || '-';
            document.getElementById('detail_penerbit').textContent = book.penerbit || '-';
            document.getElementById('detail_rak').innerHTML = '<i class="bi bi-geo-alt"></i> ' + (book.kategori_rak || 'Umum');
            document.getElementById('detail_stok').textContent = book.stok_tersedia + ' Eksemplar';
            document.getElementById('detail_sinopsis').textContent = book.sinopsis || 'Tidak ada sinopsis yang dicantumkan untuk buku ini.';

            const btnEdit = document.getElementById('btnEditFromDetail');
            btnEdit.onclick = function() {
                closeModal('modalDetailBook');
                editBookModal(book);
            };

            document.getElementById('modalDetailBook').classList.add('active');
        }

        // Edit Book Modal
        function editBookModal(book) {
            const form = document.getElementById('formEditBook');
            form.action = '/buku/' + book.id_buku;

            document.getElementById('edit_isbn').value = book.isbn || '';
            document.getElementById('edit_judul').value = book.judul || '';
            document.getElementById('edit_penulis').value = book.penulis || '';
            document.getElementById('edit_penerbit').value = book.penerbit || '';
            document.getElementById('edit_kategori_rak').value = book.kategori_rak || '';
            document.getElementById('edit_stok').value = book.stok_tersedia;
            document.getElementById('edit_sinopsis').value = book.sinopsis || '';

            document.getElementById('modalEditBook').classList.add('active');
        }

        // Confirm Delete Modal
        function confirmDeleteBook(id, title) {
            const form = document.getElementById('formDeleteBook');
            form.action = '/buku/' + id;
            document.getElementById('deleteBookTitle').textContent = '"' + title + '"';
            document.getElementById('modalDeleteBook').classList.add('active');
        }

        // Live instant table search filter (client-side responsiveness)
        const liveSearchInput = document.getElementById('liveSearchInput');
        if (liveSearchInput) {
            liveSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#booksTable tbody tr.book-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
