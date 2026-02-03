<?php

namespace Fixtures\Hello\Errors\Settings;

use Fixtures\Hello\Actions\World\Handler;
use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Enums\HttpMethod;
use LaravelUi5\Core\Enums\SettingScope;
use LaravelUi5\Core\Enums\SettingVisibilityRole;
use LaravelUi5\Core\Enums\SettingType;
use LaravelUi5\Core\Ui5\AbstractUi5Action;
use LaravelUi5\Core\Ui5\Capabilities\ActionHandlerInterface;

#[Setting('darkMode', type: SettingType::Boolean, default: false, note: 'Something', scope: SettingScope::User, role: SettingVisibilityRole::Employee)]
class Action extends AbstractUi5Action
{
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
