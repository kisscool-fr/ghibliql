<?php

namespace GhibliQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GhibliQL\Types;
use GhibliQL\Data\DataSource;

class SpecieType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Specie',
            'description' => 'The Species endpoint returns information about all of the Studio Ghibli species. This includes humans, animals, and spirits et al.',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Unique identifier representing a specific species'
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => 'Name of the species'
                    ],
                    'classification' => [
                        'type' => Type::string(),
                        'description' => 'Classification of the species'
                    ],
                    'eye_colors' => [
                        'type' => Type::string(),
                        'description' => 'Eye color of the species'
                    ],
                    'hair_colors' => [
                        'type' => Type::string(),
                        'description' => 'Hair color of the species'
                    ],
                    'people' => [
                        'type' => Type::listOf(Types::people()),
                        'description' => 'People belonging to the species'
                    ],
                    'films' => [
                        'type' => Type::listOf(Types::film()),
                        'description' => 'Array of films the species appears in'
                    ],
                    'url' => [
                        'type' => Type::string(),
                        'description' => 'Unique url of the species'
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

    public function films($value, $args, $context, ResolveInfo $info)
    {
        $films = [];

        if (property_exists($value, $info->fieldName)) {
            foreach ($value->{$info->fieldName} as $filmUrl) {
                $filmId = substr($filmUrl, strrpos($filmUrl, '/')+1);
                $films[$filmId] = DataSource::findFilm($filmId);
            }
        }

        return $films;
    }

    public function people($value, $args, $context, ResolveInfo $info)
    {
        $peoples = [];

        if (property_exists($value, $info->fieldName)) {
            foreach ($value->{$info->fieldName} as $peopleUrl) {
                $peopleId = substr($peopleUrl, strrpos($peopleUrl, '/')+1);
                $peoples[$peopleId] = DataSource::findPeople($peopleId);
            }
        }

        return $peoples;
    }
}
