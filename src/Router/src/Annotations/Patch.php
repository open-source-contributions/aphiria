<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/aphiria/blob/0.x/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Routing\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Defines the PATCH route annotation
 * @Annotation
 * @Target({"METHOD"})
 */
final class Patch extends Route
{
    /**
     * @param array $values The mapping of value names to values
     */
    public function __construct(array $values)
    {
        $values['httpMethods'] = ['PATCH'];
        parent::__construct($values);
    }
}
