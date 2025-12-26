<?php

namespace LaravelUi5\Core\Services;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Contracts\ConfigurableInterface;
use LaravelUi5\Core\Contracts\SettingResolverInterface;
use LaravelUi5\Core\Contracts\Ui5Config;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Exceptions\MissingDefaultValueException;
use ReflectionClass;

readonly class SettingResolver implements SettingResolverInterface
{
    public function resolve(ConfigurableInterface $target, ?Ui5ContextInterface $ctx = null): Ui5Config
    {
        $reflection = new ReflectionClass($target);
        $settings = $reflection->getAttributes(Setting::class);

        $out = [];
        foreach ($settings as $s) {
            $setting = $s->newInstance();
            if (null === $setting->default) {
                throw new MissingDefaultValueException($setting->key);
            }
            $out[$setting->key] = $setting->default;
        }

        return new Ui5Config($out);
    }
}
