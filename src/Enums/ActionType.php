<?php

namespace Thomascombe\BackpackAsyncExport\Enums;

/**
 * @phpcs:disable Generic.NamingConventions.UpperCaseConstantName
 */
abstract class ActionType
{
    public const Import = 'import';
    public const Export = 'export';

    public static function items(): array
    {
        $reflectionClass = new \ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }
}
