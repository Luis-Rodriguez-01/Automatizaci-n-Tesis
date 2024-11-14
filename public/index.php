<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Infrastructure\Middleware\AddJsonResponseHeader;

define('APP_ROOT', value: dirname(path: __DIR__));

require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder;
$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer(container: $container);

$app = AppFactory::create();

//Esto es para obtener valores directamente de los parametros, tales como el ID:
$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

//Implementando el error middleware:
$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');

//Agregando json middleware
$app->add(new AddJsonResponseHeader);
// routes 
// queries 
$app->get('/api/site', Infrastructure\Http\Controllers\Controller::class . ':getAllBlocks');
$app->get('/api/site/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':getBlockById');

// CREATE BLOCK 
$app->post('/api/site/create', Infrastructure\Http\Controllers\Controller::class . ':createBlock');

// UPDATE BLOCK 
$app->patch('/api/site/update/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':updateExistingBlock');

// DELETE BLOCK 
$app->delete('/api/site/delete/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':deleteExistingBlock');

// SHOW INDICATORS BY BLOCK 
$app->get('/api/site/show/{id}', Infrastructure\Http\Controllers\Controller::class . ':showIndicatorsByBlocks');

// SHOW ELEMENTS BY BLOCK 
$app->get('/api/site/showElem/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':showElementsByBlocks');

// UPDATE ELEMENTS 
$app->patch('/api/site/updateElem/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':updateElementsByBlocks');

// CREATE SUBSCRIPTION 
$app->post('/api/site/createSubscription', Infrastructure\Http\Controllers\SubscriptionController::class . ':createSubscription');


$app->map(['GET', 'POST', 'PUT, DELETE, PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});
$app->run();
