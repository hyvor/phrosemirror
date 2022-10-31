<?php

namespace Hyvor\Prosemirror\Test\Unit\Html\HtmlSerializer;

use Hyvor\Prosemirror\Document\Document;

it('renders a code block', function() {

    $doc = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'code_block',
                'attrs' => [
                    'language' => 'php'
                ],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => "Line 1\nLine2\nLine3"
                    ]
                ]
            ]
        ]
    ];

    $html = Document::fromJson(schema(), $doc)->toHtml();

    expect($html)->toBe('<pre class="language-php"><code>' . "Line 1\nLine2\nLine3" . '</code></pre>');

});