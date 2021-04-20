<?php

namespace Thomascombe\BackpackAsyncExport\Enums;

/**
 * @phpcs:disable Generic.NamingConventions.UpperCaseConstantName
 */
abstract class ExportStatus
{
    public const Created = 'created';
    public const Processing = 'processing';
    public const Successful = 'successful';
    public const Error = 'error';
    public const Deleted = 'deleted';

    public static function items(): array
    {
        $reflectionClass = new \ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }
}
