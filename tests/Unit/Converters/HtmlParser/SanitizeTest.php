<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;

use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\HeadingNodeAttrs;

it('does not sanitize', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'blockquote', tag: 'blockquote'),
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse("<blockquote>Hello</blockquote>");

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'blockquote',
                'content' => [
                    ['type' => 'text', 'text' => 'Hello'],
                    /*[
                        'type' => 'paragraph',
                        'content' => [

                        ]
                    ]*/
                ]
            ]
        ]
    ]);

});

it('sanitizes', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(node: 'blockquote', tag: 'blockquote'),
        new ParserRule(node: 'paragraph', tag: 'p'),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse("<blockquote>Hello</blockquote>", sanitize: true);

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'blockquote',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'Hello'],
                        ]
                    ]
                ]
            ]
        ]
    ]);

});