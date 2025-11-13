<?php

namespace LaravelUi5\Core\View\Components;

use Illuminate\View\Component;
use LaravelUi5\Core\Contracts\AuthServiceInterface;
use LaravelUi5\Core\Contracts\Ui5Context;
use LaravelUi5\Core\Ui5\Contracts\ResolvableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RuntimeInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Ui5Element extends Component
{

    public function __construct(public string $id)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function render(): string
    {
        $artifact = app(Ui5RuntimeInterface::class)->get($this->id);

        if ($artifact instanceof ResolvableInterface) {
            if (app(AuthServiceInterface::class)->authorize(
                $artifact->getNamespace(),
                app(Ui5Context::class)
            )) {
                return $artifact->resolve();
            }
        }

        return '';
    }
}
