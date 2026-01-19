<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Exceptions\MissingDashboardException;
use LaravelUi5\Core\Traits\SluggedSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5DashboardInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5Dashboard implements Ui5DashboardInterface
{
    use SluggedSource;

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getDashboard(): string
    {
        $path = $this->getModule()
            ->getSourceStrategy()
            ->resolvePath("dashboards/{$this->getSlug()}.blade.php");

        if (!file_exists($path)) {
            throw new MissingDashboardException($path);
        }

        return file_get_contents($path);
    }
}
