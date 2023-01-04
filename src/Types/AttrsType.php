<?php

namespace Hyvor\Phrosemirror\Types;

use Hyvor\Phrosemirror\Exception\InvalidAttributeTypeException;
use ReflectionClass;
use TypeError;

class AttrsType
{

    final public function __construct() {}

    /**
     * @param string $name
     * @return scalar
     */
    public function get(string $name, bool $escape = true)
    {

        $attr = $this->$name ?? null;
        
        // escape the Attribute
        if (is_string($attr) && $escape) {
            $attr = htmlspecialchars($attr);
        }
        
        return $attr;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray() : array
    {
        return get_object_vars($this);
    }

    /**
     * @param array<string, mixed> $attrs
     */
    public static function fromArray(array $attrs) : static
    {

        $obj = new static;
        $obj->setFromArray($attrs);
        return $obj;

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