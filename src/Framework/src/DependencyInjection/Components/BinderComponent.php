<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/aphiria/blob/0.x/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Framework\DependencyInjection\Components;

use Aphiria\Application\IComponent;
use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\Binders\IBinderDispatcher;
use Aphiria\DependencyInjection\IContainer;
use RuntimeException;

/**
 * Defines the binder component
 */
class BinderComponent implements IComponent
{
    /** @var IBinderDispatcher|null The binder dispatcher */
    private ?IBinderDispatcher $binderDispatcher = null;
    /** @var IContainer The container to dispatch binders with */
    private IContainer $container;
    /** @var Binder[] The list of binders to dispatch */
    private array $binders = [];

    /**
     * @param IContainer $container The container to dispatch binders with
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the binder dispatcher was not set
     */
    public function build(): void
    {
        if ($this->binderDispatcher === null) {
            throw new RuntimeException('Must call withBinderDispatcher() before building');
        }

        $this->binderDispatcher->dispatch($this->binders, $this->container);
    }

    /**
     * Adds a binder dispatcher to use
     *
     * @param IBinderDispatcher $binderDispatcher The binder dispatcher to use
     * @return self For chaining
     */
    public function withBinderDispatcher(IBinderDispatcher $binderDispatcher): self
    {
        $this->binderDispatcher = $binderDispatcher;
        $this->container->bindInstance(IBinderDispatcher::class, $this->binderDispatcher);

        return $this;
    }

    /**
     * Adds binders to dispatch
     *
     * @param Binder|Binder[] $binders The binders to add
     * @return self For chaining
     */
    public function withBinders($binders): self
    {
        if ($binders instanceof Binder) {
            $this->binders[] = $binders;
        } elseif (\is_array($binders)) {
            $this->binders = [...$this->binders, ...$binders];
        }

        return $this;
    }
}
