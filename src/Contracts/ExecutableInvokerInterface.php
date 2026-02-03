<?php

namespace LaravelUi5\Core\Contracts;

interface ExecutableInvokerInterface
{
    /**
     * Invoke a handler or provider method with resolved parameters and settings.
     *
     * @param object $target
     * @param string $method
     * @return mixed
     */
    public function invoke(object $target, string $method): mixed;
}
