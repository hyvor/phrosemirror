<?php

namespace Hyvor\Phrosemirror\Types;

use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Document\Mark;

abstract class MarkType
{

    public string $name;

    /**
     * @var class-string<AttrsType>
     */
    public string $attrs = AttrsType::class;

    public function toHtml(Mark $mark, string $children) : string
    {
        return $children;
    }

    /**
     * @return ParserRule[]
     */
    public function fromHtml() : array
    {
        return [];
    }

}