<?php

namespace Tests\Fixture\Hello\Errors\SemanticObject;

use LaravelUi5\Core\Attributes\SemanticObject;
use Tests\Fixture\Hello\HelloModule;

#[SemanticObject('', 'User', ['detail' => ['uri' => '/detail/{id}', 'label' => 'User Details']])]
class EmptyModule extends HelloModule
{
}
