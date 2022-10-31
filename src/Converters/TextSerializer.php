<?php

namespace Hyvor\Phrosemirror\Converters;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Document\TextNode;

class TextSerializer
{

    protected const BLOCK_SEPARATOR = "\n\n";

    public function serialize(Node $node) : string
    {

        $text = '';
        $started = false;
        $shouldSeparate = false;

        $node->traverse(function(Node $node) use (&$text, &$started, &$shouldSeparate) {

            if ($node->type->isText && $node instanceof TextNode) {
                if ($shouldSeparate && $started) {
                    $text .= self::BLOCK_SEPARATOR;
                }
                $text .= $node->text;
                $shouldSeparate = false;
                $started = true;
            } else {
                $shouldSeparate = true;
            }

        });

        return $text;

    }

}