<?php

namespace GhibliQL\Type;

use GhibliQL\AppContext;
use GhibliQL\Data\DataSource;
use GhibliQL\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'film' => [
                    'type' => Types::film(),
                    'description' => 'Returns a film based on a single ID',
                    'args' => [
                        'id' => Type::nonNull(Type::id())
                    ]
                ],
                'films' => [
                    'type' => Type::listOf(Types::film()),
                    'description' => 'Returns information about all of the Studio Ghibli films',
                ],
                'people' => [
                    'type' => Types::people(),
                    'description' => 'Returns a person based on a single ID',
                    'args' => [
                        'id' => Type::nonNull(Type::id())
                    ]
                ],
                'peoples' => [
                    'type' => Type::listOf(Types::people()),
                    'description' => 'Returns all available peoples',
                ],
                'specie' => [
                    'type' => Types::specie(),
                    'description' => 'Returns an individual species',
                    'args' => [
                        'id' => Type::nonNull(Type::id())
                    ]
                ],
                'species' => [
                    'type' => Type::listOf(Types::specie()),
                    'description' => 'Returns all available species',
                ],
                'location' => [
                    'type' => Types::location(),
                    'description' => 'Returns an individual location.',
                    'args' => [
                        'id' => Type::nonNull(Type::id())
                    ]
                ],
                'locations' => [
                    'type' => Type::listOf(Types::location()),
                    'description' => 'Returns information about all of the Studio Ghibli locations',
                ],
                'vehicle' => [
                    'type' => Types::vehicle(),
                    'description' => 'An individual vehicle',
                    'args' => [
                        'id' => Type::nonNull(Type::id())
                    ]
                ],
                'vehicles' => [
                    'type' => Type::listOf(Types::vehicle()),
                    'description' => 'Returns all available vehicles',
                ]
            ],
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }

    public function film(mixed $rootValue, array $args): mixed
    {
        return DataSource::findFilm($args['id']);
    }

    public function films(mixed $rootValue, array $args): array
    {
        return DataSource::getFilms();
    }

    public function people(mixed $rootValue, array $args): mixed
    {
        return DataSource::findPeople($args['id']);
    }

    public function peoples(mixed $rootValue, array $args): array
    {
        return DataSource::getPeoples();
    }

    public function specie(mixed $rootValue, array $args): mixed
    {
        return DataSource::findSpecie($args['id']);
    }

    public function species(mixed $rootValue, array $args): array
    {
        return DataSource::getSpecies();
    }

    public function location(mixed $rootValue, array $args): mixed
    {
        return DataSource::findLocation($args['id']);
    }

    public function locations(mixed $rootValue, array $args): array
    {
        return DataSource::getLocations();
    }

    public function vehicle(mixed $rootValue, array $args): mixed
    {
        return DataSource::findVehicle($args['id']);
    }

    public function vehicles(mixed $rootValue, array $args): array
    {
        return DataSource::getVehicles();
    }

    public function deprecatedField(): string
    {
        return 'You can request deprecated field, but it is not displayed in auto-generated documentation by default.';
    }
}
