<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Exception\InvalidJsonException;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ParagraphNodeType;

it('creates a node', function() {

    $json = [
        'type' => 'paragraph',
        'content' => [
            [
                'type' => 'text',
                'text' => 'Test'
            ]
        ]
    ];

    $node = Node::fromJson(schema(), $json);
    expect($node->type)->toBeInstanceOf(ParagraphNodeType::class);
    expect($node->content)->toHaveLength(1);

});

it('fails when type is not set', function() {

    $json = [
        'content' => []
    ];
    Node::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Node type is not set in JSON');

it('fails when an unknown node type is found', function() {

    $json = [
        'type' => 'unknown',
        'content' => []
    ];
    Node::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Node type unknown not found in schema');

it('fails when attrs value is wrong', function() {

    $json = [
        'type' => 'paragraph',
        'attrs' => 'invalid'
    ];
    Node::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Node Attrs should be an array in paragraph');


it('fails when content value is wrong', function() {

    $json = [
        'type' => 'paragraph',
        'content' => 'invalid'
    ];
    Node::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Node content should be an array in paragraph');


it('fails when marks value is wrong', function() {

    $json = [
        'type' => 'paragraph',
        'marks' => 'invalid'
    ];
    Node::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'Node marks should be an array in paragraph');