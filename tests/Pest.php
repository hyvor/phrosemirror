<?php

use Hyvor\Prosemirror\Test\TestTypes\Marks\CodeMarkType;
use Hyvor\Prosemirror\Test\TestTypes\Marks\LinkMarkType;
use Hyvor\Prosemirror\Test\TestTypes\Marks\StrongMarkType;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\BlockquoteNodeType;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\CodeBlock;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\DocNodeType;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\HeadingNodeType;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\ImageNodeType;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\ParagraphNodeType;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\TextNodeType;
use Hyvor\Prosemirror\Types\MarkType;
use Hyvor\Prosemirror\Types\NodeType;
use Hyvor\Prosemirror\Types\Schema;

/**
 * @param NodeType[] $nodes
 * @param MarkType[] $marks
 * @return Schema
 */
function schema(array $nodes = [], array $marks = []) : Schema {
    return new Schema(
        array_merge(
            [
                'doc' => new DocNodeType,
                'text' => new TextNodeType,
                'paragraph' => new ParagraphNodeType,
                'image' => new ImageNodeType,
                'blockquote' => new BlockquoteNodeType,
                'heading' => new HeadingNodeType,
                'code_block' => new CodeBlock,
            ],
            $nodes
        ),
        array_merge(
            [
                'strong' => new StrongMarkType,
                'link' => new LinkMarkType,
                'code' => new CodeMarkType
            ],
            $marks
        )
    );
}
