<?php
$pagina_actual = basename($_SERVER['PHP_SELF'], '.php');
$is_homepage   = $is_homepage ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $titulo_pagina ?? 'Clínica General' ?> — clinicageneral.local</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root {
      --verde:       #0d6e56;
      --verde-hover: #0a5a47;
      --verde-suave: #e6f2ee;
      --crema:       #f5f5f3;
      --oscuro:      #1a1a1a;
      --gris-texto:  #3a3a3a;
      --gris-medio:  #6b7280;
      --borde:       #d1d5db;
      --sidebar-w:   240px;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--crema);
      color: var(--gris-texto);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
    }

    /* ── TIPOGRAFÍA ────────────────────────── */
    h1, h2, h3, h4, h5 { font-family: 'Inter', sans-serif; font-weight: 700; }

    /* ── NAVBAR TOP ────────────────────────── */
    .top-nav {
      position: fixed;
      top: 0; left: 0; right: 0;
      height: 58px;
      background: var(--oscuro);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      z-index: 1000;
      border-bottom: 1px solid #333;
    }

    .top-nav .brand {
      font-family: 'Playfair Display', serif;
      font-size: 17px;
      font-weight: 700;
      color: #fff;
      text-decoration: none;
      letter-spacing: 0.3px;
    }

    .top-nav .brand span {
      color: #5db89a;
    }

    .top-nav-links {
      display: flex;
      align-items: center;
      gap: 4px;
      list-style: none;
      margin: 0; padding: 0;
    }

    .top-nav-links a {
      color: rgba(255,255,255,0.65);
      text-decoration: none;
      font-size: 13px;
      padding: 6px 12px;
      border-radius: 0;
      transition: color 0.15s, background 0.15s;
    }

    .top-nav-links a:hover { color: #fff; background: rgba(255,255,255,0.07); }

    .top-nav-links .nav-btn-primary {
      background: var(--verde);
      color: #fff;
      font-size: 13px;
      font-weight: 500;
      padding: 6px 14px;
    }
    .top-nav-links .nav-btn-primary:hover { background: var(--verde-hover); color: #fff; }

    .top-nav-links .nav-btn-outline {
      border: 1px solid rgba(255,255,255,0.2);
      color: rgba(255,255,255,0.8);
      font-size: 13px;
      padding: 5px 13px;
    }
    .top-nav-links .nav-btn-outline:hover { border-color: rgba(255,255,255,0.5); color: #fff; background: transparent; }

    .user-chip {
      display: flex;
      align-items: center;
      gap: 7px;
      color: rgba(255,255,255,0.85);
      font-size: 13px;
      font-weight: 500;
      padding: 5px 10px;
      border-radius: 0;
    }
    .user-chip .avatar {
      width: 26px; height: 26px;
      background: var(--verde);
      border-radius: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700; color: #fff;
    }

    /* ── MAIN WRAPPER ─────────────────────── */
    main {
      flex: 1;
      padding-top: 58px;
    }

    .page-container {
      max-width: 1300px;
      margin: 0 auto;
      padding: 32px 24px;
    }

    /* ── CARDS CUADRADAS ──────────────────── */
    .card {
      border-radius: 0 !important;
      border: 1px solid var(--borde);
      box-shadow: none !important;
    }

    .stat-card {
      border: 1px solid var(--borde);
      background: #fff;
      padding: 20px;
    }
    .stat-card .stat-num {
      font-size: 2rem;
      font-weight: 700;
      color: var(--verde);
      line-height: 1;
    }
    .stat-card .stat-label {
      font-size: 12px;
      color: var(--gris-medio);
      margin-top: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .stat-card .stat-icon {
      font-size: 1.6rem;
      color: var(--verde-suave);
    }

    /* ── PAGE HEADER ──────────────────────── */
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 20px;
      border-bottom: 2px solid var(--oscuro);
      margin-bottom: 28px;
    }
    .page-header h2 {
      font-size: 22px;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .page-header .subtitle {
      font-size: 12px;
      color: var(--gris-medio);
      margin-top: 2px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* ── SECTION TITLE ────────────────────── */
    .section-title {
      font-size: 13px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      color: var(--gris-medio);
      padding-bottom: 8px;
      border-bottom: 1px solid var(--borde);
      margin-bottom: 16px;
    }

    /* ── TABLES ───────────────────────────── */
    .table { font-size: 13.5px; }
    .table thead th {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--gris-medio);
      background: #f9f9f8;
      border-bottom: 1px solid var(--borde);
      padding: 10px 14px;
    }
    .table td { padding: 11px 14px; vertical-align: middle; }
    .table-hover tbody tr:hover { background: #f5faf8; }

    /* ── BADGES ───────────────────────────── */
    .badge { border-radius: 0; font-weight: 500; font-size: 11px; }

    /* ── FORMS ────────────────────────────── */
    .form-control, .form-select {
      border-radius: 0;
      border: 1px solid var(--borde);
      font-size: 13.5px;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--verde);
      box-shadow: 0 0 0 2px rgba(13,110,86,0.12);
    }
    .form-label {
      font-size: 11.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      color: var(--gris-medio);
      margin-bottom: 5px;
    }

    /* ── BUTTONS ──────────────────────────── */
    .btn { border-radius: 0; font-size: 13px; font-weight: 500; }
    .btn-primary   { background: var(--verde); border-color: var(--verde); }
    .btn-primary:hover { background: var(--verde-hover); border-color: var(--verde-hover); }
    .btn-success   { background: var(--verde); border-color: var(--verde); }
    .btn-success:hover { background: var(--verde-hover); border-color: var(--verde-hover); }
    .btn-outline-success { color: var(--verde); border-color: var(--verde); }
    .btn-outline-success:hover { background: var(--verde); border-color: var(--verde); color: #fff; }

    /* ── TABS ─────────────────────────────── */
    .nav-tabs { border-bottom: 2px solid var(--borde); gap: 0; }
    .nav-tabs .nav-link {
      border-radius: 0;
      font-size: 13px;
      font-weight: 500;
      color: var(--gris-medio);
      border: none;
      border-bottom: 2px solid transparent;
      margin-bottom: -2px;
      padding: 10px 18px;
    }
    .nav-tabs .nav-link:hover { color: var(--gris-texto); border-bottom-color: var(--borde); }
    .nav-tabs .nav-link.active { color: var(--verde); border-bottom-color: var(--verde); background: transparent; }

    /* ── ALERT ────────────────────────────── */
    .alert { border-radius: 0; font-size: 13.5px; }

    /* ── FOOTER ───────────────────────────── */
    footer {
      background: var(--oscuro);
      padding: 20px 24px;
      margin-top: auto;
      font-size: 12px;
      color: rgba(255,255,255,0.35);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    footer .footer-brand { color: rgba(255,255,255,0.6); font-weight: 600; }

    /* Botones usados en hero de inicio.php */
    .btn-nav-cita {
      background: var(--verde); color: #fff !important;
      border: none; border-radius: 0;
      font-size: 14px; font-weight: 500;
      transition: background 0.15s;
    }
    .btn-nav-cita:hover { background: var(--verde-hover); }
    .btn-nav-login {
      border: 1.5px solid rgba(255,255,255,0.3);
      color: #fff !important;
      border-radius: 0; font-size: 14px;
      background: transparent;
      transition: border-color 0.15s;
    }
    .btn-nav-login:hover { border-color: #fff; }
  </style>

  <?php if ($is_homepage): ?>
  <!-- Estilos originales del navbar para el homepage -->
  <style>
    body { font-family: 'DM Sans', sans-serif; background: #f9f6f1; }
    h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
    .top-nav {
      background: rgba(249,246,241,0.95) !important;
      backdrop-filter: blur(12px);
      border-bottom: 1px solid #dde8e5;
      height: 72px;
    }
    .top-nav .brand { font-family: 'Playfair Display', serif; font-size: 20px; color: #0a6e5c; }
    .top-nav .brand span { color: #0a6e5c; }
    .top-nav-links a { color: #6b6b6b; }
    .top-nav-links a:hover { color: #0a6e5c; background: transparent; }
    .top-nav-links .nav-btn-primary {
      background: #0a6e5c; border-radius: 8px; color: #fff !important;
    }
    .top-nav-links .nav-btn-primary:hover { background: #12907a; }
    .top-nav-links .nav-btn-outline {
      border: 1.5px solid #dde8e5; color: #2c2c2c !important;
      border-radius: 8px; background: transparent;
    }
    .top-nav-links .nav-btn-outline:hover { border-color: #0a6e5c; color: #0a6e5c !important; background: transparent; }
    .user-chip { color: #2c2c2c; }
    .user-chip .avatar { background: #0a6e5c; border-radius: 50%; }
    .btn-nav-cita { border-radius: 8px !important; }
    .btn-nav-login {
      border: 1.5px solid #dde8e5 !important;
      color: #2c2c2c !important;
      border-radius: 8px !important;
    }
    .btn-nav-login:hover { border-color: #0a6e5c !important; color: #0a6e5c !important; }
    main { padding-top: 72px; }
  </style>
  <?php endif; ?>
</head>
<body>

<!-- TOP NAV -->
<nav class="top-nav">
  <a class="brand" href="/index.php">Clínica<span>General</span></a>

  <ul class="top-nav-links">
    <a href="/index.php">Inicio</a>
    <a href="/index.php#servicios">Servicios</a>
    <a href="/index.php#departamentos">Departamentos</a>
    <a href="/index.php#contacto">Contacto</a>

    <?php if (isset($_SESSION['usuario'])): ?>
      <?php
        $ini = mb_strtoupper(mb_substr($_SESSION['nombre'], 0, 1));
      ?>
      <div class="user-chip">
        <div class="avatar"><?= $ini ?></div>
        <?= htmlspecialchars(explode(' ', $_SESSION['nombre'])[0]) ?>
        <span style="color:rgba(255,255,255,0.35);font-size:11px"><?= ucfirst($_SESSION['rol']) ?></span>
      </div>
      <a href="/index.php?vista=panel" class="nav-btn-outline"><i class="bi bi-grid-3x3-gap me-1"></i>Panel</a>
      <a href="/index.php?vista=cerrar" class="nav-btn-primary"><i class="bi bi-box-arrow-right me-1"></i>Salir</a>
    <?php else: ?>
      <a href="/index.php?vista=login" class="nav-btn-outline"><i class="bi bi-person-lock me-1"></i>Acceso personal</a>
      <a href="/index.php?vista=citas" class="nav-btn-primary"><i class="bi bi-calendar-plus me-1"></i>Pedir cita</a>
    <?php endif; ?>
  </ul>
</nav>

<main>
<?php if (!$is_homepage): ?>
<div class="page-container">
<?php endif; ?>
