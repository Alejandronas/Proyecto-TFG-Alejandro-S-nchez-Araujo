<?php

class Usuario {
    public $id;
    public $username;
    public $rol;
    public $nombre;
    public $apellido;

    public function __construct($id, $username, $rol, $nombre, $apellido) {
        $this->id       = $id;
        $this->username = $username;
        $this->rol      = $rol;
        $this->nombre   = $nombre;
        $this->apellido = $apellido;
    }
}
?>
