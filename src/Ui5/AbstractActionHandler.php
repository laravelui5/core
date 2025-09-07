<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Contracts\Ui5Config;
use LaravelUi5\Core\Ui5\Contracts\ActionHandlerInterface;
use LaravelUi5\Core\Ui5\Contracts\ConfigurableInterface;

abstract class AbstractActionHandler implements ActionHandlerInterface, ConfigurableInterface
{

    protected Ui5Config $config;

    public function withConfig(Ui5Config $config): static
    {
        $this->config = $config;
        return $this;
    }

    public function config(): Ui5Config
    {
        return $this->config;
    }
}
