<?php

declare(strict_types=1);

use Infrastructure\Database;
use Infrastructure\Repositories\Persistence\Subscription\SubscriptionFactory;
use Infrastructure\Http\Controllers\SubscriptionController;

return [
    // Configurar el SubscriptionFactory para que sea autowire (inyección automática de dependencias)
    SubscriptionFactory::class => DI\autowire(SubscriptionFactory::class)
        ->constructorParameter('database', DI\get(Database::class)),

    // Configurar el SubscriptionController para que sea autowire e inyecte las dependencias necesarias
    SubscriptionController::class => DI\autowire(SubscriptionController::class)
        ->constructorParameter('factory', DI\get(SubscriptionFactory::class))
        ->constructorParameter('database', DI\get(Database::class)),

    // Configuración de la clase Database para que se inyecte la configuración correctamente
    Database::class => function () {
        return new Database(
            host: 'localhost',
            name: 'integrardb',
            user: 'root',
            password: '01LYRCm.j+'
        );
    },
];