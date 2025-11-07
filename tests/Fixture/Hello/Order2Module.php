<?php

namespace Tests\Fixture\Hello;

use LaravelUi5\Core\Attributes\SemanticObject;
use Tests\Fixture\Hello\Models\Order2;

#[SemanticObject(Order2::class, 'Order', ['detail' => ['uri' => '/detail/{id}', 'label' => 'Order Details']])]
class Order2Module extends HelloLibModule
{
}
