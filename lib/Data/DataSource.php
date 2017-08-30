<?php
namespace GhibliQL\Data;

/**
 * Class DataSource
 *
 * This is just a simple in-memory data holder for the sake of example.
 * Data layer for real app may use Doctrine or query the database directly (e.g. in CQRS style)
 *
 * @package GraphQL\Examples\Blog
 */

use Curl\Curl;

class DataSource
{
    private static $curl = null;
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
        $cacheDir = __DIR__ . '/../../cache/data';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        self::$cache = new \Doctrine\Common\Cache\FilesystemCache(realpath($cacheDir));
    }

    public static function findFilm($id)
    {
        if (is_null(self::$films)) {
            self::getFilms();
        }

        return isset(self::$films[$id]) ? self::$films[$id] : null;
    }

    public static function findPeople($id)
    {
        if (is_null(self::$peoples)) {
            self::getPeoples();
        }

        return isset(self::$peoples[$id]) ? self::$peoples[$id] : null;
    }

    public static function findPeoplesForFilm($film_id)
    {
        if (is_null(self::$peoples)) {
            self::getPeoples();
        }

        $peoples = [];

        foreach (self::$peoples as $id => $data) {
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (count(array_filter($data->films, function($url) use ($film_id) {
                $id = substr($url, strrpos($url, '/')+1);
                return ($id == $film_id);
            })) > 0) {
                $peoples[$id] = $data;
            }
        }

        return $peoples;
    }

    public static function findSpecie($id)
    {
        if (is_null(self::$species)) {
            self::getSpecies();
        }

        return isset(self::$species[$id]) ? self::$species[$id] : null;
    }

    public static function findLocation($id)
    {
        if (is_null(self::$locations)) {
            self::getLocations();
        }

        return isset(self::$locations[$id]) ? self::$locations[$id] : null;
    }

    public static function findLocationsForFilm($film_id)
    {
        if (is_null(self::$locations)) {
            self::getLocations();
        }

        $locations = [];

        foreach (self::$locations as $id => $data) {
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (count(array_filter($data->films, function($url) use ($film_id) {
                $id = substr($url, strrpos($url, '/')+1);
                return ($id == $film_id);
            })) > 0) {
                $locations[$id] = $data;
            }
        }

        return $locations;
    }

    public static function findVehicle($id)
    {
        if (is_null(self::$vehicles)) {
            self::getVehicles();
        }

        return isset(self::$vehicles[$id]) ? self::$vehicles[$id] : null;
    }

    public static function findVehiclesForFilm($film_id)
    {
        if (is_null(self::$vehicles)) {
            self::getVehicles();
        }

        $vehicles = [];

        foreach (self::$vehicles as $id => $data) {
            if (is_string($data->films)) {
                $data->films = [$data->films];
            }
            if (count(array_filter($data->films, function($url) use ($film_id) {
                $id = substr($url, strrpos($url, '/')+1);
                return ($id == $film_id);
            })) > 0) {
                $vehicles[$id] = $data;
            }
        }

        return $vehicles;
    }

    public static function getFilms()
    {
        if (count(self::$films) == 0 ) {
            self::$films = [];

            $films = self::api('https://ghibliapi.herokuapp.com/films');

            foreach ($films as $film) {
                self::$films[$film['id']] = new Film(
                    array_combine(array_keys($film), array_values($film))
                );
            }
        }

        return self::$films;
    }

    public static function getPeoples()
    {
        if (count(self::$peoples) == 0 ) {
            self::$peoples = [];

            $peoples = self::api('https://ghibliapi.herokuapp.com/people');

            foreach ($peoples as $people) {
                self::$peoples[$people['id']] = new People(
                    array_combine(array_keys($people), array_values($people))
                );
            }
        }

        return self::$peoples;
    }

    public static function getSpecies()
    {
        if (count(self::$species) == 0 ) {
            self::$species = [];

            $species = self::api('https://ghibliapi.herokuapp.com/species');

            foreach ($species as $specie) {
                self::$species[$specie['id']] = new Specie(
                    array_combine(array_keys($specie), array_values($specie))
                );
            }
        }

        return self::$species;
    }

    public static function getLocations()
    {
        if (count(self::$locations) == 0 ) {
            self::$locations = [];

            $locations = self::api('https://ghibliapi.herokuapp.com/locations');

            foreach ($locations as $location) {
                // url should be string, not array
                if (is_array($location['url'])) {
                    $location['url'] = array_shift($location['url']);
                }
                self::$locations[$location['id']] = new Location(
                    array_combine(array_keys($location), array_values($location))
                );
            }
        }

        return self::$locations;
    }

    public static function getVehicles()
    {
        if (count(self::$vehicles) == 0 ) {
            self::$vehicles = [];

            $vehicles = self::api('https://ghibliapi.herokuapp.com/vehicles');

            foreach ($vehicles as $vehicle) {
                self::$vehicles[$vehicle['id']] = new Vehicle(
                    array_combine(array_keys($vehicle), array_values($vehicle))
                );
            }
        }

        return self::$vehicles;
    }


    /**
     * @todo Smart cache
     */
    private static function api($url, $args=null)
    {
        if (is_null(self::$curl)) {
            self::$curl = new Curl();
            self::$curl->setOpt(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            self::$curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        }

        $cacheKey = hash('sha1', $url . '|' . json_encode($args));
        $data = self::$cache->fetch($cacheKey);

        if (false === $data) {
            self::$curl->get($url, $args);

            if (self::$curl->error) {
                throw new \Exception('Error ' . self::$curl->error_code . ' : ' . self::$curl->error_message . ' (' . $url . ', args:'.json_encode($args).')');
            } else {
                $data = self::$curl->response;
                self::$cache->save($cacheKey, $data, 3600);
            }
        }

        return json_decode($data, true);  
    }
}
