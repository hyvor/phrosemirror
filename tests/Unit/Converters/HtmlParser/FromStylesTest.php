<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;


use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;

it('parses mark from styles', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(tag: 'p', node: 'paragraph'),
        new ParserRule(style: 'font-weight', mark: 'strong'),
        new ParserRule(tag: '#text', node: 'text'),
    ]);

    $doc = $parser->parse('<p style="font-weight: bold">Hello</p>');

    expect($doc->toArray())->toEqual([
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
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);


})->skip();
