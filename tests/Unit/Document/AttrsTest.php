<?php

namespace Hyvor\Phrosemirror\Test\Unit\Document;

use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Exception\InvalidAttributeTypeException;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ImageNodeAttrs;
use Hyvor\Phrosemirror\Test\TestTypes\Nodes\ImageNodeType;
use Hyvor\Phrosemirror\Types\AttrsType;
use Hyvor\Phrosemirror\Types\NodeType;

it('adds attributes', function() {

    $json = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'image',
                'attrs' => [
                    'src' => 'image.png',
                    'alt' => 'Hyvor Logo'
                ]
            ]
        ]
    ];

    $document = Document::fromJson(schema(), $json);

    $image = $document->content->first();

    expect($image->type)->toBeInstanceOf(ImageNodeType::class);
    expect($image->attrs)->toBeInstanceOf(ImageNodeAttrs::class);
    expect($image->attr('src'))->toBe('image.png');
    expect($image->attr('alt'))->toBe('Hyvor Logo');

});

it('fails when attribute types are wrong', function() {

    $json = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'image',
                'attrs' => [
                    'src' => null,
                    'alt' => null
                ]
            ]
        ]
    ];

    Document::fromJson(schema(), $json);

})->throws(
    InvalidAttributeTypeException::class,
    'Invalid type in the src attribute (Given type: NULL)'
);

it('fallbacks to null when attribute is missing', function() {

    $json = [
        'type' => 'image',
        'attrs' => [
            'src' => 'image.png'
        ]
    ];

    $image = Node::fromJson(schema(), $json);
    expect($image->attrs->alt)->toBeNull();

});

class ImageWithDefaultAttrs extends AttrsType
{
    public $src = 'default.png';

    private bool $hidden;
}

class ImageWithDefault extends NodeType
{
    public string $name = 'image';
    public string $attrs = ImageWithDefaultAttrs::class;
}

it('uses default value', function() {

    $json = [
        'type' => 'image',
    ];

    $image = Node::fromJson(schema([
        new ImageWithDefault
    ]), $json);
    expect($image->attr('src'))->toBe('default.png');

});

it('uses default value when the attribute is null', function() {

    $json = [
        'type' => 'image',
        'attrs' => [
            'src' => null
        ]
    ];

    $image = Node::fromJson(schema([
        'image' => new ImageWithDefault
    ]), $json);
    expect($image->attr('src'))->toBe('default.png');

});