<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Contracts\ConfigurableInterface;
use LaravelUi5\Core\Contracts\ParameterizableInterface;
use LaravelUi5\Core\Contracts\Ui5Args;
use LaravelUi5\Core\Contracts\Ui5Config;
use LaravelUi5\Core\Ui5\Capabilities\DataProviderInterface;

abstract class AbstractDataProvider implements DataProviderInterface, ConfigurableInterface, ParameterizableInterface
{

    protected Ui5Config $config;
    protected Ui5Args $args;

    public function withConfig(Ui5Config $config): static
    {
        $this->config = $config;
        return $this;
    }

    public function config(): Ui5Config
    {
        return $this->config;
    }

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
