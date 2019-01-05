<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (c) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Serialization\Encoding;

/**
 * Defines the interface for property name formatters to implement
 */
interface IPropertyNameFormatter
{
    /**
     * Formats a property name
     *
     * @param string $propertyName The property name to format
     * @return string The formatted property name
     */
    public function formatPropertyName(string $propertyName): string;
}
