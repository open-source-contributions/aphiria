<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/aphiria/blob/0.x/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Framework\Console\Binders;

use Aphiria\Application\Configuration\GlobalConfiguration;
use Aphiria\Application\Configuration\MissingConfigurationValueException;
use Aphiria\Console\Commands\Annotations\AnnotationCommandRegistrant;
use Aphiria\Console\Commands\Caching\FileCommandRegistryCache;
use Aphiria\Console\Commands\Caching\ICommandRegistryCache;
use Aphiria\Console\Commands\CommandRegistrantCollection;
use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * Defines the console command binder
 */
final class CommandBinder extends Binder
{
    /**
     * @inheritdoc
     * @throws MissingConfigurationValueException Thrown if the the config is missing values
     * @throws AnnotationException Thrown if PHP is not configured to handle scanning for annotations
     */
    public function bind(IContainer $container): void
    {
        $commands = new CommandRegistry();
        $container->bindInstance(CommandRegistry::class, $commands);
        $commandCache = new FileCommandRegistryCache(GlobalConfiguration::getString('aphiria.console.commandCachePath'));
        $container->bindInstance(ICommandRegistryCache::class, $commandCache);

        if (getenv('APP_ENV') === 'production') {
            $commandRegistrants = new CommandRegistrantCollection($commandCache);
        } else {
            $commandRegistrants = new CommandRegistrantCollection();
        }

        $container->bindInstance(CommandRegistrantCollection::class, $commandRegistrants);

        // Register some command annotation dependencies
        $commandAnnotationRegistrant = new AnnotationCommandRegistrant(
            GlobalConfiguration::getArray('aphiria.console.annotationPaths'),
            $container
        );
        $container->bindInstance(AnnotationCommandRegistrant::class, $commandAnnotationRegistrant);
    }
}
