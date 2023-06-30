<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;
use DOMDocument;
use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Converters\HtmlParser\Whitespace;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ImageNodeAttrs;

it('parses nodes', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<p>Hello World</p>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello World',
                    ]
                ]
            ]
        ]
    ]);

});

it('parses complex node', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'image', tag: 'img', getAttrs: function ($domElement) {
            return ImageNodeAttrs::fromArray([
                'src' => $domElement->getAttribute('src'),
                'alt' => $domElement->getAttribute('alt'),
            ]);
        }),
        new ParserRule(node: 'blockquote', tag: 'blockquote'),
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $html = <<<HTML
    <blockquote><p>Hello World</p><p>Line 2</p><img src="image.png" alt="Image" /></blockquote>
    HTML;

    $document = $parser->parse($html);

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'blockquote',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Hello World',
                            ]
                        ]
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Line 2',
                            ]
                        ]
                    ],
                    [
                        'type' => 'image',
                        'attrs' => [
                            'src' => 'image.png',
                            'alt' => 'Image',
                        ],
                    ]
                ]
            ]
        ]
    ]);

});

it('parses code block without any issues with code mark', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(
            node: 'code_block',
            tag: 'pre',
            getChildren: function ($node) {
                /** @var DOMDocument $document */
                $document = $node->ownerDocument;
                $text = trim($node->textContent ?? '');
                return $document->createTextNode($text);
            },
            whitespace: Whitespace::PRESERVE
        ),
        new ParserRule(mark: 'code', tag: 'code'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $html = <<<HTML
    <pre>
<code>const a = 1;
</code>
</pre>
HTML;

    $document = $parser->parse($html);

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'code_block',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'const a = 1;',
                    ]
                ]
            ]
        ]
    ]);

});