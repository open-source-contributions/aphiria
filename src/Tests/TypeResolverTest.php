<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Serialization\Tests;

use Opulence\Serialization\Tests\Encoding\Mocks\User;
use Opulence\Serialization\TypeResolver;

/**
 * Tests the type resolver
 */
class TypeResolverTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingArrayTypeReturnsNullForNonTypedArrays(): void
    {
        $this->assertNull(TypeResolver::getArrayType('string'));
        $this->assertNull(TypeResolver::getArrayType('[]'));
        $this->assertNull(TypeResolver::getArrayType('array'));
    }

    public function testGettingArrayTypeReturnsTypeTypedArrays(): void
    {
        $this->assertEquals(User::class, TypeResolver::getArrayType(User::class . '[]'));
    }

    public function testResolvingEmptyArrayReturnsArrayType(): void
    {
        $this->assertEquals('array', TypeResolver::resolveType([]));
    }

    public function testResolvingNonEmptyArrayReturnsTypeOfFirstValue(): void
    {
        $this->assertEquals('string[]', TypeResolver::resolveType(['foo', 'bar']));
    }

    public function testResolvingTypeForObjectUsesObjectsClassName(): void
    {
        $this->assertEquals(User::class, TypeResolver::resolveType(new User(123, 'foo@bar.com')));
    }

    public function testResolvingTypeForScalarUsesScalarType(): void
    {
        $this->assertEquals('boolean', TypeResolver::resolveType(true));
        $this->assertEquals('integer', TypeResolver::resolveType(1));
        $this->assertEquals('double', TypeResolver::resolveType(1.5));
        $this->assertEquals('string', TypeResolver::resolveType('foo'));
    }

    public function testTypeIsArrayReturnsTrueOnlyForArraysOfTypes(): void
    {
        $this->assertTrue(TypeResolver::typeIsArray('array'));
        $this->assertTrue(TypeResolver::typeIsArray(User::class . '[]'));
        $this->assertFalse(TypeResolver::typeIsArray('string'));
    }
}
