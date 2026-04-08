<?php
session_start();

include_once('controllers/LoginController.php');
include_once('controllers/CitaController.php');
include_once('controllers/PanelController.php');
include_once('models/EmpleadoDAO.php');
include_once('models/PacienteDAO.php');
include_once('models/CitaDAO.php');
include_once('models/UsuarioDAO.php');

$vista = $_GET['vista'] ?? 'inicio';

switch ($vista) {

    // ── INICIO ──────────────────────────────────────────────
    case 'inicio':
        include_once('views/inicio.php');
        break;

    // ── LOGIN ───────────────────────────────────────────────
    case 'login':
        if (isset($_SESSION['usuario'])) {
            header('Location: index.php?vista=panel');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new LoginController();
            $usuario    = $controller->login(
                $_POST['username'] ?? '',
                $_POST['password'] ?? ''
            );

            if ($usuario) {
                session_regenerate_id(true);
                $_SESSION['usuario'] = $usuario->username;
                $_SESSION['nombre']  = $usuario->nombre . ' ' . $usuario->apellido;
                $_SESSION['rol']     = $usuario->rol;
                header('Location: index.php?vista=panel');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        }
        include_once('views/login.php');
        break;

    // ── CERRAR SESIÓN ────────────────────────────────────────
    case 'cerrar':
        session_destroy();
        header('Location: index.php');
        exit;

    // ── CITAS PÚBLICAS ───────────────────────────────────────
    case 'citas':
        $empDAO = new EmpleadoDAO();
        $medicos = $empDAO->getSanitarios();
        $exito = false;
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre']      ?? '');
            $apellido    = trim($_POST['apellido']    ?? '');
            $telefono    = trim($_POST['telefono']    ?? '');
            $id_empleado = (int)($_POST['id_empleado'] ?? 0);
            $fecha       = $_POST['fecha'] ?? '';
            $hora        = $_POST['hora']  ?? '';

            if (!$nombre || !$apellido || !$telefono || !$id_empleado || !$fecha || !$hora) {
                $error = 'Rellena todos los campos.';
            } else {
                $controller = new CitaController();
                $resultado  = $controller->crearCita($nombre, $apellido, $telefono, $id_empleado, $fecha, $hora);

                if ($resultado === 'ok')           $exito = true;
                elseif ($resultado === 'ocupado')  $error = 'Ese horario ya está ocupado. Elige otra hora.';
                else                               $error = 'Error al guardar. Inténtalo de nuevo.';
            }
        }
        include_once('views/citas.php');
        break;

    // ── PANEL (protegido por sesión) ─────────────────────────
    case 'panel':
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?vista=login');
            exit;
        }

        $rol    = $_SESSION['rol'];
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
        $msg    = '';

        // Acciones POST del panel recepcionista
        if ($rol === 'recepcionista') {
            if ($accion === 'nueva_cita') {
                $controller = new CitaController();
                $res = $controller->crearCita(
                    $_POST['nombre'] ?? '', $_POST['apellido'] ?? '',
                    $_POST['telefono'] ?? '', (int)($_POST['id_empleado'] ?? 0),
                    $_POST['fecha'] ?? '', $_POST['hora'] ?? ''
                );
                $msg = ($res === 'ok') ? 'creada' : ($res === 'ocupado' ? 'ocupado' : 'error');
            }
            if ($accion === 'cancelar' && isset($_POST['id_cita'])) {
                $controller = new CitaController();
                $controller->cancelar((int)$_POST['id_cita']);
                $msg = 'cancelada';
            }
        }

        // Acción POST del panel laboratorio
        $msg_lab = '';
        if ($rol === 'laboratorio' && $accion === 'guardar_resultado') {
            $pdo    = Database::conectar();
            $id_sol = (int)($_POST['id_solicitud'] ?? 0);
            $id_pru = (int)($_POST['id_prueba']    ?? 0);
            $valor  = $_POST['valor']              ?? '';
            $obs    = trim($_POST['observaciones'] ?? '');
            $msg_lab = 'error';
            if ($id_sol && $id_pru && $valor !== '') {
                try {
                    $pdo->prepare(
                        "INSERT INTO RESULTADO_LABORATORIO (id_solicitud, id_prueba, valor_obtenido, observaciones)
                         VALUES (:sol, :pru, :val, :obs)
                         ON DUPLICATE KEY UPDATE valor_obtenido=VALUES(valor_obtenido), observaciones=VALUES(observaciones)"
                    )->execute([':sol' => $id_sol, ':pru' => $id_pru, ':val' => $valor, ':obs' => $obs]);
                    $msg_lab = 'ok';
                } catch (PDOException $e) {}
            }
        }

        // Acciones POST del panel administrador
        $msg_emp = '';
        $msg_fac = '';
        $msg_usr = '';
        $msg_usr_detalle = '';
        if ($rol === 'administrador') {
            $pdo = Database::conectar();

            if ($accion === 'nuevo_empleado') {
                $nom    = trim($_POST['nombre']           ?? '');
                $ape    = trim($_POST['apellido']         ?? '');
                $rol_e  = $_POST['rol']                   ?? '';
                $dept   = (int)($_POST['id_departamento'] ?? 0);
                $sal    = (float)($_POST['salario']       ?? 0);
                if ($nom && $ape && $rol_e && $dept) {
                    try {
                        $pdo->prepare(
                            "INSERT INTO EMPLEADO (nombre, apellido, rol, id_departamento, salario, fecha_contratacion)
                             VALUES (:nom, :ape, :rol, :dept, :sal, CURDATE())"
                        )->execute([':nom' => $nom, ':ape' => $ape, ':rol' => $rol_e, ':dept' => $dept, ':sal' => $sal]);
                        $msg_emp = 'ok';
                    } catch (PDOException $e) { $msg_emp = 'error'; }
                } else { $msg_emp = 'error'; }
            }

            if ($accion === 'eliminar_empleado' && isset($_POST['id_empleado'])) {
                try {
                    $pdo->prepare("DELETE FROM EMPLEADO WHERE id_empleado = :id")
                        ->execute([':id' => (int)$_POST['id_empleado']]);
                    $msg_emp = 'ok';
                } catch (PDOException $e) { $msg_emp = 'error'; }
            }

            if ($accion === 'pagar_factura' && isset($_POST['id_factura'])) {
                $pdo->prepare("UPDATE FACTURA SET estado = 'pagada' WHERE id_factura = :id")
                    ->execute([':id' => (int)$_POST['id_factura']]);
                $msg_fac = 'ok';
            }

            if ($accion === 'nueva_factura') {
                $id_pac = (int)($_POST['id_paciente'] ?? 0);
                $total  = (float)($_POST['total']     ?? 0);
                $estado = in_array($_POST['estado'] ?? '', ['pendiente','pagada']) ? $_POST['estado'] : 'pendiente';
                if ($id_pac && $total > 0) {
                    try {
                        $pdo->prepare(
                            "INSERT INTO FACTURA (id_paciente, fecha, total, estado)
                             VALUES (:pac, CURDATE(), :tot, :est)"
                        )->execute([':pac' => $id_pac, ':tot' => $total, ':est' => $estado]);
                        $msg_fac = 'ok';
                    } catch (PDOException $e) { $msg_fac = 'error'; }
                } else { $msg_fac = 'error'; }
            }

            if ($accion === 'nuevo_usuario') {
                $id_emp   = (int)($_POST['id_empleado'] ?? 0);
                $username = trim($_POST['username']     ?? '');
                $password = $_POST['password']          ?? '';
                if ($id_emp && $username && $password) {
                    try {
                        // Obtener rol del empleado
                        $emp_row = $pdo->prepare("SELECT rol FROM EMPLEADO WHERE id_empleado = :id");
                        $emp_row->execute([':id' => $id_emp]);
                        $emp_data = $emp_row->fetch();
                        $rol_emp  = $emp_data ? $emp_data['rol'] : 'medico';
                        $pass_hash = hash('sha256', $password);
                        $pdo->prepare(
                            "INSERT INTO USUARIO (id_empleado, username, password, rol)
                             VALUES (:emp, :usr, :pwd, :rol)"
                        )->execute([':emp' => $id_emp, ':usr' => $username, ':pwd' => $pass_hash, ':rol' => $rol_emp]);
                        $msg_usr = 'ok';
                    } catch (PDOException $e) {
                        $msg_usr = 'error';
                        $msg_usr_detalle = strpos($e->getMessage(), 'Duplicate') !== false
                            ? 'El nombre de usuario ya existe.' : $e->getMessage();
                    }
                } else { $msg_usr = 'error'; $msg_usr_detalle = 'Rellena todos los campos.'; }
            }

            if ($accion === 'eliminar_usuario' && isset($_POST['id_usuario'])) {
                try {
                    $pdo->prepare("DELETE FROM USUARIO WHERE id_usuario = :id")
                        ->execute([':id' => (int)$_POST['id_usuario']]);
                    $msg_usr = 'ok';
                } catch (PDOException $e) { $msg_usr = 'error'; }
            }
        }

        // Cargar datos del panel
        $controller = new PanelController();
        $datos      = $controller->obtenerDatos($_SESSION['usuario'], $rol);

        $vista_archivo = 'views/panel_' . $rol . '.php';
        if (file_exists($vista_archivo)) {
            include_once($vista_archivo);
        } else {
            echo "<p class='container mt-4 text-muted'>Rol «$rol» sin panel configurado.</p>";
        }
        break;

    // ── HISTORIAL (médico) ───────────────────────────────────
    case 'historial':
        if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'medico') {
            header('Location: index.php?vista=login');
            exit;
        }

        $empDAO      = new EmpleadoDAO();
        $id_empleado = $empDAO->getIdEmpleado($_SESSION['usuario']);

        $id_cita     = (int)($_GET['id_cita']     ?? 0);
        $id_paciente = (int)($_GET['id_paciente'] ?? 0);

        // Si viene desde una cita, obtener el paciente
        if ($id_cita && !$id_paciente) {
            $citaDAO     = new CitaDAO();
            $id_paciente = (int)($citaDAO->getPacienteDeCita($id_cita, $id_empleado) ?: 0);
        }

        if (!$id_paciente) { header('Location: index.php?vista=panel'); exit; }

        $pacienteDAO = new PacienteDAO();
        $paciente    = $pacienteDAO->getPacienteById($id_paciente);
        if (!$paciente) { header('Location: index.php?vista=panel'); exit; }

        $historial     = $pacienteDAO->getHistorial($id_paciente);
        $consultas     = $pacienteDAO->getConsultas($id_paciente);
        $recetas       = $pacienteDAO->getRecetas($id_paciente);
        $resultados_lab = $pacienteDAO->getResultadosLaboratorio($id_paciente);
        $msg_ok        = false;
        $msg_receta    = '';

        // Guardar nueva consulta (y receta opcional)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion_h    = $_POST['accion'] ?? '';
            $motivo      = trim($_POST['motivo']      ?? '');
            $diagnostico = trim($_POST['diagnostico'] ?? '');
            $tratamiento = trim($_POST['tratamiento'] ?? '');

            if ($motivo && $diagnostico) {
                $id_consulta = $pacienteDAO->guardarConsulta($id_paciente, $id_empleado, $motivo, $diagnostico, $tratamiento);

                if ($id_cita) {
                    $citaDAO = new CitaDAO();
                    $citaDAO->completar($id_cita);
                }
                $msg_ok = true;

                // Guardar receta si hay medicamentos
                $medicamentos = $_POST['medicamento'] ?? [];
                if (!empty($medicamentos) && array_filter($medicamentos)) {
                    try {
                        $pdo = Database::conectar();
                        $pdo->prepare(
                            "INSERT INTO RECETA (id_consulta, fecha_emision)
                             VALUES (:con, CURDATE())"
                        )->execute([':con' => $id_consulta]);
                        $id_receta = (int)$pdo->lastInsertId();

                        $stmt_det = $pdo->prepare(
                            "INSERT INTO DETALLE_RECETA (id_receta, medicamento, dosis, frecuencia, duracion)
                             VALUES (:rec, :med, :dos, :fre, :dur)"
                        );
                        $dosis_arr     = $_POST['dosis']      ?? [];
                        $frecuencia_arr = $_POST['frecuencia'] ?? [];
                        $duracion_arr  = $_POST['duracion']   ?? [];
                        foreach ($medicamentos as $i => $med) {
                            if (!trim($med)) continue;
                            $stmt_det->execute([
                                ':rec' => $id_receta,
                                ':med' => trim($med),
                                ':dos' => trim($dosis_arr[$i] ?? ''),
                                ':fre' => trim($frecuencia_arr[$i] ?? ''),
                                ':dur' => trim($duracion_arr[$i] ?? ''),
                            ]);
                        }
                        $msg_receta = 'ok';
                    } catch (PDOException $e) {
                        $msg_receta = 'error';
                    }
                }

                // Recargar datos
                $consultas      = $pacienteDAO->getConsultas($id_paciente);
                $recetas        = $pacienteDAO->getRecetas($id_paciente);
                $resultados_lab = $pacienteDAO->getResultadosLaboratorio($id_paciente);
            }
        }

        include_once('views/historial.php');
        break;

    // ── DEFECTO ──────────────────────────────────────────────
    default:
        include_once('views/inicio.php');
        break;
}
?>
