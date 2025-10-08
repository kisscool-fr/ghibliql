<?php

declare(strict_types=1);

date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/../vendor/autoload.php';


use GhibliQL\Types;
use GhibliQL\AppContext;
use GhibliQL\Data\DataSource;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;
use GraphQL\Error\FormattedError;
use GraphQL\Error\DebugFlag;

// Disable default PHP error reporting - we have better one for debug mode (see bellow)
ini_set('display_errors', '0');

$debug = DebugFlag::NONE;
if (!empty($_GET['debug'])) {
    set_error_handler(function ($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
    $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
}

try {
    // Initialize the data source
    DataSource::init();

    // Prepare context that will be available in all field resolvers (as 3rd argument):
    $appContext = new AppContext();
    $appContext->viewer = '';
    $appContext->rootUrl = $_SERVER['REQUEST_URI']; // @phpstan-ignore assign.propertyType
    $appContext->request = $_REQUEST;

    // Parse incoming query and variables
    if (
        isset($_SERVER['CONTENT_TYPE'])
        && is_string($_SERVER['CONTENT_TYPE'])
        && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
    ) {
        $raw = file_get_contents('php://input') ?: '';
        $data = (array)json_decode($raw, true);
    } else {
        $data = $appContext->request;
    }

    // GraphQL schema to be passed to query executor:
    $schema = new Schema([
        'query' => Types::query()
    ]);

    $result = GraphQL::executeQuery(
        $schema,
        $data['query'], // @phpstan-ignore argument.type
        null,
        $appContext,
        array_key_exists('variables', $data) ? $data['variables'] : [] // @phpstan-ignore argument.type
    );
    $output = $result->toArray($debug);
    $httpStatus = 200;
} catch (\Exception $error) {
    $httpStatus = 500;
    $errors = [
        FormattedError::createFromException($error, $debug)
    ];

    if (isset($output) && is_array($output)) {
        $output['errors'] = $errors;
    } else {
        $output = [
            'errors' => $errors
        ];
    }
}

header('Content-Type: application/json', true, $httpStatus);
echo json_encode($output);
