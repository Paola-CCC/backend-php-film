<?php


namespace App\Managers;

use App\Managers\ConnexionPDO;
use DateTimeImmutable;
use PDO;
use PDOException;

class ImagesManager
{

    public $_table = 'images';
	protected $_connexionBD;

    public function __construct()
	{
		$instanceBD = ConnexionPDO::getInstance();
		$this->_connexionBD = $instanceBD->getConnection();
	}

	public function insertImage(array $data)
	{

		try {
            $query = "INSERT INTO $this->_table (name, path, createdAt)  VALUES (:name, :path, current_timestamp())";
			$stmt = $this->_connexionBD->prepare($query);
			$stmt->execute([
				':name' => $data["name"],
				':path' => $data["path"]			
            ]);

			if ($stmt) {
				return [
					"message" => "Image added successfully",
					"imageId" => (int) $this->_connexionBD->lastInsertId()
				];
			}

		} catch (PDOException $e) {
    		return "Error: " . $e->getMessage();
		}
	}

    public function getAll()
	{

		try {
            $query = "SELECT * FROM images";
			$stmt = $this->_connexionBD->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
		} catch (PDOException $e) {
    		return "Error GetAll: " . $e->getMessage();
		}
	}


    public function getById(int $id)
	{

		try {
            $query = "SELECT * FROM images i WHERE i.id = :id";
			$stmt = $this->_connexionBD->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
		} catch (PDOException $e) {
    		return "Error GetID: " . $e->getMessage();
		}
	}

	public function delete(int $id)
	{

		try {
            $query = "DELETE FROM $this->_table WHERE id = :id";
			$stmt = $this->_connexionBD->prepare($query);
            $result = $stmt->execute([
				"id" => $id
			]);

			if($result){
				return "delete with sucess";
			}
            
		} catch (PDOException $e) {
    		return "Error GetID: " . $e->getMessage();
		}
	}

}