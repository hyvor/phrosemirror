<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Marks;

use Hyvor\Phrosemirror\Document\Mark;
use Hyvor\Phrosemirror\Types\MarkType;

class LinkMarkType extends MarkType
{

    public string $attrs = LinkMarkAttrs::class;

    public function toHtml(Mark $mark, string $children): string
    {
        $href = $mark->attr('href');

        return "<a href=\"$href\">$children</a>";
    }

}