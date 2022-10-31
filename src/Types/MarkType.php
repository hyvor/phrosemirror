<?php

namespace Hyvor\Phrosemirror\Types;

use Hyvor\Phrosemirror\Document\Mark;

abstract class MarkType
{

    /**
     * @var class-string<AttrsType>
     */
    public string $attrs = AttrsType::class;

    public function toHtml(Mark $mark, string $children) : string
    {
        return $children;
    }

}