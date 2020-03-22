<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/aphiria/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Framework\Exceptions\Bootstrappers;

use Aphiria\Api\Errors\ProblemDetailsResponseMutator;
use Aphiria\Api\Validation\InvalidRequestBodyException;
use Aphiria\Api\Validation\ValidationProblemDetails;
use Aphiria\Application\IBootstrapper;
use Aphiria\Configuration\ConfigurationException;
use Aphiria\Configuration\GlobalConfiguration;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Exceptions\Console\ConsoleExceptionRenderer;
use Aphiria\Exceptions\GlobalExceptionHandler;
use Aphiria\Exceptions\Http\HttpExceptionRenderer;
use Aphiria\Exceptions\IExceptionRenderer;
use Aphiria\Exceptions\LogLevelRegistry;
use Aphiria\Net\Http\IResponseFactory;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Defines the global exception handler bootstrapper
 */
final class GlobalExceptionHandlerBootstrapper implements IBootstrapper
{
    /** @var IContainer The DI container */
    protected IContainer $container;

    /**
     * @param IContainer $container The DI container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException Thrown if the configuration was not valid
     */
    public function bootstrap(): void
    {
        if ($this->isRunningInConsole()) {
            $exceptionRenderer = $this->createAndBindConsoleExceptionRenderer();
        } else {
            $exceptionRenderer = $this->createAndBindHttpExceptionRenderer();
        }

        $logger = $this->createAndBindLogger();
        $globalExceptionHandler = new GlobalExceptionHandler(
            $exceptionRenderer,
            $logger,
            $this->createAndBindLogLevels()
        );
        $globalExceptionHandler->registerWithPhp();
        $this->container->bindInstance(GlobalExceptionHandler::class, $globalExceptionHandler);
    }

    /**
     * Creates and binds the exception renderer for console applications
     *
     * @return IExceptionRenderer The exception renderer for console applications
     */
    protected function createAndBindConsoleExceptionRenderer(): IExceptionRenderer
    {
        return new ConsoleExceptionRenderer();
    }

    /**
     * Creates and binds the exception renderer for HTTP applications
     *
     * @return IExceptionRenderer The exception renderer for HTTP applications
     * @throws ConfigurationException Thrown if configuration values were invalid or missing
     */
    protected function createAndBindHttpExceptionRenderer(): IExceptionRenderer
    {
        $useProblemDetails = GlobalConfiguration::getBool('aphiria.exceptions.useProblemDetails');
        $exceptionRenderer = new HttpExceptionRenderer($useProblemDetails);
        $exceptionRenderer->registerManyResponseFactories([
            HttpException::class => function (HttpException $ex, IHttpRequestMessage $request, IResponseFactory $responseFactory) {
                return $ex->getResponse();
            },
            InvalidRequestBodyException::class => function (InvalidRequestBodyException $ex, IHttpRequestMessage $request, IResponseFactory $responseFactory) use ($useProblemDetails) {
                if ($useProblemDetails) {
                    $body = new ValidationProblemDetails($ex->getErrors());
                    $response = $responseFactory->createResponse($request, HttpStatusCodes::HTTP_BAD_REQUEST, null, $body);

                    return (new ProblemDetailsResponseMutator)->mutateResponse($response);
                }

                return new Response(HttpStatusCodes::HTTP_BAD_REQUEST);
            }
        ]);
        $this->container->bindInstance([IExceptionRenderer::class, HttpExceptionRenderer::class], $exceptionRenderer);

        return $exceptionRenderer;
    }

    /**
     * Creates and binds a PSR-3 logger instance to use in the exception handler
     *
     * @return LoggerInterface The PSR-3 logger to use
     * @throws ConfigurationException Thrown if the configuration was invalid
     */
    protected function createAndBindLogger(): LoggerInterface
    {
        $logger = new Logger(GlobalConfiguration::getString('aphiria.logging.name'));

        foreach (GlobalConfiguration::getArray('aphiria.logging.handlers') as $handlerConfiguration) {
            switch ($handlerConfiguration['type']) {
                case StreamHandler::class:
                    $logger->pushHandler(new StreamHandler(
                        $handlerConfiguration['path'],
                        $handlerConfiguration['level']
                    ));
                    break;
                case SyslogHandler::class:
                    $logger->pushHandler(new SyslogHandler(
                        $handlerConfiguration['ident'] ?? 'app',
                        LOG_USER,
                        $handlerConfiguration['level']
                    ));
                    break;
                default:
                    throw new ConfigurationException("Unsupported logging handler type {$handlerConfiguration['type']}");
            }
        }

        $this->container->bindInstance(LoggerInterface::class, $logger);

        return $logger;
    }

    /**
     * Creates and binds an exception log level registry
     *
     * @return LogLevelRegistry The created exception log level registry
     */
    protected function createAndBindLogLevels(): LogLevelRegistry
    {
        $logLevels = new LogLevelRegistry();
        $this->container->bindInstance(LogLevelRegistry::class, $logLevels);

        return $logLevels;
    }

    /**
     * Gets whether or not the app is running in the console
     *
     * @return bool True if the application is running in the console, otherwise false
     */
    protected function isRunningInConsole(): bool
    {
        return \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg';
    }
}
