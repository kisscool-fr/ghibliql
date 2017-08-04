<?php

date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/../vendor/autoload.php';


use \GhibliQL\Types;
use \GhibliQL\AppContext;
use \GhibliQL\Data\DataSource;
use \GraphQL\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Type\Definition\Config;
use \GraphQL\Error\FormattedError;

// Disable default PHP error reporting - we have better one for debug mode (see bellow)
// ini_set('display_errors', 0);

if (!empty($_GET['debug'])) {
    // Enable additional validation of type configs
    // (disabled by default because it is costly)
    Config::enableValidation();

    // Catch custom errors (to report them in query results if debugging is enabled)
    $phpErrors = [];
    
    set_error_handler(function($severity, $message, $file, $line) use (&$phpErrors) {
        $phpErrors[] = new ErrorException($message, 0, $severity, $file, $line);
    });
}

try {
    // Initialize the data source
    DataSource::init();

    // Prepare context that will be available in all field resolvers (as 3rd argument):
    $appContext = new AppContext();
    $appContext->viewer = '';
    $appContext->rootUrl = 'http://localhost:8080';
    $appContext->request = $_REQUEST;

    // Parse incoming query and variables
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
    }

    if (is_null($data)) {
        $data = $_REQUEST;
    }

    // GraphQL schema to be passed to query executor:
    $schema = new Schema([
        'query' => Types::query()
    ]);

    $result = GraphQL::execute(
        $schema,
        $data['query'],
        null,
        $appContext,
        (array) $data['variables']
    );

    // Add reported PHP errors to result (if any)
    if (!empty($_GET['debug']) && !empty($phpErrors)) {
        $result['extensions']['phpErrors'] = array_map(
            ['GraphQL\Error\FormattedError', 'createFromPHPError'],
            $phpErrors
        );
    }

    $httpStatus = 200;
} catch (\Exception $error) {
    $httpStatus = 500;

    if (!empty($_GET['debug'])) {
        $result['extensions']['exception'] = FormattedError::createFromException($error);
    } else {
        $result['errors'] = [FormattedError::create('Unexpected Error')];
    }
}

header('Content-Type: application/json', true, $httpStatus);
echo json_encode($result);
