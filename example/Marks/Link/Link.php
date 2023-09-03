<?php

namespace Hyvor\Phrosemirror\Example\Marks\Link;

use DOMElement;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Document\Mark;
use Hyvor\Phrosemirror\Types\MarkType;

class Link extends MarkType
{

    public string $name = 'link';
    public string $attrs = LinkAttrs::class;

    public function toHtml(Mark $mark, string $children): string
    {

        /** @var string $href */
        $href = $mark->attr('href');
        /** @var string $title */
        $title = $mark->attr('title') ?? '';

        return "<a href=\"$href\" title=\"$title\">$children</a>";

    }

    public function fromHtml(): array
    {

        return [
            new ParserRule(
                tag: 'a',
                getAttrs: function (DOMElement $node) : LinkAttrs | bool {
                    $href = $node->getAttribute('href');
                    if (!$href)
                        return false;
                    return LinkAttrs::fromArray([
                        'href' => $href,
                        'title' => $node->getAttribute('title') ?: null
                    ]);
                }
            ),
        ];

    }

}