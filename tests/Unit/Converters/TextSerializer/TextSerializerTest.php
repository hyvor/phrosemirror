<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\TextSerializer;

use Hyvor\Phrosemirror\Document\Document;

it('serializes a document to text', function() {

    $json = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Welcome '
                    ],
                    [
                        'type' => 'text',
                        'text' => 'to Hyvor.'
                    ]
                ]
            ],
            [
                'type' => 'image',
                'attrs' => ['src' => 'image.png']
            ],
            [
                'type' => 'blockquote',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'hi!'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'End.'
                    ]
                ]
            ]
        ]
    ];

    $text = Document::fromJson(schema(), $json)->toText();

    expect($text)->toBe("Welcome to Hyvor.\n\nhi!\n\nEnd.");

});