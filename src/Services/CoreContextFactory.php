<?php

namespace LaravelUi5\Core\Services;

use LaravelUi5\Core\Contracts\Ui5ContextFactoryInterface;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Contracts\Ui5CoreContext;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

class CoreContextFactory implements Ui5ContextFactoryInterface
{
    public function build(Ui5ArtifactInterface $artifact): Ui5ContextInterface
    {
        $locale = request()->getLocale();

        return new Ui5CoreContext($artifact, $locale);
    }
}
