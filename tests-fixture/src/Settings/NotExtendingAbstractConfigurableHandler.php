<?php

namespace Fixtures\Hello\Settings;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Enums\ValueType;

#[Setting(
    key: 'foo',
    type: ValueType::String,
    default: 'bar',
    note: 'Invalid target'
)]
class NotExtendingAbstractConfigurableHandler
{

}
