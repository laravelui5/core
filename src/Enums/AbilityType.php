<?php

namespace LaravelUi5\Core\Enums;

use InvalidArgumentException;

enum AbilityType: int
{
    case Use = 0;
    case Act = 1;
    case See = 2;

    public function label(): string
    {
        return match ($this) {
            self::Use => 'use',
            self::Act => 'act',
            self::See => 'see',
        };
    }

    public function isAct(): bool
    {
        return $this === self::Act;
    }

    public static function fromLabel(string $label): self
    {
        return match ($label) {
            'use' => self::Use,
            'act' => self::Act,
            'see' => self::See,
            default => throw new InvalidArgumentException("Unknown AbilityType label: $label"),
        };
    }
}
