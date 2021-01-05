<?php

namespace GhibliQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GhibliQL\AppContext;
use GhibliQL\Types;
use GhibliQL\Data\DataSource;
use GhibliQL\Data\Vehicle;

class VehicleType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Vehicle',
            'description' => 'The Vehicles endpoint returns information about all of the Studio Ghibli vechiles. This includes cars, ships, and planes.',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Unique identifier representing a specific vehicle'
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => 'Name of the vehicles'
                    ],
                    'description' => [
                        'type' => Type::string(),
                        'description' => 'Description of the vehicle'
                    ],
                    'vehicle_class' => [
                        'type' => Type::string(),
                        'description' => 'Class of the vehicle'
                    ],
                    'length' => [
                        'type' => Type::string(),
                        'description' => 'Length of the vehicle in feet'
                    ],
                    'pilot' => [
                        'type' => Types::people(),
                        'description' => 'Pilot of the vehicle'
                    ],
                    'films' => [
                        'type' => Types::film(),
                        'description' => 'Array of films the vehicle appears in'
                    ],
                    'url' => [
                        'type' => Type::string(),
                        'description' => 'Unique URL of the vehicle'
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

    public function films(Vehicle $value, array $args, AppContext $context, ResolveInfo $info): mixed
    {
        $film = null;

        if (property_exists($value, $info->fieldName)) {
            $filmUrl = $value->{$info->fieldName};
            $filmId = substr($filmUrl, strrpos($filmUrl, '/')+1);
            $film = DataSource::findFilm($filmId);
        }

        return $film;
    }

    public function pilot(Vehicle $value, array $args, AppContext $context, ResolveInfo $info): mixed
    {
        $people = null;

        if (property_exists($value, $info->fieldName)) {
            $peopleUrl = $value->{$info->fieldName};
            $peopleId = substr($peopleUrl, strrpos($peopleUrl, '/')+1);
            $people = DataSource::findPeople($peopleId);
        }

        return $people;
    }
}
