<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/aphiria/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\RouteAnnotations\Tests\Annotations;

use Aphiria\RouteAnnotations\Annotations\Get;
use PHPUnit\Framework\TestCase;

/**
 * Tests the GET annotation
 */
class GetTest extends TestCase
{
    public function testGetHttpMethodIsSet(): void
    {
        $this->assertEquals(['GET'], (new Get([]))->httpMethods);
    }
}
