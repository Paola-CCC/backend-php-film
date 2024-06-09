<?php

namespace App\Controllers;

use App\Managers\DislikesPostsManager;


class DislikesPostsController 

{
    private $likesPostsManager;

    public function __construct()
    {
        $this->likesPostsManager = new DislikesPostsManager();
    }

    public function new()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int) $data['postId'];
        $userId = (int) $data['userId'];

        return json_encode($this->likesPostsManager->insertDislike([
            "userId" => $userId,
            "postId" => $postId
        ]));
    }

    public function remove()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int) $data['postId'];
        $userId = (int) $data['userId'];

        return json_encode($this->likesPostsManager->deleteDislike([
            "userId" => $userId,
            "postId" => $postId
        ]));
    }
}