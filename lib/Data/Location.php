<?php

declare(strict_types=1);

namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Location
{
    public string $id;
    public string $name;
    public string $climate;
    public string $terrain;
    public string $surface_water;
    /** @var array<string> */
    public array $residents;
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
