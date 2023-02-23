<?php

namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Vehicle
{
    public string $id;
    public string $name;
    public string $description;
    public string $vehicle_class;
    public string $length;
    public mixed $pilot;
    public mixed $films;
    public string $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }
}
