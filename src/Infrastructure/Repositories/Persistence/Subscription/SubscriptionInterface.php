<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Persistence\Subscription;

interface SubscriptionInterface {
    public function createSubscription(string $tipo): array;
    
}
