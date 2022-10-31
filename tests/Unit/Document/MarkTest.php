<?php

namespace Hyvor\Prosemirror\Test\Unit\Document;

use Hyvor\Prosemirror\Document\Mark;
use Hyvor\Prosemirror\Document\Node;
use Hyvor\Prosemirror\Exception\InvalidJsonException;
use Hyvor\Prosemirror\Test\TestTypes\Marks\LinkMarkType;
use Hyvor\Prosemirror\Test\TestTypes\Marks\StrongMarkType;

it('gets marks from JSON', function() {

    $json = [
        'type' => 'text',
        'text' => 'Test',
        'marks' => [
            [
                'type' => 'strong'
            ]
        ]
    ];

    $node = Node::fromJson(schema(), $json);

    expect($node->marks)->toHaveLength(1);

    $mark = $node->marks[0];
    expect($mark->isOfType(StrongMarkType::class))->toBeTrue();
    expect($mark->isOfType(LinkMarkType::class))->toBeFalse();

});

it('mark with attrs', function() {

    $json = [
        'type' => 'text',
        'text' => 'Test',
        'marks' => [
            [
                'type' => 'link',
                'attrs' => [
                    'href' => 'https://hyvor.com'
                ]
            ]
        ]
    ];

    $node = Node::fromJson(schema(), $json);

    $mark = $node->marks[0];
    expect($mark->isOfType(LinkMarkType::class))->toBeTrue();
    expect($mark->attr('href'))->toBe('https://hyvor.com');

});




it('fails when type is not set', function() {

    $json = [];
    Mark::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Mark type is not set in JSON');

it('fails when an unknown node type is found', function() {

    $json = [
        'type' => 'unknown',
    ];
    Mark::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Mark type unknown not found in schema');

it('fails when attrs value is wrong', function() {

    $json = [
        'type' => 'strong',
        'attrs' => 'invalid'
    ];
    Mark::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Mark Attrs should be an array in strong');