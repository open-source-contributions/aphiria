<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (c) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Serialization\Tests\Encoding\Mocks;

/**
 * Mocks a class with untyped constructor params but with typed getters
 */
class ConstructorWithUntypedPararmsWithTypedGetters
{
    private $foo;
    private $bar;
    private $baz;

    public function __construct($foo, $bar, $baz)
    {
        $this->foo = $foo;
        $this->bar = (bool)$bar;
        $this->baz = (bool)$baz;
    }

    public function getFoo(): User
    {
        return $this->foo;
    }

    public function hasBaz(): bool
    {
        return $this->baz;
    }

    public function isBar(): bool
    {
        return $this->bar;
    }
}
