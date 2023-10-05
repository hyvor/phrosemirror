<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlSerializer;

use Hyvor\Phrosemirror\Converters\HtmlSerializer\Context;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\BlockquoteNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\DocNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\TextNodeType;
use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Types\Schema;

class FromSchemaParagraph extends NodeType
{
    public string $name = 'paragraph';
    public function toHtmlFromContext(Context $context) : string
    {
        return "{$context->node->type->name}|{$context->topNode->type->name}|{$context->children}";
    }
}

it('works', function() {

    $doc = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Hello World']
                ]
            ]
        ]
    ];

    $node = Node::fromJson(
        new Schema([
            new DocNodeType,
            new FromSchemaParagraph,
            new TextNodeType()
        ], [])
    , $doc);
    expect($node->toHtml())->toEqual('paragraph|doc|Hello World');

});

it('with nested elements', function() {
    $doc = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'blockquote',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'Hello World']
                        ]
                    ]
                ]
            ]
        ]
    ];

    $node = Node::fromJson(
        new Schema([
            new DocNodeType,
            new FromSchemaParagraph,
            new TextNodeType(),
            new BlockquoteNodeType(),
        ], [])
        , $doc);
    expect($node->toHtml())->toEqual('<blockquote>paragraph|doc|Hello World</blockquote>');
});