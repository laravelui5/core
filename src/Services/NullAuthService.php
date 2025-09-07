<?php

namespace LaravelUi5\Core\Services;

use LaravelUi5\Core\Contracts;
use LaravelUi5\Core\Contracts\Ui5Context;

class NullAuthService implements Contracts\AuthServiceInterface
{

    /**
     * @inheritDoc
     */
    public function authorize(string $ability, Ui5Context $context): bool
    {
        return true;
    }
}
