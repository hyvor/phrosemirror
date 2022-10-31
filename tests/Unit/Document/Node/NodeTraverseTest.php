<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document\Node;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\BlockquoteNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\DocNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ParagraphNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\TextNodeType;

it('traverse through nodes', function() {

    $node = [
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

    $node = Node::fromJson(schema(), $node);

    $i = 0;
    $node->traverse(function (Node $node) use (&$i) {

        if ($i === 0) {
            expect($node->isOfType(DocNodeType::class))->toBeTrue();
            expect($node->content->count())->toBe(2);
        } else if ($i === 1) {
            expect($node->isOfType(ParagraphNodeType::class))->toBeTrue();
            expect($node->allText())->toBe('test');
        } else if ($i === 2) {
            expect($node->isOfType(TextNodeType::class))->toBeTrue();
            expect($node->text)->toBe('test');
        } else if ($i === 3) {
            expect($node->isOfType(BlockquoteNodeType::class))->toBeTrue();
        } else if ($i === 4) {
            expect($node->isOfType(ParagraphNodeType::class))->toBeTrue();
        }

        $i++;

    });

});