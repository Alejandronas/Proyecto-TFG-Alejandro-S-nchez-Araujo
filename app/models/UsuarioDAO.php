<?php
include_once('db/config.php');
include_once('models/clases/Usuario.php');

class UsuarioDAO {

    public $conexion;

    public function __construct() {
        $this->conexion = Database::conectar();
    }

    // Valida credenciales y devuelve un objeto Usuario o null
    public function validar($username, $password) {
        $hash = hash('sha256', $password);

        $stmt = $this->conexion->prepare(
            "SELECT u.id_usuario, u.username, u.rol,
                    e.nombre, e.apellido
             FROM   USUARIO  u
             JOIN   EMPLEADO e ON e.id_empleado = u.id_empleado
             WHERE  u.username = :username AND u.password = :password"
        );
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hash);
        $stmt->execute();

        $fila = $stmt->fetch();
        if (!$fila) return null;

        return new Usuario(
            $fila['id_usuario'],
            $fila['username'],
            $fila['rol'],
            $fila['nombre'],
            $fila['apellido']
        );
    }
}
?>
