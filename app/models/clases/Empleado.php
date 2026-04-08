<?php

class Empleado {
    public $id;
    public $nombre;
    public $apellido;
    public $rol;
    public $salario;
    public $departamento;

    public function __construct($id, $nombre, $apellido, $rol, $salario = null, $departamento = null) {
        $this->id          = $id;
        $this->nombre      = $nombre;
        $this->apellido    = $apellido;
        $this->rol         = $rol;
        $this->salario     = $salario;
        $this->departamento = $departamento;
    }

    public function nombreCompleto() {
        return $this->nombre . ' ' . $this->apellido;
    }
}
?>
