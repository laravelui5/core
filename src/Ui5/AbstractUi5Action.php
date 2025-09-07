<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Contracts\Ui5Args;
use LaravelUi5\Core\Ui5\Contracts\ParameterizableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ActionInterface;

abstract class AbstractUi5Action implements Ui5ActionInterface, ParameterizableInterface
{
    protected Ui5Args $args;

    public function withArgs(Ui5Args $args): static
    {
        $this->args = $args;
        return $this;
    }

    public function args(): Ui5Args
    {
        return $this->args;
    }
}
