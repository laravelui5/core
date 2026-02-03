<?php

namespace Fixtures\Hello\Settings;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Enums\SettingType;

#[Setting(
    key: 'foo',
    type: SettingType::String,
    default: 'bar',
    note: 'Invalid target'
)]
class NotExtendingAbstractConfigurableHandler
{

}
