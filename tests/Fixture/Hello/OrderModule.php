<?php

namespace Tests\Fixture\Hello;

use Tests\Fixture\Hello\Models\Order;
use LaravelUi5\Core\Attributes\SemanticObject;

#[SemanticObject(Order::class, 'Order', ['detail' => ['uri' => '/detail/{id}', 'label' => 'Order Details']])]
class OrderModule extends HelloLibModule
{
}
