<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/route-matcher/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Matchers;

/**
 * Defines the interface for route matchers to implement
 */
interface IRouteMatcher
{
    /**
     * Tries to match a request to the list of routes
     *
     * @param string $httpMethod The HTTP method of the request
     * @param string $host The host of the request
     * @param string $path The path of the request
     * @param array $headers The mapping of header names to values
     * @return MatchedRoute The matched route, if one was found
     * @throws RouteNotFoundException Thrown if no matching route was found
     */
    public function match(string $httpMethod, string $host, string $path, array $headers = []) : MatchedRoute;
}
