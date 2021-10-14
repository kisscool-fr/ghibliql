<?php

namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Film
{
    public string $id;
    public string $title;
    public string $original_title;
    public string $original_title_romanised;
    public string $description;
    public string $director;
    public string $producer;
    public string $release_date;
    public string $running_time;
    public string $rt_score;
    public string $movie_banner;
    public string $image;
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
