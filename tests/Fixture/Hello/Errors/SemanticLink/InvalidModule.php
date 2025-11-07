<?php

namespace Tests\Fixture\Hello\Errors\SemanticLink;

use Tests\Fixture\Hello\HelloModule;
use LaravelUi5\Core\Attributes\SemanticObject;
use Tests\Fixture\Hello\Models\User2;

#[SemanticObject(User2::class, 'User', ['detail' => ['uri' => '/detail/{id}', 'label' => 'User Details']])]
class InvalidModule extends HelloModule
{
}
