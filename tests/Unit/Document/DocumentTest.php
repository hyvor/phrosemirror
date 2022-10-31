<?php

namespace Hyvor\Prosemirror\Test\Unit;

use Hyvor\Prosemirror\Document\Document;
use Hyvor\Prosemirror\Document\Fragment;
use Hyvor\Prosemirror\Document\Node;
use Hyvor\Prosemirror\Document\TextNode;
use Hyvor\Prosemirror\Exception\InvalidJsonException;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\DocNodeType;
use Hyvor\Prosemirror\Test\TestTypes\Nodes\ParagraphNodeType;

it('creates a basic document', function() {

    $json = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Test'
                    ]
                ]
            ]
        ]
    ];

    $document = Document::fromJson(schema(), $json);

    // doc
    expect($document->type)->toBeInstanceOf(DocNodeType::class);
    expect($document->content)->toHaveLength(1);
    expect($document->content)->toBeInstanceOf(Fragment::class);

    // content
    $first = $document->content->first();
    expect($first)->toBeInstanceOf(Node::class);
    expect($first->type)->toBeInstanceOf(ParagraphNodeType::class);

    // text
    $text = $first->content->first();
    expect($text)->toBeInstanceOf(TextNode::class);
    expect($text->text)->toBe('Test');

});

it('should have a doc top node', function() {

    $json = [
        'type' => 'paragraph',
    ];
    Document::fromJson(schema(), $json);

})->throws(InvalidJsonException::class, 'The top node must be a doc node');