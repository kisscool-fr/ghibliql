<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GhibliQL\Data\Film;

final class FilmTest extends TestCase
{
    public static function filmData()
    {
        return [
            [
                [
                    'id' => '116484',
                    'title' => 'Chloe',
                    'description' => 'Une femme pensant que son mari est infidèle décide d\'embaucher une escort girl afin de prendre son époux en flagrant délit d\'adultère...',
                    'director' => 'Atom Egoyan',
                    'producer' => 'StudioCanal',
                    'release_date' => '2009',
                    'rt_score' => '',
                    'image' => '',
                    'movie_banner' => '',
                    'people' =>  [''],
                    'species' => [''],
                    'locations' => [''],
                    'vehicles' => [''],
                    'url' => 'http://www.allocine.fr/film/fichefilm_gen_cfilm=116484.html',
                ]
            ]
        ];
    }

    /**
     * @dataProvider filmData
     */
    public function testFilm(array $data)
    {
        $film = new Film($data);

        foreach ($data as $propertie => $value) {
            $this->assertEquals(
                $film->{$propertie},
                $value,
                sprintf('`%s` don\'t match', $propertie)
            );
        }
    }
}
