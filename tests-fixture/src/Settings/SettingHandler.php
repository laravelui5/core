<?php

namespace Fixtures\Hello\Settings;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Ui5\AbstractConfigurable;
use LaravelUi5\Core\Enums\ValueType;

#[Setting(
    key: 'maxItems',
    type: ValueType::Integer,
    default: 10,
    note: 'Maximum items'
)]
#[Setting(
    key: 'enabled',
    type: ValueType::Boolean,
    default: true,
    note: 'Feature toggle'
)]
class SettingHandler extends AbstractConfigurable
{
    public function handle(): void {}
}
