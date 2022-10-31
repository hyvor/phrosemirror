<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document;

use Hyvor\Phrosemirror\Document\Fragment;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\BlockquoteNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ParagraphNodeType;

test('methods', function() {

    $schema = schema();
    $fragment = new Fragment([
        Node::fromJson($schema, ['type' => 'paragraph']),
        Node::fromJson($schema, ['type' => 'blockquote'])
    ]);

    expect($fragment->first()->isOfType(ParagraphNodeType::class))->toBeTrue();
    expect($fragment->nth(2)->isOfType(BlockquoteNodeType::class))->toBeTrue();
    expect($fragment->last()->isOfType(BlockquoteNodeType::class))->toBeTrue();
    expect($fragment->nth(3))->toBeNull();

});

test('methods on empty', function() {

    $fragment = new Fragment([]);

    expect($fragment->first())->toBeNull();
    expect($fragment->last())->toBeNull();
    expect($fragment->nth(10))->toBeNull();

});