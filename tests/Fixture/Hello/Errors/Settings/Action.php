<?php

namespace Tests\Fixture\Hello\Errors\Settings;

use LaravelUi5\Core\Attributes\Ability;
use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Enums\AbilityType;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Enums\HttpMethod;
use LaravelUi5\Core\Enums\SettingScope;
use LaravelUi5\Core\Enums\SettingVisibilityRole;
use LaravelUi5\Core\Enums\ValueType;
use LaravelUi5\Core\Ui5\Contracts\ActionHandlerInterface;
use LaravelUi5\Core\Ui5\AbstractUi5Action;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use Tests\Fixture\Hello\Actions\World\Handler;
use Tests\Fixture\Hello\Hello;

#[Setting('darkMode', type: ValueType::Boolean, default: false, scope: SettingScope::User, visibilityRole: SettingVisibilityRole::Employee)]
class Action extends AbstractUi5Action
{

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): ?Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.hello.actions.world';
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Action;
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'World';
    }

    public function getDescription(): string
    {
        return 'Action for World';
    }

    public function getSlug(): string
    {
        return 'world';
    }

    public function getMethod(): HttpMethod
    {
        return HttpMethod::POST;
    }

    public function getHandler(): ActionHandlerInterface
    {
        return new Handler();
    }
}
