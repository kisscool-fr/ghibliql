<?php

declare(strict_types=1);

namespace GhibliQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GhibliQL\AppContext;
use GhibliQL\Types;
use GhibliQL\Data\DataSource;
use GhibliQL\Data\Location;

class LocationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Location',
            'description' => 'The Locations endpoint returns information about all of the Studio Ghibli locations. ' .
            'This broadly includes lands, countries, and places.',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Unique identifier representing a specific location'
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => 'Name of location'
                    ],
                    'climate' => [
                        'type' => Type::string(),
                        'description' => 'Climate of location'
                    ],
                    'terrain' => [
                        'type' => Type::string(),
                        'description' => 'Terrain type of location'
                    ],
                    'surface_water' => [
                        'type' => Type::string(),
                        'description' => 'Percent of location covered in water'
                    ],
                    'residents' => [
                        'type' => Type::listOf(Types::people()),
                        'description' => 'Array of residents in location'
                    ],
                    'films' => [
                        'type' => Type::listOf(Types::film()),
                        'description' => 'Array of films the location appears in'
                    ],
                    'url' => [
                        'type' => Type::string(),
                        'description' => 'Individual URL of the location'
                    ]
                ];
            },
            'interfaces' => [
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                if (!in_array($info->fieldName, ['id', 'name']) && method_exists($this, $info->fieldName)) {
                    return $this->{$info->fieldName}($value, $args, $context, $info);
                } elseif (($value instanceof Location) && property_exists($value, $info->fieldName)) {
                    return $value->{$info->fieldName};
                }
                return null;
            }
        ];

        parent::__construct($config);
    }

    public function residents(Location $value, array $args, AppContext $context, ResolveInfo $info): array
    {
        $residents = [];

        if (property_exists($value, $info->fieldName) && is_array($value->{$info->fieldName})) {
            foreach ($value->{$info->fieldName} as $residentUrl) {
                if (is_string($residentUrl)) {
                    $id = substr($residentUrl, strrpos($residentUrl, '/') + 1);
                    $residents[$id] = DataSource::findPeople($id);
                }
            }
        }

        return $residents;
    }

    public function films(Location $value, array $args, AppContext $context, ResolveInfo $info): array
    {
        $films = [];

        if (property_exists($value, $info->fieldName) && is_array($value->{$info->fieldName})) {
            foreach ($value->{$info->fieldName} as $filmUrl) {
                if (is_string($filmUrl)) {
                    $id = substr($filmUrl, strrpos($filmUrl, '/') + 1);
                    $films[$id] = DataSource::findFilm($id);
                }
            }
        }

        return $films;
    }
}
