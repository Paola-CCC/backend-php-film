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
        ) AS categories, 
		GROUP_CONCAT(
			JSON_OBJECT(
				'postId', lk_p.postId,
				'userId', lk_p.userId
            )
        ) AS likesGroup,
		GROUP_CONCAT(
			JSON_OBJECT(
				'postId', dlk_p.postId,
				'userId', dlk_p.userId
            )
        ) AS dislikesGroup
				FROM posts p
				LEFT JOIN users u ON p.userId = u.id
				LEFT JOIN posts_categories pc ON p.id = pc.postId
				LEFT JOIN categories cat ON pc.categoryId = cat.id
				LEFT JOIN likesPosts lk_p ON p.id = lk_p.postId
				LEFT JOIN dislikesPosts dlk_p ON p.id = dlk_p.postId
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
        ) AS categories,
		GROUP_CONCAT(
			JSON_OBJECT(
				'postId', lk_p.postId,
				'userId', lk_p.userId
            )
        ) AS likesGroup,
		GROUP_CONCAT(
			JSON_OBJECT(
				'postId', dlk_p.postId,
				'userId', dlk_p.userId
            )
        ) AS dislikesGroup
			FROM posts p
			LEFT JOIN users u ON p.userId = u.id
			LEFT JOIN posts_categories pc ON p.id = pc.postId
			LEFT JOIN categories cat ON pc.categoryId = cat.id
			LEFT JOIN likesPosts lk_p ON p.id = lk_p.postId
			LEFT JOIN dislikesPosts dlk_p ON p.id = dlk_p.postId
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
		$query = "DELETE FROM posts WHERE id = :id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->bindParam(":id", $id);
		$stmt->execute();
	}
}


