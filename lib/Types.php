<?php

declare(strict_types=1);

namespace GhibliQL;

use GhibliQL\Type\FilmType;
use GhibliQL\Type\PeopleType;
use GhibliQL\Type\SpecieType;
use GhibliQL\Type\LocationType;
use GhibliQL\Type\VehicleType;
use GhibliQL\Type\QueryType;

class Types
{
    private static ?QueryType $query = null;
    private static ?FilmType $film = null;
    private static ?PeopleType $people = null;
    private static ?SpecieType $specie = null;
    private static ?LocationType $location = null;
    private static ?VehicleType $vehicle = null;

    public static function film(): FilmType
    {
        return self::$film ?: (self::$film = new FilmType());
    }

    public static function people(): PeopleType
    {
        return self::$people ?: (self::$people = new PeopleType());
    }

    public static function specie(): SpecieType
    {
        return self::$specie ?: (self::$specie = new SpecieType());
    }

    public static function location(): LocationType
    {
        return self::$location ?: (self::$location = new LocationType());
    }

    public static function vehicle(): VehicleType
    {
        return self::$vehicle ?: (self::$vehicle = new VehicleType());
    }

    public static function query(): QueryType
    {
        return self::$query ?: (self::$query = new QueryType());
    }
}
