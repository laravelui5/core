<?php

namespace LaravelUi5\Core\Services;

use LaravelUi5\Core\Contracts\ParameterResolverInterface;
use LaravelUi5\Core\Contracts\SettingResolverInterface;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Ui5\Contracts\ConfigurableInterface;
use LaravelUi5\Core\Ui5\Contracts\ExecutableInterface;
use LaravelUi5\Core\Ui5\Contracts\ParameterizableInterface;

readonly class ExecutableHandler
{
    public function __construct(
        private ParameterResolverInterface $parameterResolver,
        private SettingResolverInterface   $settingResolver,
        private Ui5ContextInterface             $context,
    ) {}

    /**
     * Resolve args/config as needed and execute the provider.
     *
     * @param ExecutableInterface $executable
     * @return array<string,mixed>
     */
    public function run(ExecutableInterface $executable): array
    {
        if ($executable instanceof ParameterizableInterface) {
            $executable->withArgs(
                $this->parameterResolver->resolve($executable)
            );
        }

        if ($executable instanceof ConfigurableInterface) {
            $executable->withConfig(
                $this->settingResolver->resolve($executable, $this->context)
            );
        }

        return $executable->execute();
    }
}
