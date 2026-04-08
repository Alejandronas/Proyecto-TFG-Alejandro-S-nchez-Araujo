<?php
include_once('db/config.php');
include_once('models/clases/Cita.php');

class CitaDAO {

    public $conexion;

    public function __construct() {
        $this->conexion = Database::conectar();
    }

    // Citas de un médico desde hoy
    public function getCitasMedico($id_empleado) {
        $stmt = $this->conexion->prepare(
            "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.estado,
                    p.id_paciente,
                    p.nombre AS nom_p, p.apellido AS ape_p, p.telefono
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

    // Citas de un día (para recepción)
    public function getCitasDia($fecha, $estado = null) {
        $sql = "SELECT c.id_cita, c.hora_cita, c.estado,
                       p.nombre AS nom_p, p.apellido AS ape_p, p.telefono,
                       e.nombre AS nom_e, e.apellido AS ape_e, e.rol
                FROM   CITA c
                JOIN   PACIENTE p ON p.id_paciente = c.id_paciente
                JOIN   EMPLEADO e ON e.id_empleado = c.id_empleado_sanitario
                WHERE  c.fecha_cita = :fecha";

        if ($estado !== null) $sql .= " AND c.estado = :estado";
        $sql .= " ORDER BY c.hora_cita ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha);
        if ($estado !== null) $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Comprueba si ya hay cita en ese hueco
    public function estaOcupado($id_empleado, $fecha, $hora) {
        $stmt = $this->conexion->prepare(
            "SELECT COUNT(*) FROM CITA
             WHERE id_empleado_sanitario = :emp
               AND fecha_cita = :fecha AND hora_cita = :hora
               AND estado != 'cancelada'"
        );
        $stmt->bindParam(':emp',   $id_empleado);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora',  $hora);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Inserta una nueva cita
    public function guardar($id_paciente, $id_empleado, $fecha, $hora) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO CITA (id_paciente, id_empleado_sanitario, fecha_cita, hora_cita, estado)
             VALUES (:pac, :emp, :fecha, :hora, 'programada')"
        );
        $stmt->bindParam(':pac',   $id_paciente);
        $stmt->bindParam(':emp',   $id_empleado);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora',  $hora);
        $stmt->execute();
    }

    // Cancela una cita
    public function cancelar($id_cita) {
        $stmt = $this->conexion->prepare(
            "UPDATE CITA SET estado = 'cancelada' WHERE id_cita = :id"
        );
        $stmt->bindParam(':id', $id_cita);
        $stmt->execute();
    }

    // Marca una cita como completada
    public function completar($id_cita) {
        $stmt = $this->conexion->prepare(
            "UPDATE CITA SET estado = 'completada' WHERE id_cita = :id"
        );
        $stmt->bindParam(':id', $id_cita);
        $stmt->execute();
    }

    // Obtiene el id_paciente de una cita (para el médico)
    public function getPacienteDeCita($id_cita, $id_empleado) {
        $stmt = $this->conexion->prepare(
            "SELECT id_paciente FROM CITA
             WHERE id_cita = :cita AND id_empleado_sanitario = :emp"
        );
        $stmt->bindParam(':cita', $id_cita);
        $stmt->bindParam(':emp',  $id_empleado);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
