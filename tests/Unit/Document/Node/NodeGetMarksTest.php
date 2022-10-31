<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document\Node;


use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\LinkMarkType;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\StrongMarkType;

beforeEach(function() {
    $this->node = [
        'type' => 'doc',
        'marks' => [ // usually, prosemirror only have marks in TextNodes but just for testing non-nesting
            ['type' => 'strong']
        ],
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'test',
                        'marks' => [
                            ['type' => 'strong']
                        ]
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
                                'text' => 'test 2',
                                'marks' => [
                                    [
                                        'type' => 'link',
                                        'attrs' => ['href' => 'https://hyvor.com']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
});

it('gets nested marks', function() {

    $node = Node::fromJson(schema(), $this->node);

    $marks = $node->getMarks();

    expect($marks)->toHaveLength(3);
    expect($marks[0]->isOfType(StrongMarkType::class))->toBeTrue();
    expect($marks[1]->isOfType(StrongMarkType::class))->toBeTrue();
    expect($marks[2]->isOfType(LinkMarkType::class))->toBeTrue();

});

it('gets marks with filtering nesting', function() {

    $node = Node::fromJson(schema(), $this->node);

    expect($node->getMarks(StrongMarkType::class))->toHaveLength(2);
    expect($node->getMarks(LinkMarkType::class))->toHaveLength(1);
    expect($node->getMarks([StrongMarkType::class, LinkMarkType::class]))->toHaveLength(3);

});

it('gets marks without nesting', function() {

    $node = Node::fromJson(schema(), $this->node);

    expect($node->getMarks(null, false))->toHaveLength(1);

});