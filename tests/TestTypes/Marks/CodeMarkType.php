<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Marks;

use Hyvor\Prosemirror\Document\Mark;
use Hyvor\Prosemirror\Types\MarkType;

class CodeMarkType extends MarkType
{

    public function toHtml(Mark $mark, string $children): string
    {
        return "<code>$children</code>";
    }

}