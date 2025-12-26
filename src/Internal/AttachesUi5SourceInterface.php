<?php

namespace LaravelUi5\Core\Internal;

use LaravelUi5\Core\Contracts\Ui5Source;

interface AttachesUi5SourceInterface
{
    public function __attachSource(Ui5Source $source): void;
}
