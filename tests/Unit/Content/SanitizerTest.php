<?php

namespace Hyvor\Phrosemirror\Test\Unit\Sanitizer;

use Hyvor\Phrosemirror\Content\ContentExpression;
use Hyvor\Phrosemirror\Content\Nfa\Nfa;
use Hyvor\Phrosemirror\Content\Sanitizer;
use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Types\Schema;

class SanitizerDoc extends NodeType
{
    public string $name = 'doc';
    public ?string $content = 'block+';
}

class SanitizerParagraph extends NodeType
{
    public string $name = 'paragraph';
    public string $group = 'block';
}

class SanitizerBlockquote extends NodeType
{
    public string $name = 'blockquote';
    public string $group = 'block';
}

class SanitizerImage extends NodeType
{
    public string $name = 'image';
    public string $group = 'figure_elements';
}

beforeEach(function() {
    $this->getSchema = function() {
        return new Schema(
             [
                new SanitizerDoc,
                new SanitizerParagraph,
                new SanitizerBlockquote,
                new SanitizerImage,
            ],
            []
        );
    };
});

it('test', function() {

    $schema = ($this->getSchema)();

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'blockquote'],
            ['type' => 'image'],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    dd($sanitized);

});

it('nfa', function() {

    $schema = ($this->getSchema)();
    $exp = ContentExpression::getExpr('paragraph|blockquote', $schema);

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'blockquote']
        ]
    ]);

    Sanitizer::sanitize($schema, $doc);

});