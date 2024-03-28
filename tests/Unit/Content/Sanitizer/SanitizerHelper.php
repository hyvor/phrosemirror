<?php

namespace Hyvor\Phrosemirror\Test\Unit\Content\Sanitizer;

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
    public ?string $content = 'image figcaption?';
}

class SanitizerImage extends NodeType
{
    public string $name = 'image';
    public string $group = 'figure_elements';

    public string $attrs = SanitizerImageAttrs::class;
}
class SanitizerImageAttrs extends AttrsType
{
    public ?string $src = null;
}

class SanitizerFigcaption extends NodeType
{
    public string $name = 'figcaption';
    public ?string $content = 'inline*';
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


class SanitizerHelper
{

    public static function getSchema($doc = null)
    {
        return new Schema(
            [
                $doc ?? new SanitizerDoc,
                new SanitizerParagraph,
                new SanitizerBlockquote,
                new SanitizerImage,
                new SanitizerFigure,
                new SanitizerOrderedList,
                new SanitizerListItem,
                new SanitizerText(),
                new SanitizerFigcaption(),
            ],
            []
        );
    }

}