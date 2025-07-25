<?php

/**
 * Class DataSource
 *
 * Data retrieval class
 */

namespace GhibliQL\Data;

use Doctrine\Common\Cache\PredisCache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DataSource
{
    private static ?Client $client = null;
    private static ?PredisCache $cache = null;

    private static ?array $films = null;
    private static ?array $peoples = null;
    private static ?array $species = null;
    private static ?array $locations = null;
    private static ?array $vehicles = null;

    /**
     * @todo Smart init
     */
    public static function init(): void
    {
        if (getenv('REDIS_URL')) {
            try {
                self::$cache = new PredisCache(
                    new \Predis\Client(getenv('REDIS_URL'))
                );
            } catch (\Predis\PredisException $e) {
            }
        }
    }

    public static function findFilm(string $id): mixed
    {
        if (is_null(self::$films)) {
            self::getFilms();
        }

        return isset(self::$films[$id]) ? self::$films[$id] : null;
    }

    public static function findPeople(string $id): mixed
    {
        if (is_null(self::$peoples)) {
            self::getPeoples();
        }

        return isset(self::$peoples[$id]) ? self::$peoples[$id] : null;
    }

    public static function findPeoplesForFilm(string $film_id): array
    {
        if (is_null(self::$peoples)) {
            self::getPeoples();
        }

        $peoples = [];

        foreach (self::$peoples as $id => $data) { // @phpstan-ignore-line
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (
                count(array_filter($data->films, function ($url) use ($film_id) {
                    $id = substr($url, strrpos($url, '/') + 1);
                    return ($id == $film_id);
                })) > 0
            ) {
                $peoples[$id] = $data;
            }
        }

        return $peoples;
    }

    public static function findSpecie(string $id): mixed
    {
        if (is_null(self::$species)) {
            self::getSpecies();
        }

        return isset(self::$species[$id]) ? self::$species[$id] : null;
    }

    public static function findLocation(string $id): mixed
    {
        if (is_null(self::$locations)) {
            self::getLocations();
        }

        return isset(self::$locations[$id]) ? self::$locations[$id] : null;
    }

    public static function findLocationsForFilm(string $film_id): array
    {
        if (is_null(self::$locations)) {
            self::getLocations();
        }

        $locations = [];

        foreach (self::$locations as $id => $data) { // @phpstan-ignore-line
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (
                count(array_filter($data->films, function ($url) use ($film_id) {
                    $id = substr($url, strrpos($url, '/') + 1);
                    return ($id == $film_id);
                })) > 0
            ) {
                $locations[$id] = $data;
            }
        }

        return $locations;
    }

    public static function findVehicle(string $id): mixed
    {
        if (is_null(self::$vehicles)) {
            self::getVehicles();
        }

        return isset(self::$vehicles[$id]) ? self::$vehicles[$id] : null;
    }

    public static function findVehiclesForFilm(string $film_id): array
    {
        if (is_null(self::$vehicles)) {
            self::getVehicles();
        }

        $vehicles = [];

        foreach (self::$vehicles as $id => $data) { // @phpstan-ignore-line
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (
                count(array_filter($data->films, function ($url) use ($film_id) {
                    $id = substr($url, strrpos($url, '/') + 1);
                    return ($id == $film_id);
                })) > 0
            ) {
                $vehicles[$id] = $data;
            }
        }

        return $vehicles;
    }

    public static function getFilms(): array
    {
        if (is_null(self::$films) || count(self::$films) == 0) {
            self::$films = [];

            $films = self::api('/films');

            foreach ($films as $film) {
                $datas = array_combine(array_keys($film), array_values($film));
                self::$films[$film['id']] = new Film( // @phpstan-ignore-next-line
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$films ?? [];
    }

    public static function getPeoples(): array
    {
        if (is_null(self::$peoples) || count(self::$peoples) == 0) {
            self::$peoples = [];

            $peoples = self::api('/people');

            foreach ($peoples as $people) {
                $datas = array_combine(array_keys($people), array_values($people));
                self::$peoples[$people['id']] = new People( // @phpstan-ignore-next-line
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$peoples ?? [];
    }

    public static function getSpecies(): array
    {
        if (is_null(self::$species) || count(self::$species) == 0) {
            self::$species = [];

            $species = self::api('/species');

            foreach ($species as $specie) {
                $datas = array_combine(array_keys($specie), array_values($specie));
                self::$species[$specie['id']] = new Specie( // @phpstan-ignore-next-line
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$species ?? [];
    }

    public static function getLocations(): array
    {
        if (is_null(self::$locations) || count(self::$locations) == 0) {
            self::$locations = [];

            $locations = self::api('/locations');

            foreach ($locations as $location) {
                // url should be string, not array
                if (is_array($location['url'])) {
                    $location['url'] = array_shift($location['url']);
                }
                $datas = array_combine(array_keys($location), array_values($location));
                self::$locations[$location['id']] = new Location( // @phpstan-ignore-next-line
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$locations ?? [];
    }

    public static function getVehicles(): array
    {
        if (is_null(self::$vehicles) || count(self::$vehicles) == 0) {
            self::$vehicles = [];

            $vehicles = self::api('/vehicles');

            foreach ($vehicles as $vehicle) {
                $datas = array_combine(array_keys($vehicle), array_values($vehicle));
                self::$vehicles[$vehicle['id']] = new Vehicle( // @phpstan-ignore-next-line
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$vehicles ?? [];
    }

    private static function api(string $url, array $args = null): array
    {
        if (is_null(self::$client)) {
            self::$client = new Client([
                'base_uri' => getenv('GHIBLIAPI_URL'),
                'headers' => ['Content-Type' => 'application/json']
            ]);
        }

        if (!is_array($args)) {
            $args = [];
        }

        $cacheKey = hash('sha512', $url . '|' . json_encode($args));
        $data = self::$cache ? self::$cache->fetch($cacheKey) : '';

        if (empty($data)) {
            try {
                $response = self::$client->request('GET', $url, $args);
                $data = $response->getBody()->getContents();

                if (self::$cache) {
                    self::$cache->save($cacheKey, $data, 3600);
                }
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    error_log(
                        sprintf(
                            '%s %s',
                            $e->getResponse()->getStatusCode(), // @phpstan-ignore-line
                            $e->getResponse()->getReasonPhrase() // @phpstan-ignore-line
                        )
                    );
                }
            }
        }

        $result = json_decode($data, true); // @phpstan-ignore-line

        return is_array($result) ? $result : [];
    }
}
