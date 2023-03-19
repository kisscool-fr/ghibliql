<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use GhibliQL\Types;
use GhibliQL\Type\FilmType;
use GhibliQL\Type\PeopleType;
use GhibliQL\Type\SpecieType;
use GhibliQL\Type\LocationType;
use GhibliQL\Type\VehicleType;
use GhibliQL\Type\QueryType;

final class TypesTest extends TestCase
{
    public function testFilm(): void
    {
        $this->assertInstanceOf(FilmType::class, Types::film());
    }

    public function testPeople(): void
    {
        $this->assertInstanceOf(PeopleType::class, Types::people());
    }

    public function testSpecie(): void
    {
        $this->assertInstanceOf(SpecieType::class, Types::specie());
    }

    public function testLocation(): void
    {
        $this->assertInstanceOf(LocationType::class, Types::location());
    }

    public function testVehicle(): void
    {
        $this->assertInstanceOf(VehicleType::class, Types::vehicle());
    }

    public function testQuery(): void
    {
        $this->assertInstanceOf(QueryType::class, Types::query());
    }
}
