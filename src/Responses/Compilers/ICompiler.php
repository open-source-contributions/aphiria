<?php

/*
 * Opulence
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

namespace Aphiria\Console\Responses\Compilers;

use Aphiria\Console\Responses\Compilers\Elements\Style;
use RuntimeException;

/**
 * Defines the interface for response compilers to implement
 */
interface ICompiler
{
    /**
     * Compiles a message
     *
     * @param string $message The message to compile
     * @return string The compiled message
     * @throws RuntimeException Thrown if there was an issue compiling the message
     */
    public function compile(string $message): string;

    /**
     * Registers an element
     *
     * @param string $name The name of the element
     * @param Style $style The style of the element
     */
    public function registerElement(string $name, Style $style): void;

    /**
     * Sets whether or not messages should be styled
     *
     * @param bool $isStyled Whether or not messages should be styled
     */
    public function setStyled(bool $isStyled): void;
}
