<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document\Node;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\CodeMarkType;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\StrongMarkType;

test('has mark', function() {

    $node = [
        'type' => 'text',
        'marks' => [
            ['type' => 'strong']
        ],
    ];

    $node = Node::fromJson(schema(), $node);

    expect($node->hasMark(StrongMarkType::class))->toBeTrue();
    expect($node->hasMark(CodeMarkType::class))->toBeFalse();

});

test('has mark with nested', function() {

    $node = [
        'type' => 'paragraph',
        'content' => [
            [
                'type' => 'text',
                'marks' => [
                    ['type' => 'strong']
                ],
            ]
        ]
    ];

    $node = Node::fromJson(schema(), $node);
    expect($node->hasMark(StrongMarkType::class))->toBeFalse();
    expect($node->content->first()->hasMark(StrongMarkType::class))->toBeTrue();

});