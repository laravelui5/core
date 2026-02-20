<?php

namespace Fixtures\Hello\Settings;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Ui5\AbstractConfigurable;
use LaravelUi5\Core\Enums\ValueType;

#[Setting(key: 'dup', type: ValueType::String, default: 'a', note: 'first')]
#[Setting(key: 'dup', type: ValueType::String, default: 'b', note: 'second')]
class DuplicateSettingsHandler extends AbstractConfigurable
{
}
