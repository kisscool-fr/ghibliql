<?php

declare(strict_types=1);

namespace GhibliQL\Data;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Predis\Client as RedisClient;

/**
 * Class DataSource
 *
 * Data retrieval class
 */
class DataSource
{
    private static ?HttpClient $client = null;
    private static ?RedisClient $cache = null;

    /** @var array<Film> $films */
    private static ?array $films = null;
    /** @var array<People> $peoples */
    private static ?array $peoples = null;
    /** @var array<Specie> $species */
    private static ?array $species = null;
    /** @var array<Location> $locations */
    private static ?array $locations = null;
    /** @var array<Vehicle> $vehicles */
    private static ?array $vehicles = null;

    /**
     * @todo Smart init
     */
    public static function init(): void
    {
        if (getenv('REDIS_URL')) {
            try {
                self::$cache = new RedisClient(getenv('REDIS_URL'));
            } catch (\Predis\PredisException $e) {
                error_log('No Redis cache available');
                error_log($e->getMessage());
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
                count(array_filter($data->films, function ($url) use ($film_id) { // phpcs:ignore
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
                count(array_filter($data->films, function ($url) use ($film_id) { // phpcs:ignore
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
                count(array_filter($data->films, function ($url) use ($film_id) { // phpcs:ignore
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
                // $datas = array_combine(array_keys($film), array_values($film));
                if (!is_array($film) || !array_key_exists('id', $film) || !is_string($film['id'])) {
                    continue;
                }
                self::$films[$film['id']] = new Film($film); // @phpstan-ignore argument.type
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
                // $datas = array_combine(array_keys($people), array_values($people));
                if (!is_array($people) || !array_key_exists('id', $people) || !is_string($people['id'])) {
                    continue;
                }
                self::$peoples[$people['id']] = new People($people); // @phpstan-ignore argument.type
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
                // $datas = array_combine(array_keys($specie), array_values($specie));
                if (!is_array($specie) || !array_key_exists('id', $specie) || !is_string($specie['id'])) {
                    continue;
                }
                self::$species[$specie['id']] = new Specie($specie); // @phpstan-ignore argument.type
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
                // if (is_array($location['url'])) {
                //     $location['url'] = array_shift($location['url']);
                // }
                // $datas = array_combine(array_keys($location), array_values($location));
                if (!is_array($location) || !array_key_exists('id', $location) || !is_string($location['id'])) {
                    continue;
                }
                self::$locations[$location['id']] = new Location($location); // @phpstan-ignore argument.type
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
                // $datas = array_combine(array_keys($vehicle), array_values($vehicle));
                if (!is_array($vehicle) || !array_key_exists('id', $vehicle) || !is_string($vehicle['id'])) {
                    continue;
                }
                self::$vehicles[$vehicle['id']] = new Vehicle($vehicle); // @phpstan-ignore argument.type
            }
        }

        return self::$vehicles ?? [];
    }

    /**
     * @return array<mixed>
     */
    private static function api(string $url, ?array $args = null): array
    {
        if (is_null(self::$client)) {
            if (!getenv('GHIBLIAPI_URL')) {
                error_log('Please define GHIBLIAPI_URL source');
                exit;
            }
            self::$client = new HttpClient([
                'base_uri' => getenv('GHIBLIAPI_URL'),
                'headers' => ['Content-Type' => 'application/json']
            ]);
        }

        if (!is_array($args)) {
            $args = [];
        }

        $cacheKey = hash('fnv1a64', $url . '|' . json_encode($args)); // phpcs:ignore
        $data = self::$cache ? self::$cache->get($cacheKey) : '';

        if (empty($data)) {
            try {
                $response = self::$client->request('GET', $url, $args);
                $data = $response->getBody()->getContents();

                if (self::$cache) {
                    self::$cache->set($cacheKey, $data, 'EX', 3600);
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
