<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document;

use Hyvor\Phrosemirror\Document\Fragment;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\BlockquoteNodeType;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ParagraphNodeType;

// === READERS ===

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



test('each', function() {

    $node = new Node(new ParagraphNodeType);
    $fragment = new Fragment([$node, $node, $node]);

    $i = 0;

    $fragment->each(function (Node $node) use (&$i) {$i = $i + 1;});

    expect($i)->toBe(3);

});

// === WRITERS ===

test('add node to end', function() {

    $fragment = new Fragment([new Node(new ParagraphNodeType)]);
    $node = new Node(new BlockquoteNodeType);
    $fragment->addNode($node);

    expect($fragment->count())->toBe(2);
    expect($fragment->last()->isOfType(BlockquoteNodeType::class))->toBeTrue();

});

test('add node to start', function() {

    $fragment = new Fragment([new Node(new ParagraphNodeType)]);
    $node = new Node(new BlockquoteNodeType);
    $fragment->addNodeToStart($node);

    expect($fragment->count())->toBe(2);
    expect($fragment->first()->isOfType(BlockquoteNodeType::class))->toBeTrue();

});

test('set nodes', function() {

    $fragment = new Fragment();
    $node = new Node(new ParagraphNodeType);
    $fragment->setNodes([$node]);

    expect($fragment->count())->toBe(1);
    expect($fragment->first()->isOfType(ParagraphNodeType::class))->toBeTrue();

});

test('map', function() {

    $node = new Node(new ParagraphNodeType);
    $fragment = new Fragment([$node, $node, $node]);

    $fragment->map(function (Node $node) {
        $node->type = new BlockquoteNodeType;
        return $node;
    });

    expect($fragment->first()->isOfType(BlockquoteNodeType::class))->toBeTrue();

});

it('removes a node and indexes correctly', function() {

    $paragraph = new Node(new ParagraphNodeType);
    $blockquote = new Node(new BlockquoteNodeType);
    $fragment = new Fragment([$paragraph, $blockquote]);

    $fragment->removeNode($paragraph);

    expect($fragment->count())->toBe(1);
    expect($fragment->first()->isOfType(BlockquoteNodeType::class))->toBeTrue();

});

it('replaces a node', function() {

    $paragraph = new Node(new ParagraphNodeType);
    $blockquote = new Node(new BlockquoteNodeType);
    $fragment = new Fragment([$paragraph, $blockquote]);

    $p2 = new Node(new ParagraphNodeType);

    $fragment->replaceNode($blockquote, $p2);

    expect($fragment->count())->toBe(2);
    expect($fragment->first()->isOfType(ParagraphNodeType::class))->toBeTrue();
    expect($fragment->nth(2)->isOfType(ParagraphNodeType::class))->toBeTrue();

});