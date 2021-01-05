<?php
namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Film
{
    public string $id;
    public string $title;
    public string $description;
    public string $director;
    public string $producer;
    public string $release_date;
    public string $rt_score;
    public array $people;
    public array $species;
    public array $locations;
    public array $vehicles;
    public string $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
