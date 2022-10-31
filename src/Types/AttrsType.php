<?php

namespace Hyvor\Prosemirror\Types;

use Hyvor\Prosemirror\Exception\InvalidAttributeTypeException;
use ReflectionClass;
use TypeError;

class AttrsType
{

    /**
     * @param string $name
     * @return scalar
     */
    public function get(string $name)
    {

        $attr = $this->$name ?? null;
        
        // escape the Attribute
        if (is_string($attr)) {
            $attr = htmlspecialchars($attr);
        }
        
        return $attr;
    }

    /**
     * @param array<string, mixed> $attrsArray
     * @return $this
     */
    public function setFromArray(array $attrsArray) : self
    {

        $classReflection = new ReflectionClass($this);

        /**
         * Instead of looping through user-input JSON
         * We are looping through the attrs in the AttrType class
         * To make sure invalid data is not input
         * Type checking is done by PHP runtime
         */
        foreach ($classReflection->getProperties() as $attr) {

            if (!$attr->isPublic())
                continue;

            $name = $attr->getName();
            $default = $attr->getDefaultValue();
            $value = $attrsArray[$name] ?? $default;

            try {
                $this->$name = $value;
            } catch (TypeError) {

                throw new InvalidAttributeTypeException(
                    "Invalid type in the $name attribute (Given type: " .
                    gettype($value) .
                    ")"
                );
            }

        }

        return $this;

    }

}