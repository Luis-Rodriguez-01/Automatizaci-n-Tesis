<?php

declare(strict_types=1);

use Slim\App;
use Infrastructure\Http\Controllers\Controller;
use Infrastructure\Http\Controllers\SuscriptionController;

return function (App $app) {
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
    $app->get('/api/site/show/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':showIndicatorsByBlocks');

    // SHOW ELEMENTS BY BLOCK 
    $app->get('/api/site/showElem/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':showElementsByBlocks');

    // UPDATE ELEMENTS 
    $app->patch('/api/site/updateElem/{id:[0-9]+}', Infrastructure\Http\Controllers\Controller::class . ':updateElementsByBlocks');

    // CREATE SUBSCRIPTION 
    $app->post('/api/site/createBasicSubscription', Infrastructure\Http\Controllers\SuscriptionController::class . ':createBasicSubscription');
    $app->post('/api/site/createMediumSubscription', Infrastructure\Http\Controllers\SuscriptionController::class . ':createMediumSubscription');
    $app->post('/api/site/createAdvancedSubscription', Infrastructure\Http\Controllers\SuscriptionController::class . ':createAdvancedSubscription');
};
