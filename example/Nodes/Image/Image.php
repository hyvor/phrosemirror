<?php

namespace Hyvor\Phrosemirror\Example\Nodes\Image;

use DOMElement;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

class Image extends NodeType
{

    public string $name = 'image';
    public string $attrs = ImageAttrs::class;

    public function toHtml(Node $node, string $children): string
    {
        $src = strval($node->attr('src'));
        $alt = strval($node->attr('alt'));
        $title = strval($node->attr('title'));

        return "<img src=\"$src\" alt=\"$alt\" title=\"$title\">";
    }

    public function fromHtml(): array
    {
        return [
            new ParserRule(
                tag: 'img',
                getAttrs: function (DOMElement $node) {
                    $src = $node->getAttribute('src');
                    if (!$src) return false;

                    $data = [
                        'src' => $src
                    ];

                    $alt = $node->getAttribute('alt');
                    $title = $node->getAttribute('title');

                    if ($alt) $data['alt'] = $alt;
                    if ($title) $data['width'] = $title;

                    return ImageAttrs::fromArray($data);
                },
            )
        ];
    }

}