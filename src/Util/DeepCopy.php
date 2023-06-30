<?php

namespace Hyvor\Phrosemirror\Util;

class DeepCopy
{

    /**
     * @template T of object
     * @param T $obj
     * @return T
     */
    public static function copy(object $obj) : object
    {
        $copier = new \DeepCopy\DeepCopy();
        return $copier->copy($obj); // @phpstan-ignore-line
    }

}