<?php
class UsuarioModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function buscarPorCredenciales($username, $password) {
        $sql = "SELECT * FROM USUARIO WHERE username = ? AND password = SHA2(?, 256)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
