<?php

namespace App\Managers;

use App\Managers\ConnexionPDO;
use DateTimeImmutable;
use PDO;


 class UserManager {

    public $_table;
	protected $_connexionBD;

	public function __construct($table)
	{
		$this->_table = $table;
		$instanceBD = ConnexionPDO::getInstance();
		$this->_connexionBD = $instanceBD->getConnection();
	}

    //OK
    public function findById(string $id ) 
    {

        $query = "SELECT u.id, u.username, u.email, r.name as role_name , u.picture_avatar as profilePicture
        FROM users u
        LEFT JOIN role r ON u.role_id = r.id
        WHERE u.id = :id";
        $stmt = $this->_connexionBD->prepare($query);
        $stmt->bindParam(":id", $id , PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ; 
        
    }

    //OK
    public function findByEmail(string $email) 
    {

        $query = "SELECT u.id, u.username, u.email, u.password, u.createdAt, r.name as role_name
        FROM users u
        LEFT JOIN role r ON u.role_id = r.id
        WHERE u.email = :email";
        $stmt = $this->_connexionBD->prepare($query);
        $stmt->bindParam(":email", $email , PDO::PARAM_STR);
        $stmt->execute();
        return $stmt ;   
    }


    //OK
    public function findAll()
    {

        $query = "SELECT u.id, u.username, u.email, u.createdAt, r.name as role_name
        FROM users u
        LEFT JOIN role r 
        ON u.role_id = r.id";
        $stmt = $this->_connexionBD->prepare($query);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row ;
    }

    public function findAllUserDetails()
    {
        $query = "SELECT u.id, u.username, p.id AS post_id, p.title, p.content, c.id AS comment_id, c.comment_text
        FROM users u
        LEFT JOIN post p ON p.userId = u.id
        LEFT JOIN comment c ON c.postId = p.id
        ORDER BY u.id, p.id, c.id";

            $stmt = $this->_connexionBD->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = array();
            $current_user = null;
            $current_post = null;

            foreach ($results as $result) {

                if ($result['id'] != $current_user) {
                    $current_user = $result['id'];
                    $users[$current_user] = array(
                        'id' => $current_user,
                        'username' => $result['username'],
                        'posts' => array()
                    );
                    $current_post = null;
                }
                if ($result['post_id'] != $current_post) {
                    $current_post = $result['post_id'];
                    $users[$current_user]['posts'][$current_post] = array(
                        'id' => $current_post,
                        'title' => $result['title'],
                        'content' => $result['content'],
                        'comments' => array()
                    );
                }
                if ($result['comment_id']) {
                    $users[$current_user]['posts'][$current_post]['comments'][] = array(
                        'id' => $result['comment_id'],
                        'text' => $result['comment_text']
                    );
                }
            }

            return array_values($users); ;
    }

    //OK
    public function insertUser( $data) 
    {

        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        // Récupération de la date et heure actuelles
        $now = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        // Formatage de la date en format SQL
        $createdAt = $now->format('Y-m-d H:i:s');
        // Creating timestamp from given date
        $timestamp = strtotime($createdAt);
        // Creating new date format from that timestamp
        $new_date = date("d-m-Y", $timestamp);

        $query = "INSERT INTO users (username, email, password, createdAt, role_id) VALUES (:username, :email, :password, :createdAt, :role_id)";
        $stmt = $this->_connexionBD->prepare($query);
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":createdAt",$createdAt);
        $stmt->bindParam(":role_id", $data['role']);
        if($stmt->execute()){ 
            return "success";
        } else {
            return "failed";
        }
    }

    //OK
    public function update(array $data ) 
    {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $now = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        // Formatage de la date en format SQL
        $createdAt = $now->format('Y-m-d H:i:s');

        $query = "UPDATE users SET username = :username, email = :email, password = :password, createdAt = :createdAt, role_id = :role_id WHERE id = :id";
        $stmt = $this->_connexionBD->prepare($query);
        $stmt->bindParam(":id", $data['id']);
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":createdAt",$createdAt );
        $stmt->bindParam(":role_id", $data['role']);

        return $stmt->execute();
    }

    //OK
    public function delete(string $id) 
    {
        // Suppression de l'utilisateur de la base de données
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->_connexionBD->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }
 }