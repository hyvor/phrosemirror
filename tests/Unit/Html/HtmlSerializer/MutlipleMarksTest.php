<?php

namespace Hyvor\Prosemirror\Test\Unit\Html\HtmlSerializer;

// test from tiptap-php
use Hyvor\Prosemirror\Document\Document;

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
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = Document::fromJson(schema(), $document)->toHtml();

    expect($result)->toEqual('<p><strong><code>Example Text</code></strong></p>');
});