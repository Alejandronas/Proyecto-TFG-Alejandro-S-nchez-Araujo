<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'paciente') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/PacienteModel.php';
require_once __DIR__ . '/../models/CitaModel.php';

$modeloPaciente = new PacienteModel($pdo);
$modeloCita     = new CitaModel($pdo);
$accion         = $_GET['accion'] ?? '';
$id_paciente    = $_SESSION['id_paciente'];

// ── ACTUALIZAR DATOS PERSONALES ───────────────────────────────────────────────
if ($accion === 'actualizar_perfil' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre'    => trim($_POST['nombre']),
        'apellido'  => trim($_POST['apellido']),
        'telefono'  => trim($_POST['telefono']  ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'email'     => trim($_POST['email']     ?? '')
    ];
    $modeloPaciente->actualizarPerfil($id_paciente, $datos);
    $_SESSION['nombre'] = $datos['nombre'];
    header('Location: /perfil_paciente.php?ok=datos');
    exit;
}

// ── ACTUALIZAR CONTRASEÑA ─────────────────────────────────────────────────────
if ($accion === 'actualizar_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual   = $_POST['password_actual']   ?? '';
    $nueva    = $_POST['password_nueva']    ?? '';
    $confirma = $_POST['password_confirma'] ?? '';

    if ($nueva !== $confirma) {
        header('Location: /perfil_paciente.php?error=no_coinciden');
        exit;
    }
    if (strlen($nueva) < 6) {
        header('Location: /perfil_paciente.php?error=muy_corta');
        exit;
    }
    if (!$modeloPaciente->verificarPasswordPaciente($id_paciente, $actual)) {
        header('Location: /perfil_paciente.php?error=incorrecta');
        exit;
    }
    $modeloPaciente->actualizarPasswordPaciente($id_paciente, $nueva);
    header('Location: /perfil_paciente.php?ok=password');
    exit;
}

// ── SOLICITAR CITA ────────────────────────────────────────────────────────────
if ($accion === 'solicitar_cita' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_paciente' => $id_paciente,
        'id_empleado' => (int) $_POST['id_empleado'],
        'fecha_cita'  => $_POST['fecha_cita'],
        'hora_cita'   => $_POST['hora_cita']
    ];
    $modeloCita->solicitarCita($datos);
    header('Location: /citas_paciente.php?ok=1');
    exit;
}

header('Location: /panel.php');
exit;
