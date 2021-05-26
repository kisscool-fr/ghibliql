<?php
namespace GhibliQL\Data;

use GraphQL\Utils\Utils;

class Film
{
    public $id;
    public $title;
    public $original_title;
    public $original_title_romanised;
    public $description;
    public $director;
    public $producer;
    public $release_date;
    public $running_time;
    public $rt_score;
    public $people;
    public $species;
    public $locations;
    public $vehicles;
    public $url;

    public function __construct(array $data)
    {
        Utils::assign($this, $data, ['id']);
    }
}
