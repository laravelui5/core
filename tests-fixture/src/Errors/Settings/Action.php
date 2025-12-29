<?php

namespace Fixtures\Hello\Errors\Settings;

use Fixtures\Hello\Actions\World\Handler;
use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Enums\HttpMethod;
use LaravelUi5\Core\Enums\SettingScope;
use LaravelUi5\Core\Enums\SettingVisibilityRole;
use LaravelUi5\Core\Enums\ValueType;
use LaravelUi5\Core\Ui5\AbstractUi5Action;
use LaravelUi5\Core\Ui5\Capabilities\ActionHandlerInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

#[Setting('darkMode', type: ValueType::Boolean, default: false, scope: SettingScope::User, role: SettingVisibilityRole::Employee, note: 'Something')]
class Action extends AbstractUi5Action
{

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
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
