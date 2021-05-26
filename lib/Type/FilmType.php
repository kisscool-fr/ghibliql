<?php

namespace GhibliQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GhibliQL\AppContext;
use GhibliQL\Types;
use GhibliQL\Data\DataSource;
use GhibliQL\Data\Film;

class FilmType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Film',
            'description' => 'The Films endpoint returns information about all of the Studio Ghibli films.',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Unique identifier representing a specific film'
                    ],
                    'title' => [
                        'type' => Type::string(),
                        'description' => 'Title of the film'
                    ],
                    'original_title' => [
                        'type' => Type::string(),
                        'description' => 'Original title of the film'
                    ],
                    'original_title_romanised' => [
                        'type' => Type::string(),
                        'description' => 'Orignal title in romanised form'
                    ],
                    'description' => [
                        'type' => Type::string(),
                        'description' => 'Description of the film'
                    ],
                    'director' => [
                        'type' => Type::string(),
                        'description' => 'Director of the film'
                    ],
                    'producer' => [
                        'type' => Type::string(),
                        'description' => 'Producer of the film'
                    ],
                    'release_date' => [
                        'type' => Type::string(),
                        'description' => 'Release year of film'
                    ],
                    'running_time' => [
                        'type' => Type::string(),
                        'description' => 'Running time of the film in minutes'
                    ],
                    'rt_score' => [
                        'type' => Type::string(),
                        'description' => 'Rotten Tomato score of film'
                    ],
                    'people' => [
                        'type' => Type::listOf(Types::people()),
                        'description' => 'People found in film'
                    ],
                    'species' => [
                        'type' => Type::listOf(Types::specie()),
                        'description' => 'Species found in film'
                    ],
                    'locations' => [
                        'type' => Type::listOf(Types::location()),
                        'description' => 'Locations found in film'
                    ],
                    'vehicles' => [
                        'type' => Type::listOf(Types::vehicle()),
                        'description' => 'Vehicles found in film'
                    ],
                    'url' => [
                        'type' => Type::string(),
                        'description' => 'URL of film'
                    ]
                ];
            },
            'interfaces' => [
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                if (($info->fieldName != 'id') && method_exists($this, $info->fieldName)) {
                    return $this->{$info->fieldName}($value, $args, $context, $info);
                } else {
                    return $value->{$info->fieldName};
                }
            }
        ];

        parent::__construct($config);
    }

    public function people(Film $value, array $args, AppContext $context, ResolveInfo $info): array
    {
        $peoples = [];

        if (property_exists($value, $info->fieldName)) {
            foreach ($value->{$info->fieldName} as $url) {
                $id = substr($url, strrpos($url, '/')+1);
                if (empty($id)) {
                    $peoples = DataSource::findPeoplesForFilm($value->id);
                    break;
                } else {
                    $peoples[$id] = DataSource::findPeople($id);
                }
            }
        }

        return $peoples;
    }

    public function species(Film $value, array $args, AppContext $context, ResolveInfo $info): array
    {
        $species = [];

        if (property_exists($value, $info->fieldName)) {
            foreach ($value->{$info->fieldName} as $url) {
                $id = substr($url, strrpos($url, '/')+1);
                $species[$id] = DataSource::findSpecie($id);
            }
        }

        return $species;
    }

    public function locations(Film $value, array $args, AppContext $context, ResolveInfo $info): array
    {
        $locations = [];

        if (property_exists($value, $info->fieldName)) {
            foreach ($value->{$info->fieldName} as $url) {
                $id = substr($url, strrpos($url, '/')+1);
                if (empty($id)) {
                    $locations = DataSource::findLocationsForFilm($value->id);
                    break;
                } else {
                    $locations[$id] = DataSource::findLocation($id);
                }
            }
        }

        return $locations;
    }

    public function vehicles(Film $value, array $args, AppContext $context, ResolveInfo $info): array
    {
        $vehicles = [];

        if (property_exists($value, $info->fieldName)) {
            foreach ($value->{$info->fieldName} as $url) {
                $id = substr($url, strrpos($url, '/')+1);
                if (empty($id)) {
                    $vehicles = DataSource::findVehiclesForFilm($value->id);
                    break;
                } else {
                    $vehicles[$id] = DataSource::findVehicle($id);
                }
            }
        }

        return $vehicles;
    }
}
