<?php

namespace Hyvor\Phrosemirror\Test\Unit;

use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Document\Fragment;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Document\TextNode;
use Hyvor\Phrosemirror\Exception\InvalidJsonException;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\DocNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ParagraphNodeType;

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