<?php
include_once('models/UsuarioDAO.php');

class LoginController {

    public function login($username, $password) {
        $dao = new UsuarioDAO();
        return $dao->validar($username, $password);
    }
}
?>
