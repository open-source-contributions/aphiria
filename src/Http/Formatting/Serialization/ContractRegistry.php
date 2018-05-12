<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Net\Http\Formatting\Serialization;

use Closure;
use OutOfBoundsException;

/**
 * Defines a contract registry
 */
class ContractRegistry
{
    /** @var ObjectContract[] The mapping of types to contracts */
    private $contractsByType = [];

    /**
     * Gets the contract for a type
     *
     * @param string $type The type whose contract we want
     * @return ObjectContract The contract for the input type
     * @throws OutOfBoundsException Thrown if the type does not have a contract
     */
    public function getContractForType(string $type): ObjectContract
    {
        $normalizedType = $this->normalizeType($type);

        if (!$this->hasContractForType($type)) {
            // Use the input type name to make the exception message more meaningful
            throw new OutOfBoundsException("No contract registered for type \"$type\"");
        }

        return $this->contractsByType[$normalizedType];
    }

    /**
     * Gets the contract for a value
     *
     * @param mixed $value The value whose contract we want
     * @return ObjectContract The contract for the input value
     * @throws OutOfBoundsException Thrown if the value does not have a contract
     */
    public function getContractForValue($value): ObjectContract
    {
        // Note: The type is normalized in getContractForType()
        return $this->getContractForType(TypeResolver::resolveType($value));
    }

    /**
     * Gets whether or not the registry has a contract for a type
     *
     * @param string $type The type to check for
     * @return bool True if the registry has a contract for the input type, otherwise false
     */
    public function hasContractForType(string $type): bool
    {
        return isset($this->contractsByType[$type]);
    }

    /**
     * Registers a contract
     *
     * @param ObjectContract $contract The contract to register
     */
    public function registerContract(ObjectContract $contract): void
    {
        $normalizedType = $this->normalizeType($contract->getType());
        $this->contractsByType[$normalizedType] = $contract;
    }

    /**
     * Registers a dictionary object contract
     *
     * @param string $type The type of object this contract represents
     * @param Closure $objectFactory The factory that instantiates an object from a value
     * @param Property[] $properties,... The list of properties that make up the object
     */
    public function registerDictionaryObjectContract(
        string $type,
        Closure $objectFactory,
        Property ...$properties
    ): void {
        $this->registerContract(new DictionaryObjectContract($type, $this, $objectFactory, ...$properties));
    }

    /**
     * Registers a value object contract
     *
     * @param string $type The type of object this contract represents
     * @param Closure $objectFactory The factory that instantiates an object from a value
     * @param Closure $encodingFactory The factory that encodes an instance of an object this contract represents
     */
    public function registerValueObjectContract(string $type, Closure $objectFactory, Closure $encodingFactory): void
    {
        $this->registerContract(new ValueObjectContract($type, $objectFactory, $encodingFactory));
    }

    /**
     * Normalizes a type, eg "integer" to "int", for usage as keys in the registry
     *
     * @param string $type The type to normalize
     * @return string The normalized type
     */
    private function normalizeType(string $type): string
    {
        switch ($type) {
            case 'boolean':
            case 'bool':
                return 'bool';
            case 'float':
            case 'double':
                return 'float';
            case 'int':
            case 'integer':
                return 'int';
            default:
                return $type;
        }
    }
}
