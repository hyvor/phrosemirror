<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document\Node;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\BlockquoteNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ParagraphNodeType;

beforeEach(function() {
    $this->node = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'test'
                    ]
                ]
            ],
            [
                'type' => 'blockquote',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'test 2'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
});

it('gets nested nodes', function() {

    $node = Node::fromJson(schema(), $this->node);

    $nodes = $node->getNodes();

    expect(count($nodes))->toBe(5);
    expect($nodes[0]->isOfType(ParagraphNodeType::class))->toBeTrue();
    expect($nodes[1]->text)->toBe('test');
    expect($nodes[2]->isOfType(BlockquoteNodeType::class))->toBeTrue();
    expect($nodes[3]->isOfType(ParagraphNodeType::class))->toBeTrue();
    expect($nodes[4]->text)->toBe('test 2');

});

it('gets nested nodes filtered', function() {

    $node = Node::fromJson(schema(), $this->node);

    expect($node->getNodes(ParagraphNodeType::class))->toHaveLength(2);
    expect($node->getNodes([ParagraphNodeType::class, BlockquoteNodeType::class]))->toHaveLength(3);

});

it('gets nodes without nesting', function() {

    $node = Node::fromJson(schema(), $this->node);

    expect($node->getNodes(ParagraphNodeType::class, false))->toHaveLength(1);

});