<?php

class Cita {
    public $id;
    public $id_paciente;
    public $id_empleado;
    public $fecha;
    public $hora;
    public $estado;
    // Para joins
    public $nombre_paciente;
    public $apellido_paciente;
    public $telefono_paciente;
    public $nombre_medico;
    public $apellido_medico;

    public function __construct($id, $id_paciente, $id_empleado, $fecha, $hora, $estado) {
        $this->id          = $id;
        $this->id_paciente = $id_paciente;
        $this->id_empleado = $id_empleado;
        $this->fecha       = $fecha;
        $this->hora        = $hora;
        $this->estado      = $estado;
    }
}
?>
