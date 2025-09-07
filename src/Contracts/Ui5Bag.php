<?php

namespace LaravelUi5\Core\Contracts;

use LaravelUi5\Core\Exceptions\UndefinedBagKeyException;

/**
 * Immutable key-value bag with typed accessors.
 *
 * This base class is used for both Ui5Args (runtime parameters)
 * and Ui5Config (resolved settings). It assumes that all values
 * have already been validated and cast by the respective Resolver.
 *
 * Responsibilities:
 * - Provide read-only access to the underlying data array.
 * - Offer typed getter methods with default values.
 * - Keep logic minimal: no additional casting or validation.
 *
 * Example:
 * ```php
 * $args = new Ui5Args(['year' => 2025, 'active' => true]);
 * $year = $args->int('year');           // 2025
 * $active = $args->bool('active');      // true
 * $missing = $args->string('foo', 'x'); // "x"
 * ```
 */
abstract readonly class Ui5Bag
{
    /** @param array<string,mixed> $data */
    public function __construct(protected array $data) {}

    /**
     * Return the raw underlying array.
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Return a raw value by key, with optional default.
     */
    public function get(string $key): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];

        }
        throw new UndefinedBagKeyException($key);
    }

    /**
     * Return a string value or default.
     */
    public function string(string $key): ?string
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];

        }
        throw new UndefinedBagKeyException($key);
    }

    /**
     * Return an integer value or default.
     */
    public function int(string $key): ?int
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];

        }
        throw new UndefinedBagKeyException($key);
    }

    /**
     * Return a float value or default.
     */
    public function float(string $key): ?float
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];

        }
        throw new UndefinedBagKeyException($key);
    }

    /**
     * Return a boolean value or default.
     */
    public function bool(string $key): ?bool
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];

        }
        throw new UndefinedBagKeyException($key);
    }

    /**
     * Return an array value or default.
     */
    public function array(string $key): ?array
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];

        }
        throw new UndefinedBagKeyException($key);
    }
}
