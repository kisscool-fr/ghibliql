<?php
namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Location
{
    public string $id;
    public string $name;
    public string $climate;
    public string $terrain;
    public string $surface_water;
    public array $residents;
    public array $films;
    public string $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
