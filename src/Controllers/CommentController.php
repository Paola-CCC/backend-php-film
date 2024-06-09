<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\CommentsManager;
use DateTimeImmutable;

class CommentController 
{
    private $commentManager;

    public function __construct()
    {
        $this->commentManager = new CommentsManager('comments');
    }


    public function update()
    {

        $now = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $createdAt = $now->format('Y-m-d H:i:s');

        $data = json_decode(file_get_contents('php://input'), true);
        $content = $data['comment_text'];
        $userId = $data['userId'] ;
        $postId = $data['postId'] ;
        $commentId = $data['commentId'] ;
        
        $tabDatas = [
            "comment_text" =>  $content,
            "createdAt" => $createdAt,
            "postId" => $postId,
            "userId" => $userId,
            "id" => $commentId
        ];

        return json_encode($this->commentManager->update($tabDatas));
    }

    public function show(int $id)
    {

        return json_encode($this->commentManager->findById($id));
    }

    public function new()
    {

        $now = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $createdAt = $now->format('Y-m-d H:i:s');

        $data = json_decode(file_get_contents('php://input'), true);
        $content = $data['content'];
        $userId = $data['userId'] ;
        $postId = $data['postId'] ;
        
        $tabDatas = [
            "content" =>  $content,
            "createdAt" => $createdAt,
            "postId" => $postId,
            "userId" => $userId
        ];
      
        http_response_code(200);
        return json_encode($this->commentManager->insertComment($tabDatas));
    }

    public function all()
    {
        // return json_encode([
        //     "status" => http_response_code(200),
        //     "message" => $this->commentManager->findAllcomment()
        // ]);

        http_response_code(200);
        return json_encode($this->commentManager->findAllcomment());
    }

    public function remove(string $id)
    {
        // return json_encode([
        //     "status" => http_response_code(200),
        //     "message" => $this->commentManager->deletecomment($id)
        // ]);

        http_response_code(200);
        return json_encode($this->commentManager->deletecomment($id));

        
    }
}