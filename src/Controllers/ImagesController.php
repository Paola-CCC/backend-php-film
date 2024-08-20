<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\ImagesManager;

class ImagesController 
{

    private $imagesManager;

    public function __construct()
    {
        $this->imagesManager = new ImagesManager();
    }

    /** Insérer Image dans base de donnée et stockage */
    public function new()
    {


        if ( $_SERVER['REQUEST_METHOD'] === 'POST'&& isset($_FILES['thumbnail']) && is_uploaded_file($_FILES['thumbnail']['tmp_name'])) {

            $origine = $_FILES['thumbnail']['tmp_name'];
            $fileName = $_FILES['thumbnail']['name'];
            $size = $_FILES['thumbnail']['size'];

            $tabExtension = explode('.', $fileName);
            $extension = strtolower(end($tabExtension));
            $maxSize = 400000;

            $extensions = ['jpg', 'png', 'jpeg', 'gif'];

            if (in_array($extension, $extensions) && $size <= $maxSize){

                $uniqueName = uniqid('', true);
                //uniqid génère quelque chose comme ca : 5f586bf96dcd38.73540086
                $file = $uniqueName.".".$extension;
                //$file = 5f586bf96dcd38.73540086.jpg

                $location = 'upload/' . $file;
                move_uploaded_file($origine, $location);
            }
            else {
                echo "Mauvaise extension ou taille trop grande";
            }

          
            

            return json_encode($this->imagesManager->insertImage([
                "name" => $fileName,
                "path" => $location
            ]));

        
        } else {
            return json_encode([
                "message" => "Error! Failed to insert to database"            
            ]);
        }

    }

    public function imagesDataWithPath(array $imagesList)  
    {
        $correct = [];
    
        foreach ($imagesList as $objts) {
            $temp = []; 
    
            foreach ($objts as $keys => $value) {
                if ($keys === 'path') {
                    $temp[$keys] = $this->getFullURL() . '/' . $value;
                } else {
                    $temp[$keys] = $value;
                }
            }
            $correct[] = $temp;
        }

        return $correct;
    }

    public function getFullURL() 
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $fullUrl = $protocol . $host ;
        return $fullUrl;
    }


    public function all()
    {
        $results = $this->imagesManager->getAll();
        return json_encode($this->imagesDataWithPath($results));
    }

    public function show(string $id)
    {

        $results = $this->imagesManager->getById(((int) $id));
        return json_encode($this->imagesDataWithPath($results));

    }

}