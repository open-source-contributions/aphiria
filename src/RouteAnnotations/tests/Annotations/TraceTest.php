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

use Aphiria\RouteAnnotations\Annotations\Trace;
use PHPUnit\Framework\TestCase;

/**
 * Tests the TRACE annotation
 */
class TraceTest extends TestCase
{
    public function testTraceHttpMethodIsSet(): void
    {
        $this->assertEquals(['TRACE'], (new Trace([]))->httpMethods);
    }
}
