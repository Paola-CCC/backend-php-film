<?php

namespace App\Managers;

use App\Managers\ConnexionPDO;
use PDO;
use PDOException;

class PostManager
{

	public $_table;
	protected $_connexionBD;

	public function __construct($table)
	{
		$this->_table = $table;
		$instanceBD = ConnexionPDO::getInstance();
		$this->_connexionBD = $instanceBD->getConnection();
	}

	//OK
	// public function findAllPost() {
	// 	$query = "SELECT p.id, p.title, p.content, p.createdAt, u.username
	// 		FROM posts p
	// 		LEFT JOIN users u 
	// 		ON p.userId = u.id";
	// 	$stmt = $this->_connexionBD->prepare($query);
	// 	$stmt->execute();
	// 	$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	// 	return $row;
	// }

	/** renvoie des Posts associés avec des commentaires */
	public function findAll()
	{
		$query = "SELECT p.id, p.title, p.content,p.thumbnail, p.createdAt, u.picture_avatar, u.username as author, 
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', cat.id,
				'name', cat.name,
				'slug', cat.slug
            )
        ) AS categories
				FROM posts p
				LEFT JOIN users u ON p.userId = u.id
				LEFT JOIN posts_categories pc ON p.id = pc.postId
				LEFT JOIN categories cat ON pc.categoryId = cat.id
				GROUP BY p.id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row ;
	}


	//OK
	public function insertPost(array $objet)
	{
		try {

			$query = "INSERT INTO $this->_table (title, content,createdAt, userId , thumbnail) VALUES (:title, :content, current_timestamp(), :userId , :thumbnail)";
			$stmt = $this->_connexionBD->prepare($query);
			if ($stmt->execute($objet)) {
				return "success";
			} else {
				return "failed";
			}
		} catch (PDOException $exception) {

			return $exception->getMessage();
		}
	}

	public function update($object)
	{
		try {

			$query = "UPDATE $this->_table 
				SET title = :title, content = :content ,createdAt = current_timestamp(), userId = :userId 
				WHERE id = :id";
			$stmt = $this->_connexionBD->prepare($query);
			if ($stmt->execute($object)) {
				return "success";
			} else {
				return "failed";
			}
		} catch (PDOException $exception) {

			return $exception->getMessage();
		}
	}


	//OK
	public function findById(string $id)
	{
		$query = "SELECT p.id, p.title, p.content,p.thumbnail, p.createdAt, u.picture_avatar, u.username as author, GROUP_CONCAT(
			JSON_OBJECT(
				'id', cat.id,
				'name', cat.name,
				'slug', cat.slug
            )
        ) AS categories
			FROM posts p
			LEFT JOIN users u ON p.userId = u.id
			LEFT JOIN posts_categories pc ON p.id = pc.postId
			LEFT JOIN categories cat ON pc.categoryId = cat.id
			WHERE p.id = :id
			GROUP BY p.id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}


	//OK
	public function deletePost(string $id)
	{
		// Suppression de l'utilisateur de la base de données
		$query = "DELETE FROM posts WHERE id = :id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->bindParam(":id", $id);
		$stmt->execute();
	}
}


