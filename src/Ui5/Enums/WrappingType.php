<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Available wrapping types for text controls that can be wrapped that enable you to display the text as hyphenated.
 *
 * @see https://sdk.openui5.org/api/sap.m.WrappingType
 */
enum WrappingType: string
{
    /**
     * Hyphenation will be used to break words on syllables where possible.
     */
    case Hyphenated = 'Hyphenated';

    /**
     * Normal text wrapping will be used. Words won't break based on hyphenation.
     */
    case Normal = 'Normal';
}
