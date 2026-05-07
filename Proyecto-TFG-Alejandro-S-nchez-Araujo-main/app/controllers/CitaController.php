<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'medico') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/CitaModel.php';

$modelo = new CitaModel($pdo);
$accion = $_GET['accion'] ?? '';

// ── GUARDAR (nueva cita) ──────────────────────────────────────────────────────
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_paciente' => $_POST['id_paciente'],
        'id_empleado' => $_SESSION['id_empleado'],
        'fecha_cita'  => $_POST['fecha_cita'],
        'hora_cita'   => $_POST['hora_cita']
    ];
    $modelo->crear($datos);
    header('Location: /citas.php');
    exit;
}

// ── ACTUALIZAR (editar cita) ──────────────────────────────────────────────────
if ($accion === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int) $_POST['id_cita'];
    $datos = [
        'fecha_cita' => $_POST['fecha_cita'],
        'hora_cita'  => $_POST['hora_cita'],
        'estado'     => $_POST['estado']
    ];
    $modelo->actualizar($id, $datos);
    header('Location: /citas.php');
    exit;
}

// ── ELIMINAR ──────────────────────────────────────────────────────────────────
if ($accion === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id_cita'];
    $modelo->eliminar($id);
    header('Location: /citas.php');
    exit;
}

// Si llega aquí sin acción válida, redirigir
header('Location: /citas.php');
exit;
