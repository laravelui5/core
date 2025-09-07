<?php

namespace LaravelUi5\Core\Enums;

/**
 * Enumeration of supported aggregation levels for KPIs.
 *
 * Determines how raw data is grouped and summarized over time.
 */
enum AggregationLevel: int
{
    case Daily = 1;
    case Weekly = 2;
    case Monthly = 3;
    case Quarterly = 4;
    case Semiannual = 5;
    case Yearly = 6;

    /**
     * Returns a human-readable label for UI or debugging purposes.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Daily     => 'Daily',
            self::Weekly    => 'Weekly',
            self::Monthly   => 'Monthly',
            self::Quarterly => 'Quarterly',
            self::Semiannual => 'Semiannual',
            self::Yearly    => 'Yearly',
        };
    }
}
