<?php

namespace LaravelUi5\Core\Contracts;

use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

interface Ui5ContextFactoryInterface
{
    public function build(Ui5ArtifactInterface $artifact): Ui5ContextInterface;
}
