<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/router/blob/master/LICENSE.md
 */

namespace Aphiria\Routing\Tests\Builders;

use Aphiria\Routing\Builders\RouteBuilder;
use Aphiria\Routing\Matchers\Constraints\IRouteConstraint;
use Aphiria\Routing\Middleware\MiddlewareBinding;
use Aphiria\Routing\UriTemplates\UriTemplate;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Defines the tests for the route builder
 */
class RouteBuilderTest extends TestCase
{
    /** @var RouteBuilder The route builder to use in tests */
    private $routeBuilder;

    public function setUp(): void
    {
        $this->routeBuilder = new RouteBuilder(['GET'], new UriTemplate('/foo', 'example.com'));
    }

    public function testBuildingRouteBeforeSettingActionThrowsException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No controller specified for route');
        $this->routeBuilder->build();
    }

    public function testChainingOnFluentMethodsReturnsCorrectInstance(): void
    {
        $this->assertSame($this->routeBuilder, $this->routeBuilder->toClosure(function () {
            // Don't do anything
        }));
        $this->assertSame($this->routeBuilder, $this->routeBuilder->toMethod('Foo', 'bar'));
        $this->assertSame($this->routeBuilder, $this->routeBuilder->withAttribute('foo', 'bar'));
        $this->assertSame($this->routeBuilder, $this->routeBuilder->withManyAttributes(['foo' => 'bar']));
        $this->assertSame($this->routeBuilder, $this->routeBuilder->withManyMiddleware(['Foo']));
        $this->assertSame($this->routeBuilder, $this->routeBuilder->withMiddleware('Foo'));
        $this->assertSame($this->routeBuilder, $this->routeBuilder->withName('Foo'));
    }

    public function testAttributesAreSetWhenPassingIndividualAttributes(): void
    {
        $this->routeBuilder->withAttribute('foo', 'bar');
        $this->routeBuilder->toMethod('class', 'method');
        $route = $this->routeBuilder->build();
        $this->assertEquals(['foo' => 'bar'], $route->attributes);
    }

    public function testAttributesAreSetWhenPassingMultipleAttributes(): void
    {
        $this->routeBuilder->withManyAttributes(['foo' => 'bar']);
        $this->routeBuilder->toMethod('class', 'method');
        $route = $this->routeBuilder->build();
        $this->assertEquals(['foo' => 'bar'], $route->attributes);
    }

    public function testClosureIsSetWhenUsingClosureAction(): void
    {
        $closure = function () {
            // Don't do anything
        };
        $this->routeBuilder->toClosure($closure);
        $route = $this->routeBuilder->build();
        $this->assertSame($closure, $route->action->closure);
    }

    public function testConstraintBindingIsSet(): void
    {
        /** @var IRouteConstraint|MockObject $constraint */
        $constraint = $this->createMock(IRouteConstraint::class);
        $this->routeBuilder->withConstraint($constraint);
        $this->routeBuilder->toMethod('class', 'method');
        $route = $this->routeBuilder->build();
        $this->assertContains($constraint, $route->constraints);
    }

    public function testInvalidManyMiddlewareThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Middleware binding must either be a string or an instance of %s', MiddlewareBinding::class));
        $this->routeBuilder->withManyMiddleware([1]);
    }

    public function testManyMiddlewareBindingsAreSetWhenPassingThemInAsObjects(): void
    {
        $this->routeBuilder->withManyMiddleware([
            new MiddlewareBinding('foo', ['bar' => 'baz']),
            new MiddlewareBinding('dave', ['young' => 'cool']),
        ]);
        $this->routeBuilder->toMethod('class', 'method');
        $route = $this->routeBuilder->build();
        $this->assertCount(2, $route->middlewareBindings);
        $this->assertInstanceOf(MiddlewareBinding::class, $route->middlewareBindings[0]);
        $this->assertInstanceOf(MiddlewareBinding::class, $route->middlewareBindings[1]);
        $this->assertEquals('foo', $route->middlewareBindings[0]->className);
        $this->assertEquals('dave', $route->middlewareBindings[1]->className);
        $this->assertEquals(['bar' => 'baz'], $route->middlewareBindings[0]->attributes);
        $this->assertEquals(['young' => 'cool'], $route->middlewareBindings[1]->attributes);
    }

    public function testManyConstraintBindingIsSet(): void
    {
        $constraints = [$this->createMock(IRouteConstraint::class)];
        $this->routeBuilder->withManyConstraints($constraints);
        $this->routeBuilder->toMethod('class', 'method');
        $route = $this->routeBuilder->build();
        $this->assertContains($constraints[0], $route->constraints);
    }

    public function testManyMiddlewareBindingsAreSetWhenPassingThemInAsStrings(): void
    {
        $this->routeBuilder->withManyMiddleware(['foo', 'bar']);
        $this->routeBuilder->toMethod('class', 'method');
        $route = $this->routeBuilder->build();
        $this->assertCount(2, $route->middlewareBindings);
        $this->assertInstanceOf(MiddlewareBinding::class, $route->middlewareBindings[0]);
        $this->assertInstanceOf(MiddlewareBinding::class, $route->middlewareBindings[1]);
        $this->assertEquals('foo', $route->middlewareBindings[0]->className);
        $this->assertEquals('bar', $route->middlewareBindings[1]->className);
        $this->assertEquals([], $route->middlewareBindings[0]->attributes);
        $this->assertEquals([], $route->middlewareBindings[1]->attributes);
    }

    public function testMethodIsSetWhenUsingMethodAction(): void
    {
        $this->routeBuilder->toMethod('foo', 'bar');
        $route = $this->routeBuilder->build();
        $this->assertSame('foo', $route->action->className);
        $this->assertSame('bar', $route->action->methodName);
    }

    public function testMiddlewareBindingIsSet(): void
    {
        $this->routeBuilder->withMiddleware('foo', ['bar' => 'baz']);
        $this->routeBuilder->toMethod('class', 'method');
        $route = $this->routeBuilder->build();
        $this->assertCount(1, $route->middlewareBindings);
        $this->assertInstanceOf(MiddlewareBinding::class, $route->middlewareBindings[0]);
        $this->assertEquals('foo', $route->middlewareBindings[0]->className);
        $this->assertEquals(['bar' => 'baz'], $route->middlewareBindings[0]->attributes);
    }

    public function testNameIsSet(): void
    {
        $route = $this->routeBuilder->toMethod('class', 'method')
            ->withName('foo')
            ->build();
        $this->assertEquals('foo', $route->name);
    }

    public function testUriTemplateIsSet(): void
    {
        $expectedUriTemplate = new UriTemplate('foo', 'example.com', true);
        $routeBuilder = new RouteBuilder(['GET'], $expectedUriTemplate);
        $routeBuilder->toMethod('Foo', 'bar');
        $route = $routeBuilder->build();
        $this->assertSame($expectedUriTemplate, $route->uriTemplate);
    }
}
