<?php
namespace Hyvor\Phrosemirror\Test\Unit\Content;

use Hyvor\Phrosemirror\Content\Sanitizer\Sanitizer;
use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Test\Unit\Content\Sanitizer\SanitizerHelper;
use Hyvor\Phrosemirror\Types\NodeType;

it('wraps text in list nodes around paragraphs', function() {

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson(SanitizerHelper::getSchema(), [
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

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson(SanitizerHelper::getSchema(), [
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

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson(SanitizerHelper::getSchema(), [
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

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson(SanitizerHelper::getSchema(), [
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

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson(SanitizerHelper::getSchema(), [
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

it('works with range expr', function() {

    $schema = SanitizerHelper::getSchema(new class extends NodeType {
        public string $name = 'doc';
        public ?string $content = 'paragraph paragraph{1,2}';
    });

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
        ]
    ]));

});

it('works with range expr without ending', function() {

    $schema = SanitizerHelper::getSchema(new class extends NodeType {
        public string $name = 'doc';
        public ?string $content = 'paragraph paragraph{1,}';
    });

    $doc1 = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
        ]
    ]);

    $doc2 = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
        ]
    ]);

    $doc3 = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
        ]
    ]);

    $sanitizer = new Sanitizer($schema);
    expect($sanitizer->matchChildren($doc1))->toBeTrue();
    expect($sanitizer->matchChildren($doc2))->toBeTrue();
    expect($sanitizer->matchChildren($doc3))->toBeFalse();

});

it('works with optional expr', function() {

    $schema = SanitizerHelper::getSchema(new class extends NodeType {
        public string $name = 'doc';
        public ?string $content = 'paragraph paragraph?';
    });

    $doc1 = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
        ]
    ]);

    $doc2 = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
        ]
    ]);

    $doc3 = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'blockquote'],
        ]
    ]);

    $sanitizer = new Sanitizer($schema);
    expect($sanitizer->matchChildren($doc1))->toBeTrue();
    expect($sanitizer->matchChildren($doc2))->toBeTrue();
    expect($sanitizer->matchChildren($doc3))->toBeFalse();

});

it('empties children when content is not set', function() {

    $schema = SanitizerHelper::getSchema(new class extends NodeType {
        public string $name = 'doc';
    });

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
            ['type' => 'paragraph'],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc'
    ]));

});

it('promotes children before deleting when possible', function() {

    $schema = SanitizerHelper::getSchema(new class extends NodeType {
        public string $name = 'doc';
        public ?string $content = 'paragraph paragraph?';
    });

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'hello']
                        ]
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
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'hello']
                ]
            ],
        ]
    ]));

});

it('promotes children of second child', function() {

    $schema = SanitizerHelper::getSchema();

    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'image',
                    ],
                    [
                        'type' => 'figcaption',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => 'hello']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toArray())->toBe([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'figure',
                'content' => [
                    [
                        'type' => 'image',
                        'attrs' => [
                            'src' => null
                        ]
                    ],
                    [
                        'type' => 'figcaption',
                        'content' => [
                            ['type' => 'text', 'text' => 'hello']
                        ]
                    ]
                ]
            ],
        ]
    ]);

});

it('does not promote children when not possible', function() {

    $schema = SanitizerHelper::getSchema(new class extends NodeType {
        public string $name = 'doc';
        public ?string $content = 'paragraph paragraph?';
    });

    // not possible to promote all 2
    // because then it will be 3 paras
    $doc = Document::fromJson($schema, [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'paragraph'],
                    ['type' => 'paragraph']
                ]
            ],
        ]
    ]);

    $sanitized = Sanitizer::sanitize($schema, $doc);

    expect($sanitized->toJSON())->toEqual(json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
            ],
            // since #28, this is promoted
            [
                'type' => 'paragraph',
            ]
        ]
    ]));

});
