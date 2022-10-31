<?php

namespace Hyvor\Prosemirror\Test\Unit\Html\HtmlSerializer;

use Hyvor\Prosemirror\Document\Document;

it('xss in text', function() {

    $document = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'text',
                'text' => '<script>alert(1)</script>',
            ],
        ],
    ];

    $result = Document::fromJson(schema(), $document)->toHtml();

    expect($result)->toEqual('&lt;script&gt;alert(1)&lt;/script&gt;');

});

it('xss in attrs', function() {

    $document = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'image',
                'attrs' => [
                    'src' => '<script>alert(1)</script>'
                ]
            ],
        ],
    ];


    $result = Document::fromJson(schema(), $document)->toHtml();
    expect($result)->toContain('src="&lt;script&gt;alert(1)&lt;/script&gt;"');

});