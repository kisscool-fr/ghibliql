<?php

declare(strict_types=1);

namespace GhibliQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GhibliQL\AppContext;
use GhibliQL\Types;
use GhibliQL\Data\DataSource;
use GhibliQL\Data\People;
use GhibliQL\Data\Specie;

class PeopleType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'People',
            'description' => 'The People endpoint returns information about all of the Studio Ghibli people. ' .
            'This broadly includes all Ghibli characters, human and non-.',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Unique identifier representing a specific person'
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => 'Name of the person'
                    ],
                    'gender' => [
                        'type' => Type::string(),
                        'description' => 'Gender of the person'
                    ],
                    'age' => [
                        'type' => Type::string(),
                        'description' => 'Age, if known, of the person'
                    ],
                    'eye_color' => [
                        'type' => Type::string(),
                        'description' => 'Eye color of the person'
                    ],
                    'hair_color' => [
                        'type' => Type::string(),
                        'description' => 'Hair color of the person'
                    ],
                    'films' => [
                        'type' => Type::listOf(Types::film()),
                        'description' => 'Array of films the person appears in'
                    ],
                    'species' => [
                        'type' => Types::specie(),
                        'description' => 'Species the person belongs to'
                    ],
                    'url' => [
                        'type' => Type::string(),
                        'description' => 'Unique url of the person'
                    ]
                ];
            },
            'interfaces' => [
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                if (!in_array($info->fieldName, ['id', 'name']) && method_exists($this, $info->fieldName)) {
                    return $this->{$info->fieldName}($value, $args, $context, $info);
                } elseif (($value instanceof People) && property_exists($value, $info->fieldName)) {
                    return $value->{$info->fieldName};
                }
                return null;
            }
        ];

        parent::__construct($config);
    }

    public function films(People $value, array $args, AppContext $context, ResolveInfo $info): array
    {
        $films = [];

        if (property_exists($value, $info->fieldName) && is_array($value->{$info->fieldName})) {
            foreach ($value->{$info->fieldName} as $filmUrl) {
                if (is_string($filmUrl)) {
                    $filmId = substr($filmUrl, strrpos($filmUrl, '/') + 1);
                    $films[$filmId] = DataSource::findFilm($filmId);
                }
            }
        }

        return $films;
    }

    public function species(People $value, array $args, AppContext $context, ResolveInfo $info): mixed
    {
        if (property_exists($value, $info->fieldName) && is_string($value->{$info->fieldName})) {
            $specieUrl = $value->{$info->fieldName};
            $specieId = substr($specieUrl, strrpos($specieUrl, '/') + 1);
            return DataSource::findSpecie($specieId);
        }

        return null;
    }
}
