<?php
namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Specie
{
    public string $id;
    public string $name;
    public string $classification;
    public string $eye_colors;
    public string $hair_colors;
    public array $people;
    public array $films;
    public string $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
