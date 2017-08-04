<?php
namespace GhibliQL\Data;

use GraphQL\Utils;

class Specie
{
    public $id;
    public $name;
    public $classification;
    public $eye_colors;
    public $hair_colors;
    public $people;
    public $films;
    public $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
