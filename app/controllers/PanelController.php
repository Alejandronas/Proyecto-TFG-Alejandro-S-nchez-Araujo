<?php
include_once('models/EmpleadoDAO.php');
include_once('models/CitaDAO.php');
include_once('models/PacienteDAO.php');
include_once('models/UsuarioDAO.php');

class PanelController {

    private $pdo;

    public function __construct() {
        $this->pdo = Database::conectar();
    }

    // Devuelve los datos que necesita el panel según el rol
    public function obtenerDatos($username, $rol) {
        $empDAO = new EmpleadoDAO();
        $datos  = [];

        $datos['id_empleado'] = $empDAO->getIdEmpleado($username);

        switch ($rol) {

            case 'medico':
                $datos['citas']     = $this->citasMedico($datos['id_empleado']);
                $datos['consultas'] = $this->ultimasConsultas($datos['id_empleado']);
                // Buscador de pacientes
                $termino = trim($_GET['buscar'] ?? '');
                $datos['buscar']           = $termino;
                $datos['resultado_buscar'] = $termino ? (new PacienteDAO())->buscar($termino) : [];
                break;

            case 'recepcionista':
                $fecha = $_GET['fecha'] ?? date('Y-m-d');
                $datos['fecha']   = $fecha;
                $datos['citas']   = $this->citasDia($fecha);
                $datos['medicos'] = (new EmpleadoDAO())->getSanitarios();
                break;

            case 'administrador':
                $datos['stats']        = $this->statsGenerales();
                $datos['empleados']    = $this->empleadosConDept();
                $datos['facturas']     = $this->facturas();
                $datos['departamentos'] = $this->departamentos();
                $datos['usuarios']     = $this->usuarios();
                $datos['pacientes']    = (new PacienteDAO())->getTodos();
                break;

            case 'enfermera':
                $datos['citas_hoy'] = $this->citasDia(date('Y-m-d'));
                $datos['alergicos'] = $this->pacientesConAlergias();
                break;

            case 'laboratorio':
                $datos['solicitudes'] = $this->solicitudesAnalisis();
                $datos['tipos']       = $this->tiposPrueba();
                break;
        }

        return $datos;
    }

    // ── MÉDICO ────────────────────────────────────────────────

    private function citasMedico($id_empleado) {
        $stmt = $this->pdo->prepare(
            "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.estado,
                    p.id_paciente, p.nombre AS nom_p, p.apellido AS ape_p, p.telefono
             FROM   CITA c
             JOIN   PACIENTE p ON p.id_paciente = c.id_paciente
             WHERE  c.id_empleado_sanitario = :emp
               AND  c.fecha_cita >= CURDATE()
             ORDER  BY c.fecha_cita ASC, c.hora_cita ASC
             LIMIT  20"
        );
        $stmt->bindParam(':emp', $id_empleado);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function ultimasConsultas($id_empleado) {
        $stmt = $this->pdo->prepare(
            "SELECT co.id_consulta, co.fecha_consulta, co.motivo_consulta, co.diagnostico,
                    p.nombre AS nom_p, p.apellido AS ape_p
             FROM   CONSULTA co
             JOIN   PACIENTE p ON p.id_paciente = co.id_paciente
             WHERE  co.id_empleado_sanitario = :emp
             ORDER  BY co.fecha_consulta DESC
             LIMIT  10"
        );
        $stmt->bindParam(':emp', $id_empleado);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── RECEPCIONISTA / ENFERMERA ─────────────────────────────

    private function citasDia($fecha) {
        $stmt = $this->pdo->prepare(
            "SELECT c.id_cita, c.hora_cita, c.estado,
                    p.nombre AS nom_p, p.apellido AS ape_p, p.telefono,
                    e.nombre AS nom_e, e.apellido AS ape_e, e.rol
             FROM   CITA c
             JOIN   PACIENTE p ON p.id_paciente = c.id_paciente
             JOIN   EMPLEADO e ON e.id_empleado = c.id_empleado_sanitario
             WHERE  c.fecha_cita = :fecha
             ORDER  BY c.hora_cita ASC"
        );
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── ADMINISTRADOR ─────────────────────────────────────────

    private function statsGenerales() {
        return [
            'empleados'  => $this->pdo->query("SELECT COUNT(*) FROM EMPLEADO")->fetchColumn(),
            'pacientes'  => $this->pdo->query("SELECT COUNT(*) FROM PACIENTE")->fetchColumn(),
            'citas_hoy'  => $this->pdo->query("SELECT COUNT(*) FROM CITA WHERE fecha_cita = CURDATE()")->fetchColumn(),
            'pendientes' => $this->pdo->query("SELECT COUNT(*) FROM FACTURA WHERE estado = 'pendiente'")->fetchColumn(),
        ];
    }

    private function empleadosConDept() {
        return $this->pdo->query(
            "SELECT e.id_empleado, e.nombre, e.apellido, e.rol, e.salario,
                    d.nombre AS departamento
             FROM   EMPLEADO e
             JOIN   DEPARTAMENTO d ON d.id_departamento = e.id_departamento
             ORDER  BY d.nombre, e.nombre"
        )->fetchAll();
    }

    private function facturas() {
        return $this->pdo->query(
            "SELECT f.id_factura, f.fecha, f.total, f.estado,
                    p.nombre AS nom_p, p.apellido AS ape_p
             FROM   FACTURA f
             JOIN   PACIENTE p ON p.id_paciente = f.id_paciente
             ORDER  BY f.estado ASC, f.fecha DESC
             LIMIT  30"
        )->fetchAll();
    }

    // ── ENFERMERÍA ────────────────────────────────────────────

    private function pacientesConAlergias() {
        return $this->pdo->query(
            "SELECT p.nombre, p.apellido, h.alergias
             FROM   HISTORIAL_MEDICO h
             JOIN   PACIENTE p ON p.id_paciente = h.id_paciente
             WHERE  h.alergias NOT IN ('Ninguna conocida','Sin datos','')
             ORDER  BY p.nombre LIMIT 20"
        )->fetchAll();
    }

    // ── LABORATORIO ───────────────────────────────────────────

    private function solicitudesAnalisis() {
        return $this->pdo->query(
            "SELECT sa.id_solicitud, sa.prioridad, sa.fecha_solicitud,
                    p.nombre, p.apellido, co.motivo_consulta,
                    COUNT(rl.id_resultado) AS num_resultados
             FROM   SOLICITUD_ANALISIS sa
             JOIN   CONSULTA co ON co.id_consulta = sa.id_consulta
             JOIN   PACIENTE p  ON p.id_paciente  = co.id_paciente
             LEFT JOIN RESULTADO_LABORATORIO rl ON rl.id_solicitud = sa.id_solicitud
             GROUP  BY sa.id_solicitud
             ORDER  BY sa.prioridad DESC, sa.fecha_solicitud ASC"
        )->fetchAll();
    }

    private function departamentos() {
        return $this->pdo->query(
            "SELECT id_departamento, nombre FROM DEPARTAMENTO ORDER BY nombre"
        )->fetchAll();
    }

    private function usuarios() {
        return $this->pdo->query(
            "SELECT u.id_usuario, u.username, u.rol,
                    e.nombre, e.apellido, e.id_empleado
             FROM   USUARIO u
             JOIN   EMPLEADO e ON e.id_empleado = u.id_empleado
             ORDER  BY e.nombre"
        )->fetchAll();
    }

    private function tiposPrueba() {
        return $this->pdo->query(
            "SELECT id_prueba, nombre_prueba, unidad_medida FROM TIPO_PRUEBA ORDER BY nombre_prueba"
        )->fetchAll();
    }
}
?>
