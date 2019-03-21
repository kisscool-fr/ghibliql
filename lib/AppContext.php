<?php
namespace GhibliQL;

/**
 * Class AppContext
 * Instance available in all GraphQL resolvers as 3rd argument
 *
 * @package GraphQL\Examples\Blog
 */
class AppContext
{
    /**
     * @var string
     */
    public $rootUrl;

    /**
     * @var string
     */
    public $viewer;

    /**
     * @var array
     */
    public $request;
}
