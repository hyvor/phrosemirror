<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;

use DOMNode;
use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;

it('customizes children when parsing', function() {

   $html = "<pre><code>var x = 0;</code></pre>";

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'text', tag: '#text'),
        new ParserRule(
            node: 'code_block',
            tag: 'pre',
            getChildren: function (DOMNode $node) : DOMNode {
                return $node->ownerDocument->createTextNode($node->textContent);
            },
        ),
        new ParserRule(mark: 'code', tag: 'code'),
    ]);

    $document = $parser->parse($html);

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'code_block',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'var x = 0;',
                    ]
                ]
            ]
        ]
    ]);

});