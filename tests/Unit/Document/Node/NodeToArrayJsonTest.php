<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document\Node;

use Hyvor\Phrosemirror\Document\Node;

it('convert a node to array', function() {

    $nodeArray = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'test'
                    ],
                    [
                        'type' => 'text',
                        'text' => 'with marks',
                        'marks' => [
                            [
                                'type' => 'strong',
                            ],
                            [
                                'type' => 'link',
                                'attrs' => [
                                    'href' => 'hyvor.com'
                                ]
                            ]
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
                                'text' => 'test 2'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'image',
                'attrs' => [
                    'src' => 'image.png',
                    'alt' => null
                ]
            ]
        ]
    ];

    $node = Node::fromJson(schema(), $nodeArray);

    expect($node->toArray())->toBe($nodeArray);
    expect($node->toJson())->toBe(json_encode($nodeArray));


});