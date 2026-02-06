<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Ui5\Contracts\Ui5Infrastructure;
use LogicException;

/**
 * Collects infrastructure UI5 modules declared by Service Providers
 * before the UI5 registry is built.
 */
final class Ui5InfrastructureCollector
{
    /**
     * @var array<class-string<Ui5Infrastructure>>
     */
    private array $modules = [];

    /**
     * Register an infrastructure UI5 module class.
     *
     * @param class-string<Ui5Infrastructure> $module
     */
    public function add(string $module): Ui5InfrastructureCollector
    {
        if (! is_subclass_of($module, Ui5Infrastructure::class)) {
            throw new LogicException(sprintf(
                'Class [%s] must implement %s.',
                $module,
                Ui5Infrastructure::class
            ));
        }

        $this->modules[$module] = $module;

        return $this;
    }

    /**
     * Return all collected infrastructure module classes.
     *
     * @return array<class-string<Ui5Infrastructure>>
     */
    public function all(): array
    {
        return array_values($this->modules);
    }
}
