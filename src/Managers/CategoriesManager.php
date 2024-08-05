<?php

namespace App\Managers;

use App\Managers\ConnexionPDO;
use PDO;
use PDOException;

class CategoriesManager
{
	public $_table = 'categories';
	protected $_connexionBD;

	public function __construct()
	{
		$instanceBD = ConnexionPDO::getInstance();
		$this->_connexionBD = $instanceBD->getConnection();
	}
    public function findAll()
    {
        try {
            $query = "SELECT cat_s.id AS value, cat_s.name AS label FROM categories cat_s";
            $stmt = $this->_connexionBD->prepare($query);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row ;
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

}