<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Marks;

use Hyvor\Phrosemirror\Document\Mark;
use Hyvor\Phrosemirror\Types\MarkType;

class StrongMarkType extends MarkType
{

    public string $name = 'strong';

    public function toHtml(Mark $mark, string $children): string
    {
        return "<strong>$children</strong>";
    }

}