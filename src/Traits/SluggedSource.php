<?php

namespace LaravelUi5\Core\Traits;

trait SluggedSource
{
    private function getSlug(): string
    {
        $parts = explode('.', $this->getNamespace());
        return array_pop($parts);
    }
}
