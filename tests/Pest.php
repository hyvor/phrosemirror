<?php

use Hyvor\Phrosemirror\Test\TestTypes\Marks\CodeMarkType;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\EmptyMarkType;
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
                new DocNodeType,
                new TextNodeType,
                new ParagraphNodeType,
                new ImageNodeType,
                new BlockquoteNodeType,
                new HeadingNodeType,
                new CodeBlock,
            ],
            $nodes
        ),
        array_merge(
            [
                new StrongMarkType,
                new LinkMarkType,
                new CodeMarkType,
                new EmptyMarkType
            ],
            $marks
        )
    );
}
