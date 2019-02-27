<?php

/*
 * Opulence
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

namespace Aphiria\Console\Tests\Output;

use Aphiria\Console\Output\SilentOutput;
use PHPUnit\Framework\TestCase;

/**
 * Tests the silent output
 */
class SilentOutputTest extends TestCase
{
    /** @var SilentOutput */
    private $output;

    protected function setUp(): void
    {
        $this->output = new SilentOutput();
    }

    public function testReadLineReturnsEmptyString(): void
    {
        $this->assertEquals('', $this->output->readLine());
    }

    public function testWrite(): void
    {
        ob_start();
        $this->output->write('foo');
        $this->assertEmpty(ob_get_clean());
    }

    public function testWriteln(): void
    {
        ob_start();
        $this->output->writeln('foo');
        $this->assertEmpty(ob_get_clean());
    }
}
