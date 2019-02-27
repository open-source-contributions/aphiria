<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (c) 2019 David Young
 * @license   https://github.com/aphiria/api/blob/master/LICENSE.md
 */

namespace Aphiria\Api\Tests\Exceptions;

use Aphiria\Api\Exceptions\ExceptionResponseFactory;
use Aphiria\Api\Exceptions\ExceptionResponseFactoryRegistry;
use Aphiria\Net\Http\ContentNegotiation\INegotiatedResponseFactory;
use Aphiria\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\IHttpResponseMessage;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the exception response factory
 */
class ExceptionResponseFactoryTest extends TestCase
{
    /** @var ExceptionResponseFactory The response factory to use in tests */
    private $factory;
    /** @var NegotiatedResponseFactory|MockObject The negotiated response factory to use */
    private $negotiatedResponseFactory;
    /** @var ExceptionResponseFactoryRegistry The registry to use in tests */
    private $responseFactories;

    protected function setUp(): void
    {
        $this->negotiatedResponseFactory = $this->createMock(INegotiatedResponseFactory::class);
        $this->responseFactories = new ExceptionResponseFactoryRegistry();
        $this->factory = new ExceptionResponseFactory($this->negotiatedResponseFactory, $this->responseFactories);
    }

    public function testCreatingResponseForExceptionWithNoRequestSetUsesDefaultResponse(): void
    {
        $response = $this->factory->createResponseFromException(new InvalidArgumentException, null);
        $this->assertEquals(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaders()->getFirst('Content-Type'));
    }

    public function testCreatingResponseForExceptionWithRequestAndNoResponseFactoryCreates500Response(): void
    {
        /** @var IHttpRequestMessage|MockObject $expectedRequest */
        $expectedRequest = $this->createMock(IHttpRequestMessage::class);
        $expectedResponse = $this->createMock(IHttpResponseMessage::class);
        $this->negotiatedResponseFactory->expects($this->once())
            ->method('createResponse')
            ->with($expectedRequest, HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR, null, null)
            ->willReturn($expectedResponse);
        $actualResponse = $this->factory->createResponseFromException(new InvalidArgumentException, $expectedRequest);
        $this->assertSame($expectedResponse, $actualResponse);
    }

    public function testCreatingResponseForExceptionWithRequestAndResponseFactoryCreatesResponseFromFactory(): void
    {
        /** @var IHttpRequestMessage|MockObject $expectedRequest */
        $expectedRequest = $this->createMock(IHttpRequestMessage::class);
        /** @var IHttpResponseMessage|MockObject $expectedRequest */
        $expectedResponse = $this->createMock(IHttpResponseMessage::class);
        $this->responseFactories->registerFactory(
            InvalidArgumentException::class,
            function (
                InvalidArgumentException $ex,
                IHttpRequestMessage $request
            ) use ($expectedRequest, $expectedResponse) {
                $this->assertEquals($expectedRequest, $request);

                return $expectedResponse;
            }
        );
        $response = $this->factory->createResponseFromException(new InvalidArgumentException, $expectedRequest);
        $this->assertSame($expectedResponse, $response);
    }

    public function testCreatingResponseForExceptionWithRequestAndResponseFactoryThatThrowsCreatesDefaultResponse(
    ): void {
        /** @var IHttpRequestMessage|MockObject $expectedRequest */
        $expectedRequest = $this->createMock(IHttpRequestMessage::class);
        $this->responseFactories->registerFactory(
            InvalidArgumentException::class,
            function (InvalidArgumentException $ex, IHttpRequestMessage $request) {
                throw new Exception();
            }
        );
        $response = $this->factory->createResponseFromException(new InvalidArgumentException, $expectedRequest);
        $this->assertEquals(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaders()->getFirst('Content-Type'));
    }

    public function testCreatingResponseForHttpExceptionsUseBuiltInResponseFactory(): void
    {
        // Purposely don't use a registry
        $factory = new ExceptionResponseFactory($this->negotiatedResponseFactory);
        /** @var IHttpRequestMessage|MockObject $expectedRequest */
        $expectedRequest = $this->createMock(IHttpRequestMessage::class);
        /** @var IHttpResponseMessage|MockObject $expectedResponse */
        $expectedResponse = $this->createMock(IHttpResponseMessage::class);
        $response = $factory->createResponseFromException(new HttpException($expectedResponse), $expectedRequest);
        $this->assertSame($expectedResponse, $response);
    }
}
