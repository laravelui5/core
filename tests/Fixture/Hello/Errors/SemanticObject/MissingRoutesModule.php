<?php

namespace Tests\Fixture\Hello\Errors\SemanticObject;

use LaravelUi5\Core\Attributes\SemanticObject;
use Tests\Fixture\Hello\HelloModule;
use Tests\Fixture\Hello\Models\User;

#[SemanticObject(User::class, 'User', [])]
class MissingRoutesModule extends HelloModule
{
}
