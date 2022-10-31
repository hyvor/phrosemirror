<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Marks;

use Hyvor\Prosemirror\Document\Mark;
use Hyvor\Prosemirror\Types\MarkType;

class LinkMarkType extends MarkType
{

    public string $attrs = LinkMarkAttrs::class;

    public function toHtml(Mark $mark, string $children): string
    {
        $href = $mark->attr('href');

        return "<a href=\"$href\">$children</a>";
    }

}