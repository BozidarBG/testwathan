<?php
/**
 * Created by PhpStorm.
 * User: mitrovic
 * Date: 3.3.23.
 * Time: 15.37
 */

namespace App\Services\Geolocation;


use App\Services\Map\Map;
use App\Services\Satelite\Satelite;

class Geolocation
{
    private $map;
    private $satelite;

    public function __construct(Map $map, Satelite $satelite)
    {
        $this->map=$map;
        $this->satelite=$satelite;
    }

    public function search(string $name){
        $location_info=$this->map->findAddress($name);
        $coordinates=$this->satelite->pinpoint($location_info);

        return $coordinates;
    }

}