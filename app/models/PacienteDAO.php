<?php
include_once('db/config.php');
include_once('models/clases/Paciente.php');

class PacienteDAO {

    public $conexion;

    public function __construct() {
        $this->conexion = Database::conectar();
    }

    // Busca por teléfono; si no existe lo crea. Devuelve el id.
    public function obtenerOCrear($nombre, $apellido, $telefono) {
        $stmt = $this->conexion->prepare(
            "SELECT id_paciente FROM PACIENTE WHERE telefono = :tel"
        );
        $stmt->bindParam(':tel', $telefono);
        $stmt->execute();
        $id = $stmt->fetchColumn();

        if ($id) return (int)$id;

        $ins = $this->conexion->prepare(
            "INSERT INTO PACIENTE (nombre, apellido, telefono) VALUES (:nom, :ape, :tel)"
        );
        $ins->bindParam(':nom', $nombre);
        $ins->bindParam(':ape', $apellido);
        $ins->bindParam(':tel', $telefono);
        $ins->execute();
        $id = (int)$this->conexion->lastInsertId();

        // Crear historial vacío
        $this->conexion->prepare(
            "INSERT INTO HISTORIAL_MEDICO (id_paciente, antecedentes_familiares, alergias)
             VALUES (:id, '', 'Sin datos')"
        )->execute([':id' => $id]);

        return $id;
    }

    // Devuelve un Paciente con su historial
    public function getPacienteById($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM PACIENTE WHERE id_paciente = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $fila = $stmt->fetch();
        if (!$fila) return null;

        return new Paciente(
            $fila['id_paciente'], $fila['nombre'], $fila['apellido'],
            $fila['telefono'],    $fila['fecha_nacimiento'],
            $fila['genero'],      $fila['direccion']
        );
    }

    // Historial médico del paciente
    public function getHistorial($id_paciente) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM HISTORIAL_MEDICO WHERE id_paciente = :id"
        );
        $stmt->bindParam(':id', $id_paciente);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Consultas anteriores del paciente
    public function getConsultas($id_paciente) {
        $stmt = $this->conexion->prepare(
            "SELECT co.*, e.nombre AS nom_medico, e.apellido AS ape_medico
             FROM   CONSULTA co
             JOIN   EMPLEADO e ON e.id_empleado = co.id_empleado_sanitario
             WHERE  co.id_paciente = :id
             ORDER  BY co.fecha_consulta DESC"
        );
        $stmt->bindParam(':id', $id_paciente);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Guarda una nueva consulta y devuelve su id
    public function guardarConsulta($id_paciente, $id_empleado, $motivo, $diagnostico, $tratamiento) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO CONSULTA (id_paciente, id_empleado_sanitario, motivo_consulta, diagnostico, tratamiento)
             VALUES (:pac, :emp, :mot, :dia, :tra)"
        );
        $stmt->bindParam(':pac', $id_paciente);
        $stmt->bindParam(':emp', $id_empleado);
        $stmt->bindParam(':mot', $motivo);
        $stmt->bindParam(':dia', $diagnostico);
        $stmt->bindParam(':tra', $tratamiento);
        $stmt->execute();
        return (int)$this->conexion->lastInsertId();
    }

    // Busca pacientes por nombre, apellido o teléfono
    public function buscar($termino) {
        $like = '%' . $termino . '%';
        $stmt = $this->conexion->prepare(
            "SELECT id_paciente, nombre, apellido, telefono, fecha_nacimiento
             FROM   PACIENTE
             WHERE  nombre   LIKE :t1
                OR  apellido LIKE :t2
                OR  telefono LIKE :t3
             ORDER  BY apellido, nombre
             LIMIT  30"
        );
        $stmt->execute([':t1' => $like, ':t2' => $like, ':t3' => $like]);
        return $stmt->fetchAll();
    }

    // Resultados de laboratorio de un paciente
    public function getResultadosLaboratorio($id_paciente) {
        $stmt = $this->conexion->prepare(
            "SELECT rl.valor_obtenido, rl.observaciones, rl.fecha_procesamiento,
                    tp.nombre_prueba, tp.unidad_medida,
                    sa.prioridad, sa.fecha_solicitud
             FROM   RESULTADO_LABORATORIO rl
             JOIN   SOLICITUD_ANALISIS sa  ON sa.id_solicitud = rl.id_solicitud
             JOIN   TIPO_PRUEBA tp         ON tp.id_prueba    = rl.id_prueba
             JOIN   CONSULTA co            ON co.id_consulta  = sa.id_consulta
             WHERE  co.id_paciente = :id
             ORDER  BY rl.fecha_procesamiento DESC"
        );
        $stmt->bindParam(':id', $id_paciente);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Recetas de un paciente
    public function getRecetas($id_paciente) {
        $stmt = $this->conexion->prepare(
            "SELECT r.id_receta, r.fecha_emision,
                    e.nombre AS nom_medico, e.apellido AS ape_medico,
                    GROUP_CONCAT(CONCAT(dr.medicamento,' (',dr.dosis,') – ',dr.frecuencia) SEPARATOR ' | ') AS medicamentos
             FROM   RECETA r
             JOIN   CONSULTA co        ON co.id_consulta  = r.id_consulta
             JOIN   EMPLEADO e         ON e.id_empleado   = co.id_empleado_sanitario
             JOIN   DETALLE_RECETA dr  ON dr.id_receta    = r.id_receta
             WHERE  co.id_paciente = :id
             GROUP  BY r.id_receta
             ORDER  BY r.fecha_emision DESC"
        );
        $stmt->bindParam(':id', $id_paciente);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Todos los pacientes (para selector en admin)
    public function getTodos() {
        return $this->conexion->query(
            "SELECT id_paciente, nombre, apellido FROM PACIENTE ORDER BY apellido, nombre"
        )->fetchAll();
    }
}
?>
