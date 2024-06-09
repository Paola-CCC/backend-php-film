<?php
declare(strict_types=1);

namespace App\Routing;


class Routes
{
    public array $routes = [];
    private const METHOD_POST = 'POST';
    private const METHOD_GET = 'GET';
    private const METHOD_PUT = 'PUT';
    private const METHOD_DELETE = 'DELETE';

    public function __construct() {

        /** User */
        $this->post('/register',['user' => 'register']);
        $this->post('/login',['user' => 'login']);
        
        $this->get('/user-all',['user' => 'all']);
        $this->get('/users-details',['user' => 'allDetails']);
        $this->put('/user-update',['user' => 'update']);
        $this->get('/user-show/:id',['user' => 'show']);
        $this->delete('/user-remove/:id',['user' => 'remove']);

        /** Post */
        $this->post('/post-new',['post' => 'new']);
        $this->get('/posts-all',['post' => 'all']);
        $this->put('/post-update',['post' => 'update']);
        $this->get('/post-show/:id',['post' => 'show']);
        $this->delete('/post-remove/:id',['post' => 'remove']);

        /** Commentaire */
        $this->post('/comment-new',['comment' => 'new']);
        $this->get('/comments-all',['comment' => 'all']);
        $this->put('/comment-update',['comment' => 'update']);
        $this->get('/comment-show/:id',['comment' => 'show']);
        $this->delete('/comment-remove/:id',['comment' => 'remove']);

        /** Likes */
        $this->post('/likes-posts-new',['likesPosts' => 'new']);
        $this->get('/likes-posts-all',['likesPosts' => 'all']);
        $this->get('/likes-posts-show/:id',['likesPosts' => 'show']);
        $this->delete('/likes-posts-remove',['likesPosts' => 'remove']);

        /** DisLikes */
        $this->post('/dislikes-posts-new',['dislikesPosts' => 'new']);
        $this->get('/dislikes-posts-all',['dislikesPosts' => 'all']);
        $this->get('/dislikes-posts-show/:id',['dislikesPosts' => 'show']);
        $this->delete('/dislikes-posts-remove',['dislikesPosts' => 'remove']);

    }
    
    /**
    * @param string $path
    * @param mixed $handler
    */
    function get(string $path, $handler)
    {
        $this->addRoutes(self::METHOD_GET, $path, $handler);
    }

    /**
    * @param string $path
    * @param mixed $handler
    */
    function post(string $path, $handler)
    {
        $this->addRoutes(self::METHOD_POST, $path, $handler);
    }

    /**
    * @param string $path
    * @param mixed $handler
    */
    function put(string $path, $handler)
    {
        $this->addRoutes(self::METHOD_PUT, $path, $handler);
    }

    /**
    * @param string $path
    * @param mixed $handler
    */
    function delete(string $path, $handler)
    {
        $this->addRoutes(self::METHOD_DELETE, $path, $handler);
    }

    /**
    * @param string $method
    * @param string $path
    * @param mixed $handler
    */
    function addRoutes(string $method, string $path, $handler)
    {

        $data = $this->getPattern($path);
        $this->routes[] = [
            'path' => $data["path"],
            'pattern' => $data["regExReplace"],
            'method' => $method,
            'handler' => $handler
        ];
    }

    /**
    * @param string $path
    */
    function getPattern(string $path)
    {    

        $removeFirstSlash = trim($path, '/');
        $putDataInArray = explode('/', $removeFirstSlash);
        $url = "/$putDataInArray[0]";
        $regexReplace = '';
        $tab = [
            'path' => $url,
            'regExReplace' => $regexReplace
        ];

        if( isset($putDataInArray[1]) && !empty($putDataInArray[1])) {
            $regexReplace = str_replace(':id', '(\d+)', $putDataInArray[1]);
            $tab = [
                'path' => $url,
                'regExReplace' => $regexReplace
            ];
            return $tab ;
        } else { return $tab ;}

    }

    public function getRoutes() : array
    {
        return $this->routes;
    }
}
