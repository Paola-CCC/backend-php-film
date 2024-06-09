<?php

namespace App\Managers;

use PDO;
use PDOException;

class ConnexionPDO
{
    private static $instance = null;
    public static PDO $connexion;



    public function __construct()
    {
        try {
            self::$connexion = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) { // On vérifie si $instance contient une instance de nous-même
            self::$instance = new self(); // si non, on s'instancie et on stocke cette instance - retourne self::$instance
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return self::$connexion;
    }
}
