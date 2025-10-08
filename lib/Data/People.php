<?php

declare(strict_types=1);

namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class People
{
    public string $id;
    public string $name;
    public string $gender;
    public string $gander;  // fixing bad typing from REST API ?
    public string $age;
    public string $eye_color;
    public string $hair_color;
    /** @var array<string> */
    public array $films;
    public string $species;
    public string $url;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }
}
