<?php

/*
 * Opulence
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

namespace Aphiria\Console\Tests\Requests;

use Aphiria\Console\Requests\Request;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Tests the console request
 */
class RequestTest extends TestCase
{
    /** @var Request The request to use in tests */
    private $request;

    /**
     * Sets up the tests
     */
    public function setUp(): void
    {
        $this->request = new Request();
    }

    /**
     * Tests adding multiple values for an option
     */
    public function testAddingMultipleValuesForOption(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertEquals('bar', $this->request->getOptionValue('foo'));
        $this->request->addOptionValue('foo', 'baz');
        $this->assertEquals(['bar', 'baz'], $this->request->getOptionValue('foo'));
    }

    /**
     * Tests checking if an option with a value is set
     */
    public function testCheckingIfOptionWithValueIsSet(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertTrue($this->request->optionIsSet('foo'));
    }

    /**
     * Tests checking if an option without a value is set
     */
    public function testCheckingIfOptionWithoutValueIsSet(): void
    {
        $this->request->addOptionValue('foo', null);
        $this->assertTrue($this->request->optionIsSet('foo'));
    }

    /**
     * Tests getting all arguments
     */
    public function testGettingAllArguments(): void
    {
        $this->request->addArgumentValue('foo');
        $this->request->addArgumentValue('bar');
        $this->assertEquals(['foo', 'bar'], $this->request->getArgumentValues());
    }

    /**
     * Tests getting all options
     */
    public function testGettingAllOptions(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->request->addOptionValue('baz', 'blah');
        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $this->request->getOptionValues());
    }

    /**
     * Tests getting the command name
     */
    public function testGettingCommandName(): void
    {
        $this->request->setCommandName('foo');
        $this->assertEquals('foo', $this->request->getCommandName());
    }

    /**
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->request->getOptionValue('foo');
    }

    /**
     * Tests getting an option
     */
    public function testGettingOption(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertEquals('bar', $this->request->getOptionValue('foo'));
    }
}
