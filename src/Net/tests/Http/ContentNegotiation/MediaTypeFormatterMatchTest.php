<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/aphiria/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Net\Tests\Http\ContentNegotiation;

use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatterMatch;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\IMediaTypeFormatter;
use Aphiria\Net\Http\Headers\ContentTypeHeaderValue;
use PHPUnit\Framework\TestCase;

class MediaTypeFormatterMatchTest extends TestCase
{
    public function testGettingFormatterReturnsSameOneInConstructor(): void
    {
        /** @var IMediaTypeFormatter $formatter */
        $formatter = $this->createMock(IMediaTypeFormatter::class);
        $mediaTypeHeaderValue = new ContentTypeHeaderValue('foo/bar');
        $match = new MediaTypeFormatterMatch($formatter, 'baz/blah', $mediaTypeHeaderValue);
        $this->assertSame($formatter, $match->getFormatter());
    }

    public function testGettingMediaTypeReturnsSameOneInConstructor(): void
    {
        /** @var IMediaTypeFormatter $formatter */
        $formatter = $this->createMock(IMediaTypeFormatter::class);
        $mediaTypeHeaderValue = new ContentTypeHeaderValue('foo/bar');
        $match = new MediaTypeFormatterMatch($formatter, 'baz/blah', $mediaTypeHeaderValue);
        $this->assertEquals('baz/blah', $match->getMediaType());
    }

    public function testGettingMediaTypeHeaderReturnsSameOneInConstructor(): void
    {
        /** @var IMediaTypeFormatter $formatter */
        $formatter = $this->createMock(IMediaTypeFormatter::class);
        $mediaTypeHeaderValue = new ContentTypeHeaderValue('foo/bar');
        $match = new MediaTypeFormatterMatch($formatter, 'baz/blah', $mediaTypeHeaderValue);
        $this->assertSame($mediaTypeHeaderValue, $match->getMediaTypeHeaderValue());
    }
}
