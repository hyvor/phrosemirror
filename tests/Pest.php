<?php

use Hyvor\Phrosemirror\Test\TestTypes\Marks\CodeMarkType;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\LinkMarkType;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\StrongMarkType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\BlockquoteNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\CodeBlock;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\DocNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\HeadingNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ImageNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ParagraphNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\TextNodeType;
use Hyvor\Phrosemirror\Types\MarkType;
use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Types\Schema;

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
