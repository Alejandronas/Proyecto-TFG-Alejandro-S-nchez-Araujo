<?php
include_once('models/CitaDAO.php');
include_once('models/PacienteDAO.php');
include_once('models/EmpleadoDAO.php');

class CitaController {

    // Pedir cita pública: devuelve 'ok', 'ocupado' o 'error'
    public function crearCita($nombre, $apellido, $telefono, $id_empleado, $fecha, $hora) {
        $pacienteDAO = new PacienteDAO();
        $citaDAO     = new CitaDAO();

        $id_paciente = $pacienteDAO->obtenerOCrear($nombre, $apellido, $telefono);

        if ($citaDAO->estaOcupado($id_empleado, $fecha, $hora)) return 'ocupado';

        $citaDAO->guardar($id_paciente, $id_empleado, $fecha, $hora);
        return 'ok';
    }

    // Citas próximas de un médico
    public function citasMedico($id_empleado) {
        $dao = new CitaDAO();
        return $dao->getCitasMedico($id_empleado);
    }

    // Citas de un día para recepción
    public function citasDia($fecha, $estado = null) {
        $dao = new CitaDAO();
        return $dao->getCitasDia($fecha, $estado);
    }

    // Cancelar cita
    public function cancelar($id_cita) {
        $dao = new CitaDAO();
        $dao->cancelar($id_cita);
    }
}
?>
