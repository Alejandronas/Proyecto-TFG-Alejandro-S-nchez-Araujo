<?php
session_start();

// Si ya está logueado, va al panel directamente
if (isset($_SESSION['usuario'])) {
    header('Location: /panel.php');
    exit;
}

$titulo_pagina = 'Acceso personal';
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 420px; margin-top: 60px;">
    <h2 class="mb-4 text-center">Acceso personal</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Usuario o contraseña incorrectos.</div>
    <?php endif; ?>

    <form method="POST" action="/controllers/AuthController.php?accion=login">
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Entrar</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
