<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/route-matcher/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Matchers\Requests;

/**
 * Tests the request header parser
 */
class RequestHeaderParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var array The $_SERVER super global to use */
    private static $serverArray = [
        'NON_HEADER' => 'foo',
        'CONTENT_LENGTH' => 4,
        'CONTENT_TYPE' => 'foo',
        'HTTP_ACCEPT' => 'accept',
        'HTTP_ACCEPT_CHARSET' => 'accept_charset',
        'HTTP_ACCEPT_ENCODING' => 'accept_encoding',
        'HTTP_ACCEPT_LANGUAGE' => 'accept_language',
        'HTTP_CONNECTION' => 'connection',
        'HTTP_HOST' => 'host',
        'HTTP_REFERER' => 'referer',
        'HTTP_USER_AGENT' => 'user_agent'
    ];
    /** @var RequestHeaderParser The header parse to use in tests */
    private $headerParser = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->headerParser = new RequestHeaderParser();
    }

    /**
     * Tests parsing raw header values returns correct values
     */
    public function testParsingRawHeaderValuesReturnsCorrectValues()
    {
        $expectedHeaders = [];

        foreach (self::$serverArray as $key => $value) {
            if (strpos(strtoupper($key), 'HTTP_') === 0) {
                if (!is_array($value)) {
                    $value = [$value];
                }

                $expectedHeaders[$this->normalizeName($key)] = $value;
            } elseif (strpos(strtoupper($key), 'CONTENT_') === 0) {
                if (!is_array($value)) {
                    $value = [$value];
                }

                $expectedHeaders[$this->normalizeName($key)] = $value;
            }
        }

        $this->assertEquals($expectedHeaders, $this->headerParser->parseHeaders(self::$serverArray));
    }

    /**
     * Normalizes a name
     *
     * @param string $name The name to normalize
     * @return string The normalized name
     */
    private function normalizeName($name)
    {
        $dashedName = strtr($name, '_', '-');

        if (strpos(strtoupper($dashedName), 'HTTP-') === 0) {
            $dashedName = substr($dashedName, 5);
        }

        return $dashedName;
    }
}
