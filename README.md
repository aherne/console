# Lucinda Console

This API was created to give an ability of styling console responses so they are easy to read and pleasurable to see. It does this in two steps:

1. Defining a platform to create and format texts via following classes:
    - **[Text](#Text)**: class encapsulating a text, able to be applied any of above three UNIX styling options: 
        - **[BackgroundColor](https://github.com/aherne/console/blob/master/src/BackgroundColor.php)**: enum encapsulating background colors UNIX console texts can have 
        - **[ForegroundColor](https://github.com/aherne/console/blob/master/src/ForegroundColor.php)**: enum encapsulating foreground colors UNIX console texts can have  
        - **[FontStyle](https://github.com/aherne/console/blob/master/src/FontStyle.php)**: enum encapsulating font styles UNIX console texts can have (eg: bold)
    - **[Table](#Table)**: class encapsulating a table, not able to include sub-tables
    - **[OrderedList](#OrderedList)**: class encapsulating an ordered list, able to contain leaves that point to other ordered lists
    - **[UnorderedList](#UnorderedList)**: class encapsulating a unordered list, able to contain leaves that point to other unordered lists
2. Defining a HTML-like [templating language](#console-templating-language) that points to above structures behind the scenes, helping developers to implement console frontend without programming via following tags:
    - **[&lt;div&gt;](#div-tag)**: same as HTML tag but only supporting *style* attribute. 
    - **[&lt;table&gt;](#table-tag)**: same as HTML tag but with a number of restrictions
    - **[&lt;ol&gt;](#ol-tag)**: same as HTML tag but with a number of differences and restrictions
    - **[&lt;ul&gt;](#ul-tag)**: same as HTML tag, with equivalent differences and restrictions as &lt;ol&gt;
    - **[&lt;br&gt;](#br-tag)**: same as HTML tag
3. Defining a class able to bind templated text at point #2 with structures at point #3 in order to build the final view:
     - **[Wrapper](#Wrapper)**: class encapsulating a table

API requires no dependency other than PHP 7.1+ interpreter and SimpleXML extension. All classes inside belong to **Lucinda\Console** interface!

## Example Usage

```php
// defines text to be compiled
$text = '
<div style="font-weight: bold">hello</div>
    
<table>
    <thead>
        <tr>
            <td style="background-color: red">Name</td>
            <td>Value</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="color: green">qqq</td>
            <td>sss</td>
        </tr>
        <tr>
            <td>ddd</td>
            <td>fff</td>
        </tr>
    </tbody>
</table>
    
<ol>
    <caption style="color: blue">Is Lucinda smart?</caption>
    <li>
        <ol>
            <caption>Yes</caption>
            <li style="background-color: blue">qwerty</li>
            <li>asdfgh</li>
        </ol>
    </li>
    <li>No</li>
</ol>
';

// compiling and outputting results (on windows style attributes will be ignored)
$wrapper = new Lucinda\Console\Wrapper($text);
echo $wrapper->getBody();
```

## Console Templating Language

Console templating language supports a fraction of HTML standard, namely parts that are feasable in styling and formatting console text. Certain elements allow a *style* attribute that supports following CSS directives:

- *font-weight*: value must be one of [FontStyle](https://github.com/aherne/console/blob/master/src/FontStyle.php) constant names
- *background-color*: value must be one of [BackgroundColor](https://github.com/aherne/console/blob/master/src/BackgroundColor.php) constant names
- *color*: value must be one of [ForegroundColor](https://github.com/aherne/console/blob/master/src/ForegroundColor.php) constant names
    
### Div Tag

Binding to **[Text](#Text)**, works the same as HTML &lt;div&gt; tag with following restrictions:

- only supporting *style* attribute
- body can only contain plain text

Syntax example:

```html
<div style="background-color: red">Hello, world!</div>
```

### Table Tag

Binding to **[Table](#Table)**, works the same as HTML &lt;table&gt; tag with following restrictions:

- must have a &lt;thead&gt; child
- must have a &lt;tbody&gt; child
- any &lt;tr&gt; inside supports no attributes
- any &lt;td&gt; inside supports only *style* attribute 

Syntax example:

```html
<table>
    <thead>
        <tr>
            <td style="color: red">Name</td>
            <td>Value</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>qqq</td>
            <td>sss</td>
        </tr>
    </tbody>
</table>
```

### Ol Tag

Binding to **[OrderedList](#OrderedList)**, works the same as HTML &lt;ol&gt; tag with following differences and restrictions:

- can contain a &lt;caption&gt; tag defining what ordered list is about (behaving as **[&lt;div&gt;](#div-tag)**). If present it MUST be first child!
- must contain &lt;li&gt; subtags supporting only *style* attribute
- if &lt;li&gt; branches to another &lt;ol&gt;, latter must be the only child

Example:

```html
<ol>
    <caption style="color: blue">Is Lucinda smart?</caption>
    <li>
        <ol>
            <caption>Yes</caption>
            <li style="background-color: blue">qwerty</li>
            <li>asdfgh</li>
        </ol>
    </li>
    <li>No</li>
</ol>
```

### Ul Tag

Binding to **[UnorderedList](#UnorderedList)**, works the same as HTML &lt;ul&gt; tag with equivalent differences and restrictions as **[&lt;ol&gt;](#ol-tag)**.

### Br Tag

Works the same as HTML &lt;br&gt.

## Reference Guide

### Stringable

Interface [Lucinda\Console\Stringable](https://github.com/aherne/console/blob/master/src/Stringable.php) defines blueprint of an entity able to be displayed on console/terminal via method:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| toString | void | string | Gets entity string representation to be displayed on console |

### Text

Class [Lucinda\Console\Text](https://github.com/aherne/console/blob/master/src/Text.php) implements [Stringable](#Stringable) and styles a UNIX console text, defining following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $text | void | Sets text to style |
| setFontStyle | [Lucinda\Console\FontStyle](https://github.com/aherne/console/blob/master/src/FontStyle.php) $style | void | Sets text style (eg: makes it bold) from input enum member. |
| setBackgroundColor | [Lucinda\Console\BackgroundColor](https://github.com/aherne/console/blob/master/src/BackgroundColor.php) $color | void | Sets text background color from input enum member. |
| setForegroundColor | [Lucinda\Console\ForegroundColor](https://github.com/aherne/console/blob/master/src/ForegroundColor.php) $color | void | Sets text foreground color from input enum member. |
| getOriginalValue | void | string | Gets original text before styling |
| getStyledValue | void | string | Gets final text after styling |
| toString | void | string | Gets final string representation of text to be shown on console/terminal |

### Table

Class [Lucinda\Console\Table](https://github.com/aherne/console/blob/master/src/Table.php) implements [Stringable](#Stringable) and creates a table to be displayed on console/terminal, defining following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | array $columns | void | Sets table columns based on *string* or *[Text](#Text)* array input |
| addRow | array $row | void | Adds a row to table based on *string* or *[Text](#Text)* array input |
| toString | void | string | Gets final string representation of table to be shown on console/terminal |

### AbstractList

Abstract class [Lucinda\Console\AbstractList](https://github.com/aherne/console/blob/master/src/AbstractList.php) implements [Stringable](#Stringable) and creates a list to be displayed on console/terminal, defining following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string\|Text $caption = null | void | Sets optional caption to define what list is about based on *string* or *[Text](#Text)* input |
| addItem | string\|Text $$item | void | Adds a textual list item based on *string* or *[Text](#Text)* input. |
| addList | string\|Text $caption = null | [AbstractList](#AbstractList) | Creates a sublist by optional caption to define what list is about based on *string* or *[Text](#Text)* input, adds to parent and returns it |
| toString | void | string | Gets final string representation of list to be shown on console/terminal |

and following abstract method children must implement:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| formatOptionNumber | int $optionNumber | string | Formats list option number for later display |

### OrderedList

Class [Lucinda\Console\OrderedList](https://github.com/aherne/console/blob/master/src/OrderedList.php) extends [AbstractList](#AbstractList) and creates an ordered list to be displayed on console/terminal.

### UnorderedList

Class [Lucinda\Console\UnorderedList](https://github.com/aherne/console/blob/master/src/UnorderedList.php) extends [AbstractList](#AbstractList) and creates an uordered list to be displayed on console/terminal.

### Wrapper

Class [Lucinda\Console\Wrapper](https://github.com/aherne/console/blob/master/src/Wrapper.php) compiles user-defined text using [Console Templating Language](#console-templating-language) by binding tags inside to their equivalent classes. It defines following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $body | void | Takes text received and compiles it |
| getBody | void | string | Gets compiled body, ready to be displayed on console/terminal |

If compilation fails, a [Lucinda\Console\Exception](https://github.com/aherne/console/blob/master/src/Exception.php) is thrown!