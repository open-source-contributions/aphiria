<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/route-annotations/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\RouteAnnotations\Tests\Annotations;

use Aphiria\RouteAnnotations\Annotations\RouteConstraint;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Tests the route constraint annotation
 */
class RouteConstraintTest extends TestCase
{
    public function testConstructorParamsCanBeSetFromConstructorParams(): void
    {
        $constraint = new RouteConstraint(['className' => 'foo', 'constructorParams' => ['foo']]);
        $this->assertEquals(['foo'], $constraint->constructorParams);
    }

    public function testConstructorParamsDefaultToEmptyArrayWhenNotSpecified(): void
    {
        $this->assertEquals([], (new RouteConstraint(['className' => 'foo']))->constructorParams);
    }

    public function testClassNameCanBeSetFromClassName(): void
    {
        $this->assertEquals('foo', (new RouteConstraint(['className' => 'foo']))->className);
    }

    public function testClassNameCanBeSetFromValue(): void
    {
        $this->assertEquals('foo', (new RouteConstraint(['value' => 'foo']))->className);
    }

    public function testEmptyClassNameThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class name must be set');
        new RouteConstraint([]);
    }
}
