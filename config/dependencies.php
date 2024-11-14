<?php

use DI\Container;
use Infrastructure\Database;
use Infrastructure\Repositories\Persistence\Subscription\SubscriptionFactory;
use Infrastructure\Http\Controllers\SubscriptionController;

return [
    Database::class => function (Container $c): Database {
        return new Database(
            $c->get('localhost'),
            $c->get('root'),
            $c->get('01LYRCm.j+'),
            $c->get('IntegraDB')
        );
       
    },

    SubscriptionFactory::class => DI\autowire(SubscriptionFactory::class),

    SubscriptionController::class => function (Container $c): SubscriptionController {
        return new SubscriptionController(
            factory: $c->get(SubscriptionFactory::class),
            database: $c->get(Database::class)
        );
    },
];