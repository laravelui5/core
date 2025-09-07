<?php

namespace LaravelUi5\Core\Contracts;

interface AppContextInterface
{
    public function manifest(): array;

    public function i18n(): array;
}
