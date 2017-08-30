<?php
namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class People
{
    public $id;
    public $name;
    public $gender;
    public $age;
    public $eye_color;
    public $hair_color;
    public $films;
    public $species;
    public $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
