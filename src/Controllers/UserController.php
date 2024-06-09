<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\UserManager;
use App\Service\HttpRequest;
use App\Service\JwtToken;
use DateTimeImmutable;

class UserController 
{

    private $userManager ;
    private $eventRequest ;

    public function __construct()
    {
        $this->userManager = new UserManager('users');
        $this->eventRequest = new HttpRequest();
    }

    public function checkHeader()
    {
        $headers = apache_request_headers();

        if (isset($headers['Authorization']) && !empty($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $authToken = substr($authHeader, 7);
            $providerToken = new JwtToken();
            return $providerToken->verifyToken($authToken);
        }
    }

    public function login ()
    {    
        $data = $this->eventRequest->getBody();
        $email = $data['email'];
        $password = $data['password'] ;
        
        if( !empty($email) && !empty($password)) {

                $rowDB = $this->userManager->findByEmail($email); 
                $userCount = $rowDB->rowCount();

                if ($userCount > 0 ){

                    $rowDBFetched = $rowDB->fetch();

                    if (password_verify($password, $rowDBFetched['password'])) {

                        return json_encode([
                            "user" => $rowDBFetched,
                            'jwt' => $this->addDatasToJWT([
                                'id' => $rowDBFetched['id'],
                                'username' => $rowDBFetched['username'],
                                'email' => $rowDBFetched['email']
                            ]),
                        ]);
                    }

                } else {
                    return json_encode([
                        "code" => http_response_code(404),
                        "message" => 'Aucun utilisateur trouvé'
                    ]);
                } 
                
        } else {
            return json_encode([
                "code" => http_response_code(404),
                "message" => 'Veuillez saisir les informations demandées',
            ]);       
        }
    }

    public function register () 
    {
        $data = $this->eventRequest->getBody();
        $username = $data['username'] ;
        $email = $data['email'];
        $password = $data['password'] ;
     
        $tabDatas = [
            "username" => $username,
            "email" =>  $email,
            "password" => $password,
            "role" => 2
        ];
        

        $rowDB = $this->userManager->findByEmail($email); 

        $userCount = $rowDB->rowCount();
    
        if ($userCount === 0 ){

            if($this->userManager->insertUser($tabDatas) === "success"){

                $rowDBFetched = $this->userManager->findByEmail($email)->fetch(); 

                return json_encode([
                    "user" => $rowDBFetched,
                    "jwt" => $this->addDatasToJWT([
                        'id' => $rowDBFetched['id'],
                        'username' => $rowDBFetched['username'],
                        'email' => $rowDBFetched['email']
                    ]),               
                 ]);

            } else {

                return json_encode([
                    "message" => "Inpossible de s'inscrire"
                ]);
            }

        } else {

            return json_encode([
                "message" => 'Saisiez une autre adresse mail'
            ]);
        } 
    }

    // Mettes des informations dans le JWT __, Enregistrement
    public function addDatasToJWT(array $userElement)
    {
       
        $JWT = new JwtToken();
        $createdJWT = $JWT->createToken($userElement);
        setcookie('Token', $createdJWT, time() + 3600, '/');
        return $createdJWT;
    }

    //Ok
    public function update()
    {

        $now = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        // Formatage de la date en format SQL
        $createdAt = $now->format('Y-m-d H:i:s');
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['userId'];
        $username = $data['username'] ;
        $email = $data['email'];
        $password = $data['password'] ;

        $tabDatas = [
            "id" => $userId,
            "username" => $username,
            "email" =>  $email,
            "password" => $password,
            "role" => '2',
            "createdAt" => $createdAt
        ];
        http_response_code(200);
        return json_encode($this->userManager->update($tabDatas));
    }
    
    //OK
    public function show(string $id)
    {
        http_response_code(200);
        return json_encode($this->userManager->findById($id));
    }

    //OK
    public function all()
    {
        http_response_code(200);
        return json_encode($this->userManager->findAll());
    }

    public function allDetails()
    {
        http_response_code(200);
        return json_encode($this->userManager->findAllUserDetails());
    }

    //OK
    public function remove(string $id)
    {
        http_response_code(200);
        return json_encode($this->userManager->delete($id));
    }
}