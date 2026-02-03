<?php

namespace LaravelUi5\Core\Enums;

/**
 * Enum representing allowed primitive value types for settings.
 */
enum ParameterType: int
{
    /**
     * A string value (e.g. 'EUR', 'dark', 'ProjectVH')
     */
    case String = 1;

    /**
     * An integer value (e.g. 42, 0, 365)
     */
    case Integer = 2;

    /**
     * A floating-point number (e.g. 19.99, 0.875)
     */
    case Float = 3;

    /**
     * A boolean value (true or false)
     */
    case Boolean = 4;

    /**
     * A ISO-8601/UTC date string
     */
    case Date = 5;

    /**
     * An integer representing the foreign key of a Model class
     */
    case Model = 6;

    /**
     * Returns a human-readable label for this type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::String => 'String',
            self::Integer => 'Integer',
            self::Float => 'Float',
            self::Boolean => 'Boolean',
            self::Date => 'Date',
            self::Model => 'Model',
        };
    }
}
