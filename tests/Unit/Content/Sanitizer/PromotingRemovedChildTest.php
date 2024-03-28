<?php

namespace Hyvor\Phrosemirror\Test\Unit\Content\Sanitizer;
use Hyvor\Phrosemirror\Content\Sanitizer\Sanitizer;
use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Types\NodeType;

it('success', function() {

    $schema =  SanitizerHelper::getSchema();

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'blockquote',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => 'hello']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
            ],
            [
                'type' => 'blockquote',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'hello']
                        ]
                    ]
                ]
            ]
        ]
    ]);

});


it('ignores when the parent does not accept it', function() {

    $schema = SanitizerHelper::getSchema(new class extends NodeType {
        public string $name = 'doc';
        public ?string $content = 'paragraph figure';
    });

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [['type' => 'text', 'text' => 'hello']]
            ],
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'blockquote',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => 'hello']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [['type' => 'text', 'text' => 'hello']]
            ],
            [
                'type' => 'figure',
            ]
        ]
    ]);

});


it('promotes to parent if removed - when there are other elements after', function() {

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'blockquote',
                    ]
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'world']
                ]
            ]
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
            ],
            [
                'type' => 'blockquote',
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'world']
                ]
            ]
        ]
    ]);

});

it('when there are multiple blockquotes', function() {

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'blockquote',
                    ]
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'hello']
                ]
            ],
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'blockquote',
                    ]
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'world']
                ]
            ]
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            ['type' => 'figure',],
            ['type' => 'blockquote',],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'figure',],
            ['type' => 'blockquote',],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'world']]]
        ]
    ]);

});

