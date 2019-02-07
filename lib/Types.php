<?php

namespace GhibliQL;

use GhibliQL\Type\FilmType;
use GhibliQL\Type\PeopleType;
use GhibliQL\Type\SpecieType;
use GhibliQL\Type\LocationType;
use GhibliQL\Type\VehicleType;
use GhibliQL\Type\QueryType;

class Types
{
    private static $query;
    private static $film;
    private static $people;
    private static $specie;
    private static $location;
    private static $vehicle;

    /**
     * @return FilmType
     */
    public static function film()
    {
        return self::$film ?: (self::$film = new FilmType());
    }

    /**
     * @return PeopleType
     */
    public static function people()
    {
        return self::$people ?: (self::$people = new PeopleType());
    }

    /**
     * @return SpecieType
     */
    public static function specie()
    {
        return self::$specie ?: (self::$specie = new SpecieType());
    }

    /**
     * @return LocationType
     */
    public static function location()
    {
        return self::$location ?: (self::$location = new LocationType());
    }

    /**
     * @return VehicleType
     */
    public static function vehicle()
    {
        return self::$vehicle ?: (self::$vehicle = new VehicleType());
    }

    /**
     * @return QueryType
     */
    public static function query()
    {
        return self::$query ?: (self::$query = new QueryType());
    }
}
