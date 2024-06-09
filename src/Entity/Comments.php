<?php


declare(strict_types=1);

namespace App\Entity;

class Comment {
    private $id;
    private $content;
    private $createdAt;
    private $postId;
    private $userId;

    //GETTER
    public function getId() {
        return $this->id;
    }

    public function getComment_text() {
        return $this->content;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getPostId() {
        return $this->postId;
    }

    public function getUserId() {
        return $this->userId;
    }



    // SETTER
    public function setId($id) {
        $this->id = $id;
        return $this->id;
    }

    public function setComment_text($content) {
        $this->content = $content;
        return $this->content;
    }

    public function setCreatedAt($dateCreatedAt) {

        $this->createdAt = $dateCreatedAt;
        return $this->createdAt;
    }

    public function setPostId($postId) {
        $this->userId = $postId;
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
        return $this->userId;
    }
}