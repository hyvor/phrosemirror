Prosemirror PHP is a package to manipulate Prosemirror (or TipTap) JSON content in PHP in a type-safe and easy way.

## Installation

```
composer require hyvor/prosemirror
```

## Why this library?

The idea of this library is to make working with Prosemirror JSON easy and developer-friendly. Usually, once a user submits their document, it should be sent to the PHP backend as a JSON string (not HTML). This makes (with the help of this library) easy to analyze content in the document and make sure the JSON adheres to the given schema. You can also easily manipulate the document (add/remove/edit nodes). You can use this library to convert JSON to HTML using `toHtml` methods defined by you.

## 1. Schema

This library is unopinionated, which means there is no default schema. To start, you have to start with defining your schema that is similar to your front-end Prosemirror configurations.

```php
use Hyvor\Prosemirror\Types\Schema;

$schema = new Schema(
    [
        'doc' => new Doc,
        'text' => new Text,
        'paragraph' => new Paragraph,
        'image' => new Image
    ],
    [
        'strong' => new Strong,
        'italic' => new Italic,  
    ]
);
```

In the `Schema` constructor, first argument is a keyed array of **Nodes Types** and the second one is a keyed array of **Marks Types**.

### Schema: Node Types

A basic node type looks like this:

```php
use Hyvor\Prosemirror\Types\NodeType;

class Doc extends NodeType
{}
```

### Schema: Mark Types

A basic mark type looks like this:

```php
use Hyvor\Prosemirror\Types\MarkType;

class Strong extends MarkType
{}
```

### Schema: Attributes (Attrs)

One main goal of this library is to achieve type-safety. Therefore, attributes are defined in a typed class.

```php
use Hyvor\Prosemirror\Types\AttrsType;

class ImageAttrs extends AttrsType
{

    public string $src;
    public ?string $alt;
    
}
```

> By defining explicit types, we are sure that `src` attribute of the Image is always a string. `alt` can be a string or null.

Then, in the Node Type, you have to mention the Attrs class.

```php
use Hyvor\Prosemirror\Types\NodeType;

class Image extends NodeType
{
    public string $attrs = ImageAttrs::class;
}
```

You can also define default values for attributes, which will be used if they are not present in the JSON document.

```php
class ImageAttrs extends AttrsType
{
    public string $src = 'https://hyvor.com/placeholder.png';
}
```

## 2. Document

Once the Schema is ready, we can start working with Documents.

```php
use Hyvor\Prosemirror\Types\Schema;
use Hyvor\Prosemirror\Document\Document;

$schema = new Schema($nodes, $marks);
$json = '{}'; // <- this is the JSON from the front-end

$document = Document::fromJson($schema, $json);
```

`$json` can be a JSON string, a PHP array, or a PHP object. If the document is valid, `$document` will be an instance of `Hyvor\Prosemirror\Document\Document`. If not, an error will be thrown. See Error Handling below.

A `Document` is just a `Node` with the `doc` type. These are the properties of a Node.

```
{
    NodeType $type,
    AttrsType $attrs,
    Fragment $content,
    Mark[] $marks
}
```

* `NodeType $type` is the type of the node, which you defined in the schema
* `AttrsType $attrs` is the attributes of the Node. This will be an object of the class you defined in Node Type. For example, as in the above example of Node Types, if the node is `Image`, `$attrs` will be an object of `ImageAttrs`.
* `Fragment $content` is a collection of children Nodes
* `Mark[] $marks` is an array of Marks assigned to this node.

### Document: Example


## 3. HTML

Next, let's convert your document to HTML. To do this, you have to define the `toHtml()` method in Node Types and Mark Types.

```php
use Hyvor\Prosemirror\Document\Node;use Hyvor\Prosemirror\Types\NodeType;

class Paragraph extends NodeType
{

    public function toHtml(Node $node, string $children) : string
    {
        return "<p>$children</p>";
    }

}
```

`toHtml()` should return the HTML string of the node, placing the `$children` string in it.

Here is another example using the attributes of that Node.

```php
use Hyvor\Prosemirror\Document\Node;use Hyvor\Prosemirror\Types\NodeType;

class Image extends NodeType
{

    public function toHtml(Node $node, string $children) : string
    {
        $src = $node->attr('src');
        return "<img src=\"$src\">$children</p>";
    }

}
```

> Do not directly use `$node->attrs->src` as the raw attributes are not HTML-escaped. Always use `$node->attr()` or `$node->attrs->get()`

## Error Handling

This library is strict, and it expects correct input from the 

## Who uses this?

* [Hyvor Talk](https://talk.hyvor.com)
* [Hyvor Blogs](https://blogs.hyvor.com)
* Add yours with a PR