<?php
class CitaModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Citas de hoy para las tarjetas de estado
    public function obtenerCitasMedico($id_empleado) {
        $sql = "SELECT c.id_cita, c.hora_cita, c.fecha_cita, c.estado,
                       p.nombre, p.apellido
                FROM CITA c
                JOIN PACIENTE p ON c.id_paciente = p.id_paciente
                WHERE c.id_empleado_sanitario = ?
                AND c.fecha_cita = CURDATE()
                ORDER BY c.hora_cita ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_empleado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Todas las citas del médico para el calendario
    public function obtenerTodasCitasMedico($id_empleado) {
        $sql = "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.estado,
                       p.nombre, p.apellido
                FROM CITA c
                JOIN PACIENTE p ON c.id_paciente = p.id_paciente
                WHERE c.id_empleado_sanitario = ?
                ORDER BY c.fecha_cita ASC, c.hora_cita ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_empleado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Próximas citas (esta semana) para la tabla lateral
    public function obtenerProximasCitas($id_empleado) {
        $sql = "SELECT c.fecha_cita, c.hora_cita, c.estado,
                       p.nombre, p.apellido
                FROM CITA c
                JOIN PACIENTE p ON c.id_paciente = p.id_paciente
                WHERE c.id_empleado_sanitario = ?
                AND c.fecha_cita BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ORDER BY c.fecha_cita ASC, c.hora_cita ASC
                LIMIT 6";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_empleado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Número de pacientes distintos del médico
    public function contarPacientesMedico($id_empleado) {
        $sql = "SELECT COUNT(DISTINCT id_paciente) as total
                FROM CITA
                WHERE id_empleado_sanitario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_empleado]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Obtener todos los pacientes (para el select del formulario)
    public function obtenerPacientes() {
        $sql = "SELECT id_paciente, nombre, apellido FROM PACIENTE ORDER BY apellido ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear una cita nueva
    public function crear($datos) {
        $sql = "INSERT INTO CITA (id_paciente, id_empleado_sanitario, fecha_cita, hora_cita, estado)
                VALUES (?, ?, ?, ?, 'programada')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $datos['id_paciente'],
            $datos['id_empleado'],
            $datos['fecha_cita'],
            $datos['hora_cita']
        ]);
    }

    // Actualizar una cita existente
    public function actualizar($id, $datos) {
        $sql = "UPDATE CITA SET fecha_cita = ?, hora_cita = ?, estado = ?
                WHERE id_cita = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $datos['fecha_cita'],
            $datos['hora_cita'],
            $datos['estado'],
            $id
        ]);
    }

    // Eliminar una cita
    public function eliminar($id) {
        $sql  = "DELETE FROM CITA WHERE id_cita = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
    }

    // ── MÉTODOS PARA PACIENTE ─────────────────────────────────────────────────

    // Todas las citas del paciente
    public function obtenerCitasPaciente($id_paciente) {
        $sql = "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.estado,
                       e.nombre AS medico_nombre, e.apellido AS medico_apellido,
                       esp.nombre AS especialidad
                FROM CITA c
                JOIN EMPLEADO e   ON c.id_empleado_sanitario = e.id_empleado
                LEFT JOIN ESPECIALIDAD esp ON e.id_especialidad = esp.id_especialidad
                WHERE c.id_paciente = ?
                ORDER BY c.fecha_cita DESC, c.hora_cita DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_paciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Próximas citas del paciente (7 días)
    public function obtenerProximasCitasPaciente($id_paciente) {
        $sql = "SELECT c.fecha_cita, c.hora_cita, c.estado,
                       e.nombre AS medico_nombre, e.apellido AS medico_apellido,
                       esp.nombre AS especialidad
                FROM CITA c
                JOIN EMPLEADO e   ON c.id_empleado_sanitario = e.id_empleado
                LEFT JOIN ESPECIALIDAD esp ON e.id_especialidad = esp.id_especialidad
                WHERE c.id_paciente = ?
                AND c.fecha_cita >= CURDATE()
                ORDER BY c.fecha_cita ASC, c.hora_cita ASC
                LIMIT 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_paciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Médico asignado al paciente
    public function obtenerMedicoAsignado($id_paciente) {
        $sql = "SELECT e.nombre, e.apellido, esp.nombre AS especialidad
                FROM MEDICO_PACIENTE mp
                JOIN EMPLEADO e ON mp.id_empleado = e.id_empleado
                LEFT JOIN ESPECIALIDAD esp ON e.id_especialidad = esp.id_especialidad
                WHERE mp.id_paciente = ?
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_paciente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Especialidades disponibles (para solicitar cita)
    public function obtenerEspecialidades() {
        $sql  = "SELECT id_especialidad, nombre FROM ESPECIALIDAD ORDER BY nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Médicos de una especialidad (para solicitar cita)
    public function obtenerMedicosPorEspecialidad($id_especialidad) {
        $sql = "SELECT e.id_empleado, e.nombre, e.apellido
                FROM EMPLEADO e
                WHERE e.id_especialidad = ? AND e.rol = 'medico'
                ORDER BY e.apellido ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_especialidad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Solicitar nueva cita (desde el paciente)
    public function solicitarCita($datos) {
        $sql = "INSERT INTO CITA (id_paciente, id_empleado_sanitario, fecha_cita, hora_cita, estado)
                VALUES (?, NULL, ?, ?, 'programada')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $datos['id_paciente'],
            $datos['fecha_cita'],
            $datos['hora_cita']
        ]);
    }
}
