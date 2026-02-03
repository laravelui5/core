<?php

namespace Fixtures\Hello\Settings;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Ui5\AbstractConfigurable;
use LaravelUi5\Core\Enums\SettingType;

#[Setting(
    key: 'maxItems',
    type: SettingType::Integer,
    default: 10,
    note: 'Maximum items'
)]
#[Setting(
    key: 'enabled',
    type: SettingType::Boolean,
    default: true,
    note: 'Feature toggle'
)]
class SettingHandler extends AbstractConfigurable
{
    public function handle(): void {}
}
