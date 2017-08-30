<?php
namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Vehicle
{
    public $id;
    public $name;
    public $description;
    public $vehicle_class;
    public $length;
    public $pilot;
    public $films;
    public $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
