<?php

namespace Hyvor\Phrosemirror\Test\Unit\Html\HtmlSerializer;

// test from tiptap-php
use Hyvor\Phrosemirror\Document\Document;

test('multiple marks get rendered correctly', function () {
    $document = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Example Text',
                        'marks' => [
                            [
                                'type' => 'strong',
                            ],
                            [
                                'type' => 'code',
                            ],
                            [
                                'type' => 'empty'
                            ]
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = Document::fromJson(schema(), $document)->toHtml();

    expect($result)->toEqual('<p><strong><code>Example Text</code></strong></p>');
});