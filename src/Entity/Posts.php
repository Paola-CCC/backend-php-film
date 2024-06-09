<?php

declare(strict_types=1);

namespace App\Entity;

class Post 
{
    private $id;
    private $title;
    private $content;
    private $createdAt;
    private $user;

    //GETTER
    public function getId() 
    {
        return $this->id;
    }

    public function getTitle() 
    {
        return $this->title;
    }

    public function getContent() 
    {
        return $this->content;
    }

    public function getCreatedAt() 
    {
        return $this->createdAt;
    }

    public function getUser() {
        return $this->user;
    }



    // SETTER
    public function setId($id) 
    {
        $this->id = $id;
        return $this->id;
    }

    public function setTitle($title) 
    {
        $this->title = $title;
        return $this->title;
    }

    public function setContent($content) 
    {
        $this->content = $content;
        return $this->content;
    }

    public function setCreatedAt($dateCreatedAt)
    {
        $this->createdAt = $dateCreatedAt;
        return $this->createdAt;
    }

    public function setUser($user) 
    {
        $this->user = $user;
        return $this->user;
    }
}