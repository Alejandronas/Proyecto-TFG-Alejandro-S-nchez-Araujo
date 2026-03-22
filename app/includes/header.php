<?php
// ============================================================
//  Clínica General — Cabecera compartida
//  Archivo: includes/header.php
// ============================================================

// Página actual para marcar el enlace activo en el navbar
$pagina_actual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $titulo_pagina ?? 'Clínica General' ?> — clinicageneral.local</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --verde:       #0a6e5c;
      --verde-claro: #12907a;
      --verde-suave: #e8f5f2;
      --crema:       #f9f6f1;
      --gris-texto:  #2c2c2c;
      --gris-suave:  #6b6b6b;
      --borde:       #dde8e5;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--crema);
      color: var(--gris-texto);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main { flex: 1; }

    h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }

    .navbar {
      background: rgba(249, 246, 241, 0.95) !important;
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--borde);
      height: 72px;
    }

    .brand-name {
      font-family: 'Playfair Display', serif;
      font-size: 20px;
      font-weight: 700;
      color: var(--verde);
      line-height: 1.1;
    }

    .brand-sub {
      font-size: 11px;
      color: var(--gris-suave);
      letter-spacing: 0.5px;
    }

    .nav-link {
      color: var(--gris-suave) !important;
      font-size: 14px;
      transition: color 0.2s;
    }

    .nav-link:hover,
    .nav-link.activo { color: var(--verde) !important; font-weight: 500; }

    .btn-login {
      border: 1.5px solid var(--borde);
      border-radius: 8px;
      color: var(--gris-texto) !important;
      font-size: 14px;
      padding: 8px 18px;
      transition: border-color 0.2s, color 0.2s;
    }

    .btn-login:hover { border-color: var(--verde); color: var(--verde) !important; }

    .btn-cita {
      background: var(--verde);
      color: #fff !important;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      padding: 8px 20px;
      transition: background 0.2s;
    }

    .btn-cita:hover { background: var(--verde-claro); }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top px-4">
  <a class="navbar-brand" href="/index.php">
    <div class="brand-name">Clínica General</div>
    <div class="brand-sub">clinicageneral.local</div>
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navMenu">
    <ul class="navbar-nav align-items-center gap-2">
      <li class="nav-item">
        <a class="nav-link <?= $pagina_actual === 'index' ? 'activo' : '' ?>" href="/index.php">Inicio</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $pagina_actual === 'index' ? 'activo' : '' ?>#servicios" href="/index.php#servicios">Servicios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $pagina_actual === 'index' ? 'activo' : '' ?>#departamentos" href="/index.php#departamentos">Departamentos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $pagina_actual === 'index' ? 'activo' : '' ?>#contacto" href="/index.php#contacto">Contacto</a>
      </li>

      <?php if (isset($_SESSION['usuario'])): ?>
        <li class="nav-item">
          <a class="nav-link btn-login ms-2" href="/panel.php">
            👤 <?= htmlspecialchars($_SESSION['nombre']) ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn-cita" href="/controllers/AuthController.php?accion=cerrar">Cerrar sesión</a>
        </li>
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link btn-login ms-2 <?= $pagina_actual === 'login' ? 'activo' : '' ?>" href="/login.php">Acceso personal</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn-cita <?= $pagina_actual === 'citas' ? 'activo' : '' ?>" href="/citas.php">Pedir cita</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<main style="padding-top: 72px;">
