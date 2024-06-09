<?php 

declare(strict_types=1);

namespace App\Service;

class FormatDateHelper 
{

    public function getFrenchDate(string $dateToChange) 
    {
        $date = date_create($dateToChange);
        $changeDate = date_format($date,"d/m/Y");
        return $changeDate;
    }
}