<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/aphiria/blob/0.x/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Framework\Tests\ContentNegotiation\Binders;

use Aphiria\Application\Configuration\GlobalConfiguration;
use Aphiria\Application\Configuration\HashTableConfiguration;
use Aphiria\ContentNegotiation\AcceptCharsetEncodingMatcher;
use Aphiria\ContentNegotiation\AcceptLanguageMatcher;
use Aphiria\ContentNegotiation\ContentNegotiator;
use Aphiria\ContentNegotiation\IContentNegotiator;
use Aphiria\ContentNegotiation\IEncodingMatcher;
use Aphiria\ContentNegotiation\ILanguageMatcher;
use Aphiria\ContentNegotiation\IMediaTypeFormatterMatcher;
use Aphiria\ContentNegotiation\MediaTypeFormatterMatcher;
use Aphiria\ContentNegotiation\MediaTypeFormatters\JsonMediaTypeFormatter;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Framework\ContentNegotiation\Binders\ContentNegotiationBinder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContentNegotiationBinderTest extends TestCase
{
    /** @var IContainer|MockObject */
    private IContainer $container;
    private ContentNegotiationBinder $binder;

    protected function setUp(): void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->binder = new ContentNegotiationBinder();
        GlobalConfiguration::resetConfigurationSources();

        // Some universal assertions
        $this->container->expects($this->at(0))
            ->method('resolve')
            ->with(JsonMediaTypeFormatter::class)
            ->willReturn(new JsonMediaTypeFormatter());
        $this->container->expects($this->at(1))
            ->method('bindInstance')
            ->with(IMediaTypeFormatterMatcher::class, $this->isInstanceOf(MediaTypeFormatterMatcher::class));
    }

    public function testAcceptCharsetEncodingMatcherIsCreatedDirectly(): void
    {
        GlobalConfiguration::addConfigurationSource(new HashTableConfiguration(self::getBaseConfig()));
        $this->container->expects($this->at(2))
            ->method('bindInstance')
            ->with(IEncodingMatcher::class, $this->isInstanceOf(AcceptCharsetEncodingMatcher::class));
        $this->binder->bind($this->container);
    }

    public function testAcceptLanguageMatcherIsCreatedDirectly(): void
    {
        GlobalConfiguration::addConfigurationSource(new HashTableConfiguration(self::getBaseConfig()));
        $this->container->expects($this->at(3))
            ->method('bindInstance')
            ->with(ILanguageMatcher::class, $this->isInstanceOf(AcceptLanguageMatcher::class));
        $this->binder->bind($this->container);
    }

    public function testContentNegotiatorIsBound(): void
    {
        GlobalConfiguration::addConfigurationSource(new HashTableConfiguration(self::getBaseConfig()));
        $this->container->expects($this->at(4))
            ->method('bindInstance')
            ->with(IContentNegotiator::class, $this->isInstanceOf(ContentNegotiator::class));
        $this->binder->bind($this->container);
    }

    public function testUnknownEncodingMatchersAreResolved(): void
    {
        $encodingMatcher = $this->createMock(IEncodingMatcher::class);
        $config = self::getBaseConfig();
        $config['aphiria']['contentNegotiation']['encodingMatcher'] = \get_class($encodingMatcher);
        GlobalConfiguration::addConfigurationSource(new HashTableConfiguration($config));
        $this->container->expects($this->at(2))
            ->method('resolve')
            ->with(\get_class($encodingMatcher))
            ->willReturn($encodingMatcher);
        $this->container->expects($this->at(3))
            ->method('bindInstance')
            ->with(IEncodingMatcher::class, $encodingMatcher);
        $this->binder->bind($this->container);
    }

    public function testUnknownLanguageMatchersAreResolved(): void
    {
        $languageMatcher = $this->createMock(ILanguageMatcher::class);
        $config = self::getBaseConfig();
        $config['aphiria']['contentNegotiation']['languageMatcher'] = \get_class($languageMatcher);
        GlobalConfiguration::addConfigurationSource(new HashTableConfiguration($config));
        $this->container->expects($this->at(3))
            ->method('resolve')
            ->with(\get_class($languageMatcher))
            ->willReturn($languageMatcher);
        $this->container->expects($this->at(4))
            ->method('bindInstance')
            ->with(ILanguageMatcher::class, $languageMatcher);
        $this->binder->bind($this->container);
    }

    /**
     * Gets the base config
     *
     * @return array The base config
     */
    private static function getBaseConfig(): array
    {
        return [
            'aphiria' => [
                'contentNegotiation' => [
                    'encodingMatcher' => AcceptCharsetEncodingMatcher::class,
                    'languageMatcher' => AcceptLanguageMatcher::class,
                    'mediaTypeFormatters' => [
                        JsonMediaTypeFormatter::class
                    ],
                    'supportedLanguages' => ['en']
                ]
            ]
        ];
    }
}
