<?php 

namespace App\Service;


class FormatDateHelper 
{

    function getFrenchDate(string $dateToChange) 
    {
        $date = date_create($dateToChange);
        $changeDate = date_format($date,"d/m/Y");
        return $changeDate;
    }
}