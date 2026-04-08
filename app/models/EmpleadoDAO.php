<?php
include_once('db/config.php');
include_once('models/clases/Empleado.php');

class EmpleadoDAO {

    public $conexion;

    public function __construct() {
        $this->conexion = Database::conectar();
    }

    // Devuelve médicos y enfermeras (para selector de citas)
    public function getSanitarios() {
        $stmt = $this->conexion->prepare(
            "SELECT id_empleado, nombre, apellido, rol
             FROM   EMPLEADO
             WHERE  rol IN ('medico', 'enfermera')
             ORDER  BY nombre"
        );
        $stmt->execute();
        $lista = [];
        foreach ($stmt->fetchAll() as $f) {
            $lista[] = new Empleado($f['id_empleado'], $f['nombre'], $f['apellido'], $f['rol']);
        }
        return $lista;
    }

    // Devuelve todos los empleados con departamento (para admin)
    public function getTodos() {
        $stmt = $this->conexion->prepare(
            "SELECT e.id_empleado, e.nombre, e.apellido, e.rol, e.salario,
                    d.nombre AS departamento
             FROM   EMPLEADO e
             JOIN   DEPARTAMENTO d ON d.id_departamento = e.id_departamento
             ORDER  BY d.nombre, e.nombre"
        );
        $stmt->execute();
        $lista = [];
        foreach ($stmt->fetchAll() as $f) {
            $lista[] = new Empleado($f['id_empleado'], $f['nombre'], $f['apellido'], $f['rol'], $f['salario'], $f['departamento']);
        }
        return $lista;
    }

    // Devuelve el id_empleado a partir del username
    public function getIdEmpleado($username) {
        $stmt = $this->conexion->prepare(
            "SELECT id_empleado FROM USUARIO WHERE username = :u"
        );
        $stmt->bindParam(':u', $username);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
?>
