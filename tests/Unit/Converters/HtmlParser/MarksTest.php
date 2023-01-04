<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;

use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;

it('parses marks', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(mark: 'strong', tag: 'strong'),
        new ParserRule(mark: 'code', tag: 'code'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<p><strong>Hello</strong> <code>World</code></p>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello',
                        'marks' => [
                            [
                                'type' => 'strong',
                            ]
                        ]
                    ],
                    [
                        'type' => 'text',
                        'text' => ' ',
                    ],
                    [
                        'type' => 'text',
                        'text' => 'World',
                        'marks' => [
                            [
                                'type' => 'code',
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);

});

it('applies marks to text instead of node', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(mark: 'strong', tag: 'strong'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<strong><p>Hello World</p></strong>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello World',
                        'marks' => [
                            [
                                'type' => 'strong',
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);

});

it('parses nested marks', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(mark: 'strong', tag: 'strong'),
        new ParserRule(mark: 'code', tag: 'code'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<p><strong><code>Hello World</code></strong></p>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello World',
                        'marks' => [
                            [
                                'type' => 'strong',
                            ],
                            [
                                'type' => 'code',
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);

});