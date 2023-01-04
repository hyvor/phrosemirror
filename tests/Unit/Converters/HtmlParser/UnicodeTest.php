<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;

use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;

it('parses emojis and multi-byte chars correcrly', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<p>👋Ϊ</p>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => '👋Ϊ',
                    ]
                ]
            ]
        ]
    ]);

});

it('parses HTML entities correctly', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<p>&lt;</p>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => '<',
                    ]
                ]
            ]
        ]
    ]);

});