[doc-hooks]: ./hooks.md

# ix-framework: Rendering HTML

You have two options for rendering HTML within ix-framework:

0. `HtmlRenderer` - where the output HTML is defined as PHP code,
    and can be generated anywhere;
0. `Twig-View` - where you define HTML templates in the `templates/`
    directory of your application, and then render them.

For larger applications, or ones where you want the users of your
application to be able to override the templates you provide,
use `Twig-View`.

For small one-off applications, where you don't really care about
customisability or styling and Just Need To Render Some HTML,
`HtmlRenderer` is your best bet.

Picking one doesn't prevent you from using the other - they're both
completely separate implementations, and can happily co-exist within
a single ix-framework application.

## HtmlRenderer

`HtmlRenderer` at it's core is just somewhat-fancy string manipulation,
with a couple helpers to make rendering documents (with `<!DOCTYPE>`)
and individual tags (including attributes, with or without children)
easier.

`HtmlRenderer` is very useful for rendering error pages inside exception
handlers - in fact, ix-framework itself uses it for just that!

To make an `HtmlRenderer` instance available anywhere in your application,
you can include the following [container hook][doc-hooks]:

```php
HookMachine::add(
    [Container::class, 'construct'],
    '\ix\Container\ContainerHooksHtmlRenderer::hookContainerHtmlRenderer'
);
```

Once this hook is added, you can access an `HtmlRenderer` instance from
within a controller with `$this->container->get('html')`. We will use the
shorthand `$html` for this within this section of the documentation.

### Rendering tags

#### Childless tags

To render a tag with no children, with a set of attributes:

```php
echo $html->tag('meta', ['charset' => 'utf-8']);
```

To omit attributes, pass an empty array:

```php
echo $html->tag('tagname', []);
```

#### Tags with children

Rendering tags that have children is pretty much the same as for
childless tags, except that the function call now takes many child parameters:

```php
echo $html->tag('h1', [], "This is the content of an h1,", "&nbsp;", "that just keeps on going!");
```

You can also use the splat operator to pass in an array,
to make things look a bit cleaner:

```php
echo $html->tag('p', ['class' => 'test-class'], ...[
    "This paragraph contains some text, including",
    $html->tag('a', ['href' => 'https://example.com'], "a link"),
    "and some",
    $html->tag('em', [], "other styling!"),
]);
```

Everything that is passed as a child of an element is converted to
a string, and nested arrays are flattened.

### Rendering documents

There is a `$html->renderDocument()` helper function, aimed at generating
full HTML documents - it includes rendering a `<!DOCTYPE>` at the top of
the document, and nesting a `<head>` and `<body>` within a root `<html>`
element.

The `renderDocument()` function takes a whole pile of parameters, but for
most uses, you will only need to specify the first two. These parameters are:

0. The content of the `<head>` element;
0. The content of the `<body>` element;
0. A list of attributes to add to the root `<html>` element (optional, default `[]`);
0. A list of attributes to add to the `<body>` element (optional, default `[]`);
0. The doctype string (optional, default `<!DOCTYPE html>`).

To render a well-formed HTML5 document, with a `lang` attribute, you can do
something like this:

```php
echo $html->renderDocument(
	[
		$html->tag('meta', ['charset' => 'utf-8']),
		$html->tag('meta', ['name' => 'viewport', 'content' => 'initial-scale=1, width=device-width']),
	],
	[
		$html->tagHasChildren('h1', [], 'Hello, world!'),
	],
    [],
    [
        'lang' => 'en'
    ],
);
```

## Twig-View

TODO

### Installing the hooks

```php
HookMachine::add([Container::class, 'construct'], '\ix\Container\ContainerHooksTwig::hookContainerTwig');
HookMachine::add([Application::class, 'create_app', 'preMiddleware'], '\ix\Application\ApplicationHooksTwig::hookApplicationMiddlewareTwig');
```

### Usage

TODO
