<?php

namespace LaravelUi5\Core\Enums;

/**
 * Enum representing allowed primitive value types for settings.
 */
enum SettingType: int
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
     * An array of strings (e.g. ["bp-123", "bp-456"])
     */
    case StringArray = 5;

    /**
     * An array of integers (e.g. [1, 2, 3])
     */
    case IntegerArray = 6;

    /**
     * An array of floats (e.g. [1.0, 2.5, 3.14])
     */
    case FloatArray = 7;

    /**
     * An array of booleans (e.g. [true, false])
     */
    case BooleanArray = 8;

    /**
     * A ISO-8601/UTC date string
     */
    case Date         = 9;

    /**
     * An integer representing the foreign key of a Model class
     */
    case Model        = 10;

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
            self::StringArray => 'String[]',
            self::IntegerArray => 'Integer[]',
            self::FloatArray => 'Float[]',
            self::BooleanArray => 'Boolean[]',
            self::Date         => 'Date',
            self::Model        => 'Model',
        };
    }

    public function isScalar(): bool
    {
        return in_array($this, [
            self::String,
            self::Integer,
            self::Float,
            self::Boolean,
            self::Date,
        ], true);
    }

    /**
     * Checks whether the type is an array-based type.
     */
    public function isArray(): bool
    {
        return in_array($this, [
            self::StringArray,
            self::IntegerArray,
            self::FloatArray,
            self::BooleanArray,
        ], true);
    }

    public function requiresModelClass(): bool
    {
        return $this === self::Model;
    }
}
