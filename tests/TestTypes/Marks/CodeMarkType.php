<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Marks;

use Hyvor\Phrosemirror\Document\Mark;
use Hyvor\Phrosemirror\Types\MarkType;

class CodeMarkType extends MarkType
{

    public function toHtml(Mark $mark, string $children): string
    {
        return "<code>$children</code>";
    }

}