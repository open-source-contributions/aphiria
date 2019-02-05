<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/router/blob/master/LICENSE.md
 */

namespace Aphiria\Routing\Tests\Matchers;

use Aphiria\Routing\Matchers\MatchedRouteCandidate;
use Aphiria\Routing\MethodRouteAction;
use Aphiria\Routing\Route;
use Aphiria\Routing\UriTemplates\UriTemplate;
use PHPUnit\Framework\TestCase;

/**
 * Tests a matched route candidate
 */
class MatchedRouteCandidateTest extends TestCase
{
    public function testPropertiesSetCorrectlyInConstructor(): void
    {
        $expectedRoute = new Route(new UriTemplate(''), new MethodRouteAction('Foo', 'bar'), []);
        $expectedRouteVariables = ['foo' => 'bar'];
        $matchedRoute = new MatchedRouteCandidate($expectedRoute, $expectedRouteVariables);
        $this->assertSame($expectedRoute, $matchedRoute->route);
        $this->assertSame($expectedRouteVariables, $matchedRoute->routeVariables);
    }
}
