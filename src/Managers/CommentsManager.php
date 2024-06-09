<?php

namespace App\Managers;

use App\Managers\ConnexionPDO;
use PDO;
use PDOException;

class CommentsManager
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
	public function findAllComment()
	{

		$query = "SELECT c.id, c.content, c.createdAt, c.postId, u.username ,u.picture_avatar
			FROM comments c
			LEFT JOIN users u 
			ON c.userId = u.id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}

	//OK
	public function insertComment(array $objet)
	{

		try {

			$query = "INSERT INTO $this->_table (content, createdAt, postId, userId) VALUES (:content, :createdAt, :postId, :userId)";
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

	//OK
	public function update(array $object)
	{
		try {

			$query = "UPDATE $this->_table 
				SET content = :content, createdAt = :createdAt, postId = :postId, userId = :userId 
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
	public function findById(int $id)
	{
		$query = "SELECT c.id, c.content, c.createdAt, c.postId, u.picture_avatar, u.username as comment_author
			FROM comments c
			LEFT JOIN users u 
			ON c.userId = u.id
			WHERE c.id = :id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}

	public function findAllByPostId(int $id)
	{
		$query = "SELECT c.id, c.content, c.createdAt, c.postId, u.picture_avatar, u.username as comment_author
			FROM comments c
			LEFT JOIN users u 
			ON c.userId = u.id
			WHERE c.postId = :id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}

	//OK
	public function deleteComment(string $id)
	{
		// Suppression de l'utilisateur de la base de données
		$query = "DELETE FROM comments WHERE id = :id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->bindParam(":id", $id);
		$stmt->execute();
	}
}
