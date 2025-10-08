<?php

declare(strict_types=1);

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
