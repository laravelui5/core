<?php

namespace LaravelUi5\Core\View\Components;

use Illuminate\View\Component;
use LaravelUi5\Core\Ui5\Capabilities\ResolvableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
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
        $artifact = app(Ui5RegistryInterface::class)->get($this->id);

        if ($artifact instanceof ResolvableInterface) {
            return $artifact->resolve();
        }

        return '';
    }
}
