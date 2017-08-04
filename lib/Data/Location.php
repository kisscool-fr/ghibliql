<?php
namespace GhibliQL\Data;

use GraphQL\Utils;

class Location
{
    public $id;
    public $name;
    public $climate;
    public $terrain;
    public $surface_water;
    public $residents;
    public $films;
    public $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
