<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\CommentsManager;
use App\Managers\DislikesPostsManager;
use App\Managers\LikesPostsManager;
use App\Managers\PostManager;
use App\Service\FormatDateHelper;
use DateTimeImmutable;
use PDOException;

class PostController 
{
    private $postManager;
    private $date;

    public function __construct()
    {
        $this->postManager = new PostManager('posts');
        $this->date = new FormatDateHelper();
    }

    /** renvoie les posts avec les likes, dislikes et les commentaires */
    public function getPostsWithComments(array $listPosts)
    {

        $commentManager = new CommentsManager('comments');
        $likes = new LikesPostsManager();
        $dislikes = new DislikesPostsManager();

        $allPosts = [];
        $likesCounter = 0;
        $dislikesCounter = 0;

        foreach($listPosts as $values ) {
            $StepOne = [];
            $commentsList = [];
            $categories = [];
            $comments = $commentManager->findAllByPostId($values["id"]);
            $likesPosts = $likes->findAllByPostId((int) $values["id"]);
            $dislikesPosts = $dislikes->findAllByPostId((int) $values["id"]);
            $decodeCategories = json_decode('[' . $values['categories'] . ']', true);
            $categories = $decodeCategories[0]["id"] !== null ? $decodeCategories : [];

            if( count($comments) > 0){

                foreach( $comments as $comment){

                    $dateFormated = $this->date->getFrenchDate($comment["createdAt"]);

                    $filteredArr = array_filter($comment, function($key) {
                        return $key !== 'createdAt';
                    }, ARRAY_FILTER_USE_KEY);
                    
                    $filteredArr ["createdAt"] = $dateFormated;
                    $StepOne[] = $filteredArr;
                }

                $commentsList = [...$StepOne];
            }

            if( count($likesPosts) > 0){
                $likesCounter += count($likesPosts);
            }

            if( count($likesPosts) > 0){
                $dislikesCounter += count($dislikesPosts);
            }            

            $allPosts[] = [
                "id" => $values["id"],
                "title" => $values["title"],
                "content" => $values["content"],
                "createdAt" => $this->date->getFrenchDate($values["createdAt"]),
                "author" => $values["author"],
                "thumbnail" => $values["thumbnail"],
                "picture_author_post" => $values["picture_avatar"],
                "comments" => $commentsList,
                "likes" => $likesCounter,
                "dislikes" => $dislikesCounter,
                "categories" => $categories

            ];

        }

        return $allPosts;
    }

    public function update()
    {

        $now = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $createdAt = $now->format('Y-m-d H:i:s');

        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ;
        $content = $data['content'];
        $userId = $data['userId'] ;
        $postId = $data['postId'] ;

        $tabDatas = [
            "title" => $title,
            "content" =>  $content,
            "createdAt" => $createdAt,
            "userId" => $userId,
            "id" => $postId
        ];
        http_response_code(200);
        return json_encode($this->postManager->update($tabDatas));
    }

    public function show(string $id)
    {
  
        $results = $this->postManager->findById($id);
        return json_encode($this->getPostsWithComments($results));
    }

    public function new()
    {

        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ;
        $content = $data['content'];
        $userId = $data['userId'] ;
        $$thumbnail = $data["$thumbnail"];

        $tabDatas = [
            "title" => $title,
            "content" =>  $content,
            "userId" => $userId,
            "thumbnail" => $thumbnail
        ];

        http_response_code(200);
        return json_encode($this->postManager->insertPost($tabDatas));
    }

    public function all()
    {

        try {

            $results = $this->postManager->findAll();
            return json_encode($this->getPostsWithComments($results));

		} catch (PDOException $exception) {
            return json_encode([
                "status" => http_response_code(404),
                "eroor-message" => $exception->getMessage()
            ]);
		}
    }

    public function remove(string $id)
    {
  
        http_response_code(200);
        return json_encode($this->postManager->deletePost($id));
    }
}