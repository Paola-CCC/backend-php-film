<?php

namespace App\Managers;

use App\Managers\ConnexionPDO;
use Exception;
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
		$query = "SELECT p.id, p.title, p.content, imgthumbnail.path AS thumbnail, p.createdAt, i.path AS picture_avatar, u.username as author, 
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', cat.id,
				'name', cat.name,
				'slug', cat.slug
            )
        ) AS categories, 
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', lk_p.id,
				'postId', lk_p.postId,
				'userId', lk_p.userId
            )
        ) AS likesGroup,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', dlk_p.id,
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
				LEFT JOIN images i ON i.id = u.picture_avatar
				LEFT JOIN images imgthumbnail ON imgthumbnail.id = p.thumbnail
				GROUP BY p.id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row ;
	}

	public function insertPostCategories(int $postId, int $categoryId)
    {
        try {

            $query = "INSERT INTO posts_categories ( postId, categoryId) VALUES (:postId, :categoryId)";
            $stmt = $this->_connexionBD->prepare($query);
            $stmt->bindParam(":postId", $postId, PDO::PARAM_INT);
            $stmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
			
            if ($stmt->execute()) {
                return "success";
            } else {
                return "failed";
            }

        } catch (PDOException $exception) {

            return $exception->getMessage();
        }
    }

	//OK
	public function insertPost(array $object)
	{
		try {

			$query = "INSERT INTO $this->_table (title, content,createdAt, userId , thumbnail) VALUES (:title, :content, current_timestamp(), :userId , :thumbnail)";
			$stmt = $this->_connexionBD->prepare($query);
			$stmt->bindParam(":title", $object["title"], PDO::PARAM_STR);
			$stmt->bindParam(":content", $object["content"], PDO::PARAM_STR);
			$stmt->bindParam(":userId", $object["userId"], PDO::PARAM_INT);
			$stmt->bindParam(":thumbnail", $object["thumbnail"], PDO::PARAM_STR);

			if ($stmt->execute()) {
				$lastInsert = $this->_connexionBD->lastInsertId();
				return $this->insertPostCategories($lastInsert, $object["categoryId"]);
			} else {
				return "failed to create Post step one";
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
		$query = "SELECT p.id, p.title, p.content, imgthumbnail.path AS thumbnail, p.createdAt, i.path AS picture_avatar, u.username as author, GROUP_CONCAT(
		JSON_OBJECT(
				'id', cat.id,
				'name', cat.name,
				'slug', cat.slug
            )
        ) AS categories,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', lk_p.id,
				'postId', lk_p.postId,
				'userId', lk_p.userId
            )
        ) AS likesGroup,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', dlk_p.id,
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
			LEFT JOIN images i ON i.id = u.picture_avatar
			LEFT JOIN images imgthumbnail ON imgthumbnail.id = p.thumbnail
			WHERE p.id = :id
			GROUP BY p.id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}


	public function getFrontPagePost()
	{
		$query = "SELECT p.id, p.title, p.content, imgthumbnail.path AS thumbnail, p.createdAt, i.path AS picture_avatar, u.username as author, GROUP_CONCAT(
		JSON_OBJECT(
				'id', cat.id,
				'name', cat.name,
				'slug', cat.slug
            )
        ) AS categories,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', lk_p.id,
				'postId', lk_p.postId,
				'userId', lk_p.userId
            )
        ) AS likesGroup,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', dlk_p.id,
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
			LEFT JOIN images i ON i.id = u.picture_avatar
			LEFT JOIN images imgthumbnail ON imgthumbnail.id = p.thumbnail
			GROUP BY p.id
			ORDER BY p.createdAt DESC 
			LIMIT 1";

		try {
			$stmt = $this->_connexionBD->prepare($query);
			$stmt->execute();
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $row;
		} catch (PDOException $e) {
			return $e->getMessage(); 

		}
	}

	public function getThreeLatestPost()
	{
		$query = "SELECT p.id, p.title, p.content, imgthumbnail.path AS thumbnail, p.createdAt, i.path AS picture_avatar, u.username as author, GROUP_CONCAT(
		JSON_OBJECT(
				'id', cat.id,
				'name', cat.name,
				'slug', cat.slug
            )
        ) AS categories,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', lk_p.id,
				'postId', lk_p.postId,
				'userId', lk_p.userId
            )
        ) AS likesGroup,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', dlk_p.id,
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
			LEFT JOIN images i ON i.id = u.picture_avatar
			LEFT JOIN images imgthumbnail ON imgthumbnail.id = p.thumbnail
			GROUP BY p.id
			ORDER BY p.createdAt DESC
			LIMIT 3 OFFSET 1";

		try {
			$stmt = $this->_connexionBD->prepare($query);
			$stmt->execute();
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $row;
		} catch (PDOException $e) {
			return $e->getMessage(); 
		}
	}

	public function getEightFirstPost()
	{
		$query = "SELECT p.id, p.title, p.content, imgthumbnail.path AS thumbnail, p.createdAt, i.path AS picture_avatar , u.username as author, GROUP_CONCAT(
		JSON_OBJECT(
				'id', cat.id,
				'name', cat.name,
				'slug', cat.slug
            )
        ) AS categories,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', lk_p.id,
				'postId', lk_p.postId,
				'userId', lk_p.userId
            )
        ) AS likesGroup,
		GROUP_CONCAT(
			JSON_OBJECT(
				'id', dlk_p.id,
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
			LEFT JOIN images i ON i.id = u.picture_avatar
			LEFT JOIN images imgthumbnail ON imgthumbnail.id = p.thumbnail
			GROUP BY p.id
			ORDER BY p.createdAt ASC
			LIMIT 8";

		try {
			$stmt = $this->_connexionBD->prepare($query);
			$stmt->execute();
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $row;
		} catch (PDOException $e) {
			return $e->getMessage(); 
		}
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


