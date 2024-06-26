<?php


namespace App\Managers;

use App\Managers\ConnexionPDO;
use PDO;

class LikesPostsManager 
{
	public string $_table = 'likesPosts';
	protected $_connexionBD;

	public function __construct()
	{
		$instanceBD = ConnexionPDO::getInstance();
		$this->_connexionBD = $instanceBD->getConnection();
	}


	public function findAll()
	{
		$query = "SELECT l.id, l.postId, l.userId , u.username , l.likedAt
				FROM $this->_table l
				LEFT JOIN users u 
                ON l.userId = u.id
				ORDER BY l.id";
		$stmt = $this->_connexionBD->prepare($query);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row ;
	}


    public function findAllByPostId(int $id)
	{
		$query = "SELECT l.id, l.postId, l.userId , u.username , l.likedAt
				FROM $this->_table l
				LEFT JOIN users u 
                ON l.userId = u.id
                WHERE l.postId = :id
				ORDER BY l.id";
		$stmt = $this->_connexionBD->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row ;
	}

	public function insertLike(array $data)
	{


		try {
			$query ="INSERT INTO $this->_table (postId, userId, likedAt) VALUES (:postId, :userId, current_timestamp())";
			$stmt = $this->_connexionBD->prepare($query);
			$stmt->execute([
				':postId' => $data["postId"],
				':userId' => $data["userId"],
			]);

			if ($stmt) {
				return 'Like added successfully.';
			}

		} catch (\Throwable $e) {
    		return "error: " . $e->getMessage();
		}
	}

	public function deleteLike(array $data)
	{

		try {
			$query ="DELETE FROM $this->_table WHERE postId=:postId AND userId=:userId";
			$stmt = $this->_connexionBD->prepare($query);
			$stmt->execute([
				':postId' => $data["postId"],
				':userId' => $data["userId"],
			]);		
			
			if ($stmt) {
				return 'Like deleted successfully.';
			}

		} catch (\Throwable $e) {
    		return "error: " . $e->getMessage();
		}
	}
}