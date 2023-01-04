<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;

use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;

it('keeps content of unknown tags', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<p>Hello <unknown>World</unknown></p>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello ',
                    ],
                    [
                        'type' => 'text',
                        'text' => 'World',
                    ]
                ]
            ]
        ]
    ]);

});

it('keeps tags inside unknown tags', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
        new ParserRule(mark: 'strong', tag: 'b'),
    ]);

    $document = $parser->parse('<p>Hello <unknown><b>World</b></unknown></p>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello ',
                    ],
                    [
                        'type' => 'text',
                        'text' => 'World',
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