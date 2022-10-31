<?php
namespace Hyvor\Phrosemirror\Html;

use Hyvor\Phrosemirror\Document\Mark;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Document\TextNode;


class HtmlSerializer
{

    public function __construct() {}

    public function node(Node $node) : string
    {

        $nodeType = $node->type;


        if ($nodeType->isText && $node instanceof TextNode) {
            $content = htmlspecialchars($node->text);
        } else {

            $childContent = '';

            foreach ($node->content->all() as $child) {
                $childContent .= $this->node($child);
            }

            $content = $nodeType->toHtml($node, $childContent);

        }

        foreach (array_reverse($node->marks) as $mark) {
            $content = $this->mark($mark, $content);
        }

        return $content;

    }

    public function mark(Mark $mark, string $children = '') : string
    {
        return $mark->type->toHtml($mark, $children);
    }

}