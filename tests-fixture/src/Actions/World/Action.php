<?php

namespace Fixtures\Hello\Actions\World;

use Fixtures\Hello\Hello;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Enums\HttpMethod;
use LaravelUi5\Core\Ui5\AbstractUi5Action;
use LaravelUi5\Core\Ui5\Contracts\ActionHandlerInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

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
        return Hello::ACTION_SLUG;
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
