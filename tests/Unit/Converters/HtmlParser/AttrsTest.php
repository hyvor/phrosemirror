<?php

namespace Hyvor\Phrosemirror\Test\Unit\Converters\HtmlParser;

use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Test\TestTypes\Marks\LinkMarkAttrs;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ImageNodeAttrs;

it('gets node attrs', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(
            node: 'image',
            tag: 'img',
            getAttrs: function ($domElement) {
                return ImageNodeAttrs::fromArray([
                    'src' => $domElement->getAttribute('src'),
                    'alt' => $domElement->getAttribute('alt'),
                ]);
            }
        ),
    ]);

    $document = $parser->parse('<img src="image.png" alt="Image" />');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'image',
                'attrs' => [
                    'src' => 'image.png',
                    'alt' => 'Image',
                ],
            ]
        ]
    ]);

});

it('get mark attrs', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(
            mark: 'link',
            tag: 'a',
            getAttrs: function ($domElement) {
                return LinkMarkAttrs::fromArray([
                    'href' => $domElement->getAttribute('href'),
                ]);
            }
        ),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<a href="https://hyvor.com">Hyvor</a>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'text',
                'text' => 'Hyvor',
                'marks' => [
                    [
                        'type' => 'link',
                        'attrs' => [
                            'href' => 'https://hyvor.com',
                        ],
                    ]
                ]
            ]
        ]
    ]);

});

it('does not match when getAttrs returns false', function() {

    $parser = new HtmlParser(schema(), [
        new ParserRule(
            mark: 'link',
            tag: 'a',
            getAttrs: function ($domElement) {

                if ($domElement->getAttribute('class') === 'hidden')
                    return false;

                return LinkMarkAttrs::fromArray([
                    'href' => $domElement->getAttribute('href'),
                ]);
            }
        ),
        new ParserRule(node: 'text', tag: '#text'),
    ]);

    $document = $parser->parse('<a href="https://hyvor.com">Hyvor</a><a href="https://hyvor.com" class="hidden">Hyvor</a>');

    expect($document->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'text',
                'text' => 'Hyvor',
                'marks' => [
                    [
                        'type' => 'link',
                        'attrs' => [
                            'href' => 'https://hyvor.com',
                        ],
                    ]
                ]
            ],
            [
                'type' => 'text',
                'text' => 'Hyvor',
            ]
        ]
    ]);

});