<?php

declare(strict_types=1);

namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Specie
{
    public string $id;
    public string $name;
    public string $classification;
    public string $eye_colors;
    public string $hair_colors;
    /** @var array<string> */
    public array $people;
    /** @var array<string> */
    public array $films;
    public string $url;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }
}
