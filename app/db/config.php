<?php

class Database {

    private static $instancia = null;

    public static function conectar() {

        if (self::$instancia !== null) {
            return self::$instancia;
        }

        $host = '10.0.20.10';
        $db   = 'clinica';
        $user = 'clinica_user';
        $pass = '1234';

        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$instancia = $pdo;
            return self::$instancia;
        } catch (PDOException $e) {
            die("Error de conexión con la base de datos: " . $e->getMessage());
        }
    }
}
?>
