<?php
namespace Hyvor\Phrosemirror\Test\Unit\Content;

use Hyvor\Phrosemirror\Content\Sanitizer;
use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Types\AttrsType;
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
    public ?string $content = 'inline*';
}

class SanitizerBlockquote extends NodeType
{
    public string $name = 'blockquote';
    public string $group = 'block';
}

class SanitizerFigure extends NodeType
{
    public string $name = 'figure';
    public string $group = 'block';
    public ?string $content = 'image';
}

class SanitizerImage extends NodeType
{
    public string $name = 'image';
    public string $group = 'figure_elements';

    public string $attrs = SanitizerImageAttrs::class;
}
class SanitizerImageAttrs extends AttrsType
{
    public string $src;
}

class SanitizerOrderedList extends NodeType
{
    public string $name = 'ordered_list';
    public string $group = 'block';
    public ?string $content = 'list_item+';
}

class SanitizerListItem extends NodeType
{
    public string $name = 'list_item';
    public string $group = 'block';
    public ?string $content = 'paragraph block*';
}

class SanitizerText extends NodeType
{
    public string $name = 'text';
    public string $group = 'inline';
    public bool $inline = true;
}

beforeEach(function() {
    $this->getSchema = function($doc = null) {
        return new Schema(
             [
                $doc ?? new SanitizerDoc,
                new SanitizerParagraph,
                new SanitizerBlockquote,
                new SanitizerImage,
                new SanitizerFigure,
                new SanitizerOrderedList,
                new SanitizerListItem,
                 new SanitizerText()
            ],
            []
        );
    };
});

it('wraps text in list nodes around paragraphs', function() {

    $schema = ($this->getSchema)();

    $doc = Document::fromJson(($this->getSchema)(), [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'ordered_list',
                'content' => [
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Hello',
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'ordered_list',
                'content' => [
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'Hello',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]));

});

it('wraps images in figures', function() {

    $schema = ($this->getSchema)();

    $doc = Document::fromJson(($this->getSchema)(), [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'image',
                'attrs' => [
                    'src' => 'https://example.com/image.png'
                ]
            ]
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'image',
                        'attrs' => [
                            'src' => 'https://example.com/image.png'
                        ]
                    ]
                ]
            ]
        ]
    ]));

});

it('connects inline elements to previous', function() {

    $schema = ($this->getSchema)();

    $doc = Document::fromJson(($this->getSchema)(), [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello',
                    ]
                ]
            ],
            [
                'type' => 'text',
                'text' => 'World',
            ]
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello',
                    ],
                    [
                        'type' => 'text',
                        'text' => 'World',
                    ]
                ]
            ]
        ]
    ]));

});

it('wraps text in list nodes in paragraphs and connects texts', function() {

    $schema = ($this->getSchema)();

    $doc = Document::fromJson(($this->getSchema)(), [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'ordered_list',
                'content' => [
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Hello',
                            ],
                            [
                                'type' => 'text',
                                'text' => 'World',
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'ordered_list',
                'content' => [
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'Hello',
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'World',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]));

});


it('removes invalid elements when cannot be wrapped', function() {

    $schema = ($this->getSchema)();

    $doc = Document::fromJson(($this->getSchema)(), [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'image'
                    ]
                ]
            ],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph'
            ],
        ]
    ]));

});