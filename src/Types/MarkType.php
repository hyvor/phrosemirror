<?php

namespace Hyvor\Prosemirror\Types;

use Hyvor\Prosemirror\Document\Mark;

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

    public function fromHtml() : void {}

}