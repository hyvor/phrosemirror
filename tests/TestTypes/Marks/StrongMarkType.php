<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Marks;

use Hyvor\Prosemirror\Document\Mark;
use Hyvor\Prosemirror\Types\MarkType;

class StrongMarkType extends MarkType
{

    public function toHtml(Mark $mark, string $children): string
    {
        return "<strong>$children</strong>";
    }

}