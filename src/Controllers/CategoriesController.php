<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\CategoriesManager;

class CategoriesController
{

    public $categoriesManager;

    public function __construct()
    {
        $this->categoriesManager = new CategoriesManager();
    }

    public function all()
    {
        http_response_code(200);
        return json_encode($this->categoriesManager->findAll());
    }
}