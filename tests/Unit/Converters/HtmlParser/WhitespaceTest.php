<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;

use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Converters\HtmlParser\Whitespace;

it('collapses whitespace by default', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse("<p>Hello\n\n\nWorld</p>");

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

it('normalizes whitespace', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p', whitespace: Whitespace::NORMALIZE),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse("<p>Hello\nWor  ld</p>");

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello Wor  ld',
                    ]
                ]
            ]
        ]
    ]);

});

it('preserves whitespace', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p', whitespace: Whitespace::PRESERVE),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse("<p>\nHello\n\nWorld\n</p>");

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => "\nHello\n\nWorld\n",
                    ]
                ]
            ]
        ]
    ]);

});