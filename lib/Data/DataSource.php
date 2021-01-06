<?php
namespace GhibliQL\Data;

/**
 * Class DataSource
 *
 * Data retrieval class
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DataSource
{
    private static $client = null;
    private static $cache = null;

    private static $films = null;
    private static $peoples = null;
    private static $species = null;
    private static $locations = null;
    private static $vehicles = null;

    /**
     * @todo Smart init
     */
    public static function init()
    {
        if (getenv('HEROKU_REDIS_AQUA_URL')) {
            try {
                self::$cache = new \Doctrine\Common\Cache\PredisCache(
                    new \Predis\Client(getenv('HEROKU_REDIS_AQUA_URL'))
                );
            } catch (\Predis\PredisException $e) {
            }
        }
    }

    public static function findFilm(string $id)
    {
        if (is_null(self::$films)) {
            self::getFilms();
        }

        return isset(self::$films[$id]) ? self::$films[$id] : null;
    }

    public static function findPeople(string $id)
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

        foreach (self::$peoples as $id => $data) {
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (count(array_filter($data->films, function ($url) use ($film_id) {
                $id = substr($url, strrpos($url, '/')+1);
                return ($id == $film_id);
            })) > 0) {
                $peoples[$id] = $data;
            }
        }

        return $peoples;
    }

    public static function findSpecie(string $id)
    {
        if (is_null(self::$species)) {
            self::getSpecies();
        }

        return isset(self::$species[$id]) ? self::$species[$id] : null;
    }

    public static function findLocation(string $id)
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

        foreach (self::$locations as $id => $data) {
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (count(array_filter($data->films, function ($url) use ($film_id) {
                $id = substr($url, strrpos($url, '/')+1);
                return ($id == $film_id);
            })) > 0) {
                $locations[$id] = $data;
            }
        }

        return $locations;
    }

    public static function findVehicle(string $id)
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

        foreach (self::$vehicles as $id => $data) {
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (count(array_filter($data->films, function ($url) use ($film_id) {
                $id = substr($url, strrpos($url, '/')+1);
                return ($id == $film_id);
            })) > 0) {
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
                self::$films[$film['id']] = new Film(
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$films;
    }

    public static function getPeoples(): array
    {
        if (is_null(self::$peoples) || count(self::$peoples) == 0) {
            self::$peoples = [];

            $peoples = self::api('/people');

            foreach ($peoples as $people) {
                $datas = array_combine(array_keys($people), array_values($people));
                self::$peoples[$people['id']] = new People(
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$peoples;
    }

    public static function getSpecies(): array
    {
        if (is_null(self::$species) || count(self::$species) == 0) {
            self::$species = [];

            $species = self::api('/species');

            foreach ($species as $specie) {
                $datas = array_combine(array_keys($specie), array_values($specie));
                self::$species[$specie['id']] = new Specie(
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$species;
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
                self::$locations[$location['id']] = new Location(
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$locations;
    }

    public static function getVehicles(): array
    {
        if (is_null(self::$vehicles) || count(self::$vehicles) == 0) {
            self::$vehicles = [];

            $vehicles = self::api('/vehicles');

            foreach ($vehicles as $vehicle) {
                $datas = array_combine(array_keys($vehicle), array_values($vehicle));
                self::$vehicles[$vehicle['id']] = new Vehicle(
                    is_array($datas) ? $datas : []
                );
            }
        }

        return self::$vehicles;
    }

    private static function api(string $url, array $args=null): array
    {
        if (is_null(self::$client)) {
            self::$client = new Client([
                'base_uri' => 'https://ghibliapi.herokuapp.com',
                'headers' => ['Content-Type' => 'application/json']
            ]);
        }

        if (!is_array($args)) {
            $args = [];
        }

        $cacheKey = hash('sha1', $url . '|' . json_encode($args));
        $data = self::$cache ? self::$cache->fetch($cacheKey) : false;

        if (false === $data) {
            try {
                $response = self::$client->request('GET', $url, $args);
                $data = $response->getBody()->getContents();

                if (self::$cache) {
                    self::$cache->save($cacheKey, $data, 3600);
                }
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    error_log($e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase());
                }
            }
        }

        return json_decode($data, true);
    }
}
