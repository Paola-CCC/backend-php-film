<?php

namespace App\Controllers;

use App\Managers\LikesPostsManager;


class LikesPostsController 

{
    private $likesPostsManager;

    public function __construct()
    {
        $this->likesPostsManager = new LikesPostsManager();
    }

    public function new()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int) $data['postId'];
        $userId = (int) $data['userId'];

        return json_encode($this->likesPostsManager->insertLike([
            "userId" => $userId,
            "postId" => $postId
        ]));
    }

    public function remove()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int) $data['postId'];
        $userId = (int) $data['userId'];

        return json_encode($this->likesPostsManager->deleteLike([
            "userId" => $userId,
            "postId" => $postId
        ]));
    }
}