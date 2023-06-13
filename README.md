Phrosemirror is a library to work with Prosemirror (or TipTap) JSON content in PHP in an easy and type-safe way.

Here is what this library can do:

* Convert Prosemirror JSON into a Document with typed Nodes, Marks, and Attributes
* Analyze and manipulate Documents
* Convert a Document to HTML
* Convert a Document to Text
* Parse HTML to a Document

Coming soon:

* `content` and `group` in schema for further validation

> This library is currently in beta. We will go through multiple changes. Do not use in production.

## Installation

```
composer require hyvor/phrosemirror
```

## 1. Schema

This library is unopinionated, which means there is no default schema. To start, you have to start with defining your schema that is similar to your front-end Prosemirror configurations.

```php
use Hyvor\Phrosemirror\Types\Schema;

$schema = new Schema(
    [
        new Doc,
        new Text,
        new Paragraph,
        new Blockquote,
        new Image
    ],
    [
        new Strong,
        new Italic,  
    ]
);
```

In the `Schema` constructor, first argument is an array of **Nodes Types** and the second one is an array of **Marks Types**.

### Node Types

A basic node type looks like this:

```php
use Hyvor\Phrosemirror\Types\NodeType;

class Doc extends NodeType
{
    public string $name = 'doc';
}
```

### Mark Types

A basic mark type looks like this:

```php
use Hyvor\Phrosemirror\Types\MarkType;

class Strong extends MarkType
{
    public string $name = 'strong';
}
```

### Attributes (Attrs)

One main goal of this library is to achieve type-safety. Therefore, attributes are defined in a typed class.

```php
use Hyvor\Phrosemirror\Types\AttrsType;

class ImageAttrs extends AttrsType
{

    public string $src;
    public ?string $alt;
    
}
```

> By defining explicit types, we are sure that `src` attribute of the Image is always a string. `alt` can be a string or null.

Then, in the Node Type, you have to mention the Attrs class.

```php
use Hyvor\Phrosemirror\Types\NodeType;

class Image extends NodeType
{
    // ...
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
use Hyvor\Phrosemirror\Types\Schema;
use Hyvor\Phrosemirror\Document\Document;

$schema = new Schema($nodes, $marks);
$json = '{}'; // <- this is the JSON from the front-end

$document = Document::fromJson($schema, $json);
```

`$json` can be a JSON string, a PHP array, or a PHP object. If the given JSON is valid, `$document` will be an instance of `Hyvor\Phrosemirror\Document\Document`. If not, an error will be thrown. See Error Handling below.

### Node

A `Document` is just a `Node` with the `doc` type. These are the properties of a Node.

```php
namespace Hyvor\Phrosemirror\Document;

use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Types\AttrsType;

class Node
{
    public NodeType $type;
    public AttrsType $attrs;
    public Fragment $content;
    public Mark[] $marks;
}
```

`NodeType $type` is the type of the node, which you defined in the schema

`AttrsType $attrs` is the attributes of the Node. This will be an object of the class you defined in Node Type attrs. For example, as in the above example of Node Types, if the node is `Image`, `$attrs` will be an object of `ImageAttrs`.

`Fragment $content` is a collection of children Nodes.

`Mark[] $marks` is an array of Marks assigned to this node.

> `TextNode` is a special `Node` that represents the `text` node type in Prosemirror. It has the `string $text` property in addition to the above properties. Also, `$marks` only makes sense in the context of `TextNode`.

#### Checking Node Type

Use `isOfType()` to check if a `Node` is of a particular `NodeType` defined in your schema.

```php
$json = ['type' => 'paragraph'];
$node = Node::fromJson($schema, $json);

$node->isOfType(Paragraph::class); // true
$node->isOfType(Image::class); // false
$node->isOfType([Paragraph::class, Image::class]); // true
```

> Note that, just like `Document::fromJson()`, you can create other nodes from JSON using `Node::fromJson()`. `Document` is an extended `Node` that makes sure the root node is `doc`.

#### Accessing Attributes

Use the `attr()` method to access an attribute of the Node.

```php
$json = ['type' => 'image', 'attrs' => ['src' => 'image.png']];
$image = Node::fromJson($schema, $json);

// html-escaped (safe to use in HTML output)
$src = $image->attr('src');

// not html-escaped
$src = $image->attr('src', escape: false);
```

#### Traversing Through Nested Nodes

You can traverse through nested nodes using the `traverse()` method with a callback. Here is an example that traverse through all nodes and finds all image nodes.

```php
$document = Document::fromJson($schema, $json);

$images = [];
$document->traverse(function(Node $node) use(&$images) {
    if ($node->isOfType(Image::class)) {
        $images[] = $node;
    }
})
```

> `traverse()` traverses through `TextNode`s too!

#### Traversing Through Direct Children

Use `foreach` with `$node->content`.

```php
foreach ($node->content as $child) {
    if ($child->isOfType(Image::class)) {
        echo "I found an image!";
    }
}
```

#### Finding Nodes

Earlier, we used `traverse()` to find nodes, but there is the `getNodes()` method to make it easier. It searches through the all nested nodes and returns `Node[]` of matched nodes.

```php
// images
$node->getNodes(Image::class);

// all nodes (including TextNodes)
$node->getNodes();

// nodes of multiple types
$node->getNodes([Paragraph::class, Blockquote::class]);

// images (only direct children)
$node->getNodes(Image::class, false);
```

#### Finding Marks

Similar to `getNodes()` you can use `getMarks()` to find marks within the current node. It searches all nested nodes and returns `Mark[]` of matched marks.

```php
// links
$node->getMarks(Link::class);

// all marks
$node->getMarks();

// multiple types
$node->getMarks([Strong::class, Italic::class]);

// without nesting (marks of the current node only)
$node->getMarks(Link::class, false);
```

#### JSON Serialize

You can serialize a Node/Document back to JSON.

```php
$node->toJson(); // JSON string
$node->toArray(); // PHP array
```

### Mark

```php
namespace Hyvor\Phrosemirror\Document;

use Hyvor\Phrosemirror\Types\MarkType;
use Hyvor\Phrosemirror\Types\AttrsType;

class Mark
{
    public MarkType $type;
    public AttrsType $attrs;  
}
```

`$type` and `$attrs` are analogous to those of Node's.

`Mark` has `isOfType()`, `attr()`, `toArray()`, and `toJson()`, which works similar to `Node`'s methods.

```php
$mark = Mark::fromJson(['type' => 'link', 'attrs' => ['src' => 'https://hyvor.com']);

$mark->isOfType(Strong::class); // false
$mark->attr('src'); // https://hyvor.com
```

### Fragment

`$node->content` is a `Fragment`. It contains an array of children nodes. You can think of it just as an array, but with helper methods that makes things easier.

```php
$fragment = $node->content();

// READ

$fragment->first(); // Node | null
$fragment->last(); // Node | null
$fragment->nth(2); // Node | null

$fragment->count(); // int

// get all Nodes in the Fragment as an array
$fragment->all(); // Node[]

// loop through each node
$fragment->each(fn (Node $node) => false);

// WRITE (Be careful, these methods changes the document)

$fragment->addNodeToStart($node);
$fragment->addNodeToEnd($node);
$fragment->addNode($node); // same as addNodeToEnd
$fragment->setNodes($nodes);
$fragment->map(fn (Node $node) => $node); // update nodes in a callback
```

## 3. HTML

Next, let's convert your document to HTML. To do this, you have to define the `toHtml()` method in Node Types and Mark Types.

```php
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

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
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

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

### HTML: Document -> HTML

Use the `toHtml()` method to serialize a document (or any node) to HTML. 

```php
$document = Document::fromJson($schema, $json);
$html = $document->toHtml();
```

## Parsing HTML

The `HtmlParser` class is responsible for parsing HTML to a Document. It takes the Schema and some parsing rules to parse the HTML.

```php
<?php
use Hyvor\Phrosemirror\Converters\HtmlParser\HtmlParser;use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;

$schema = new Schema($nodes, $marks); // this is the same schema you create for the document
$parser = new HtmlParser($schema, [
    new ParserRule(tag: 'p', node: 'paragraph'),
    new ParserRule(tag: '#text', node: 'text'),
    // ... other rules
])
```

However, in most cases, you only need one rule set to parse from multiple HTML inputs. Therefore, you can directly define rules in the Schema (in the `fromHtml()` method of Nodes and Marks).

```php
use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Document\Node;

class Paragraph extends NodeType
{

    public string $name = 'paragraph';

    public function toHtml(Node $node, string $children): string
    {
        return "<p>$children</p>";
    }

    public function fromHtml(): array
    {
        return [
            new ParserRule(tag: 'p'),
        ];
    }

}
```

The `fromHtml()` method should return `ParserRule[]`. Here, the `node` property is not required as it is the same as the Node Type's name.

### Parsing HTML Attributes to Node Attributes

Use the `getAttrs()` method to parse attributes from the HTML element.

```php
use DOMElement;

class Image extends NodeType
{
    public string $name = 'image';
    public string $attrs = ImageAttrs::class;
    
    public function fromHtml() : array
    {
    
        return [
            new ParserRule(
                tag: 'img', 
                getAttrs: fn (DOMElement $element) => ImageAttrs::fromArray([
                    'src' => $element->getAttribute('src'),
                    'alt' => $element->getAttribute('alt'),
                ])
            )
        ];
    
    }
}
```

The `getAttrs()` callback should return one of the following:

- `false` to ignore the element
- `null` if the attributes are not found
- `AttrsType` if the attributes are found

### Parsing Using Styles

You can also parse HTML elements using their styles instead of the tag.

```php

```


## Error Handling

This library is strict, and it expects correct input from the front-end. It can throw the following exceptions:

* `InvalidJsonException` - on invalid JSON
* `InvalidAttributeTypeException` - on invalid attribute type

Both exceptions extend the `PhrosemirrorException` class. Therefore, the best practise would be catching it when building the document.

```php
use Hyvor\Phrosemirror\Exception\PhrosemirrorException;

try {
    $document = Document::fromJson($schema, $json);
} catch (PhrosemirrorException $e) {
    // invalid document
}
```

If the front-end (JS) and back-end (PHP) schema matches, the only way an exception can happen is when the Prosemirror JSON is altered. Therefore, it is a good practise to stop processing here.

## Who uses this Library?

* [Hyvor Talk](https://talk.hyvor.com)
* [Hyvor Blogs](https://blogs.hyvor.com)
* Add yours with a PR