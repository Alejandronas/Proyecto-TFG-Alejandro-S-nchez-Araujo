<?php

class Paciente {
    public $id;
    public $nombre;
    public $apellido;
    public $telefono;
    public $fecha_nacimiento;
    public $genero;
    public $direccion;

    public function __construct($id, $nombre, $apellido, $telefono, $fecha_nacimiento = null, $genero = null, $direccion = null) {
        $this->id               = $id;
        $this->nombre           = $nombre;
        $this->apellido         = $apellido;
        $this->telefono         = $telefono;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->genero           = $genero;
        $this->direccion        = $direccion;
    }

    public function nombreCompleto() {
        return $this->nombre . ' ' . $this->apellido;
    }
}
?>
