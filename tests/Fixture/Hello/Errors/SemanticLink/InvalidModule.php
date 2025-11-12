<?php

namespace Tests\Fixture\Hello\Errors\SemanticLink;

use LaravelUi5\Core\Attributes\Role;
use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloModule;
use LaravelUi5\Core\Attributes\SemanticObject;
use Tests\Fixture\Hello\Models\User2;

#[Role(Hello::ROLE, 'Administrative access to Hello module')]
#[SemanticObject(User2::class, 'User', ['detail' => ['uri' => '/detail/{id}', 'label' => 'User Details']])]
class InvalidModule extends HelloModule
{
}
