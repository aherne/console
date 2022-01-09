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
    - **[&lt;span&gt;](#span-tag)**: same as HTML tag
    - **[&lt;u&gt;](#u-tag)**: same as HTML tag
    - **[&lt;b&gt;](#b-tag)**: same as HTML tag
    - **[&lt;i&gt;](#i-tag)**: same as HTML tag
3. Defining a class able to bind templated text at point #2 with structures at point #3 in order to build the final view:
     - **[Wrapper](#Wrapper)**: class encapsulating a table

API requires no dependency other than PHP 8.1+ interpreter and SimpleXML extension. All classes inside belong to **Lucinda\Console** interface!

## Example Usage

```php
// defines text to be compiled
$text = '
<div style="font-style: bold">hello</div>
    
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

- *font-style*: value must be one of [FontStyle](https://github.com/aherne/console/blob/master/src/FontStyle.php) constant names
- *background-color*: value must be one of [BackgroundColor](https://github.com/aherne/console/blob/master/src/BackgroundColor.php) constant names
- *color*: value must be one of [ForegroundColor](https://github.com/aherne/console/blob/master/src/ForegroundColor.php) constant names
    
### Div Tag

Binding to **[Text](#Text)**, works the same as HTML &lt;div&gt; tag with following restrictions:

- only supporting *style* attribute
- body can only contain plain text or/and [&lt;span&gt;](#span-tag), [&lt;u&gt;](#u-tag), [&lt;b&gt;](#b-tag), [&lt;i&gt;](#i-tag) tags

Syntax example:

```html
<div style="background-color: red">Hello, <b>world</b>!</div>
```

### Table Tag

Binding to **[Table](#Table)**, works the same as HTML &lt;table&gt; tag with following restrictions:

- must have a &lt;thead&gt; child
- must have a &lt;tbody&gt; child
- any &lt;tr&gt; inside supports no attributes
- any &lt;td&gt; inside supports only *style* attribute
- any &lt;td&gt; body can only contain plain text

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

- can contain a &lt;caption&gt; tag defining what list is about (behaving as **[&lt;div&gt;](#div-tag)**). 
- if a &lt;caption&gt; is present it MUST be first child!
- must contain &lt;li&gt; sub-tags supporting only *style* attribute
- any &lt;li&gt; body can only contain one of below:
   - plain text or/and [&lt;span&gt;](#span-tag), [&lt;u&gt;](#u-tag), [&lt;b&gt;](#b-tag), [&lt;i&gt;](#i-tag) tags
   - another &lt;ol&gt;/&lt;ul&gt; tag

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

### Span Tag

Works the same as HTML &lt;span&gt; with following restrictions:

- supports only *style* attribute
- plain text or/and [&lt;u&gt;](#u-tag), [&lt;b&gt;](#b-tag), [&lt;i&gt;](#i-tag) tags
- can only occur inside a [&lt;div&gt;](#div-tag) or &lt;caption&gt;

Example:

```html
<div>Hello, <span style="background-color: BLUE">Lucian</span>!</div>
```

### B Tag

Works the same as HTML &lt;b&gt;  with same restrictions as [&lt;span&gt;](#span-tag) tag! Equivalent to:

```html
<span style="font-style: bold">Lucian</span>
```

### U Tag

Works the same as HTML &lt;u&gt;  with same restrictions as [&lt;span&gt;](#span-tag) tag! Equivalent to:

```html
<span style="font-style: underline">Lucian</span>
```

^ Note the difference from HTML *text-decoration: underline*

### I Tag

Works the same as HTML &lt;i&gt;  with same restrictions as [&lt;span&gt;](#span-tag) tag!  Equivalent to:

```html
<span style="font-style: italic">Lucian</span>
```

^ Note the difference from HTML *font-style: italic*

## Reference Guide

### Text

Class [Lucinda\Console\Text](https://github.com/aherne/console/blob/master/src/Text.php) implements [Stringable](https://www.php.net/manual/en/class.stringable.php) and styles a UNIX console text, defining following public methods:

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

Class [Lucinda\Console\Table](https://github.com/aherne/console/blob/master/src/Table.php) implements [Stringable](https://www.php.net/manual/en/class.stringable.php) and creates a table to be displayed on console/terminal, defining following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | array $columns | void | Sets table columns based on *string* or *[Text](#Text)* array input |
| addRow | array $row | void | Adds a row to table based on *string* or *[Text](#Text)* array input |
| toString | void | string | Gets final string representation of table to be shown on console/terminal |

### AbstractList

Abstract class [Lucinda\Console\AbstractList](https://github.com/aherne/console/blob/master/src/AbstractList.php) implements [Stringable](https://www.php.net/manual/en/class.stringable.php) and creates a list to be displayed on console/terminal, defining following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | int $indent = 0 | void | Constructs a list by number of spaces to indent in members (default=5) |
| setCaption | string\|Text $caption | void | Sets optional caption to define what list is about based on *string* or *[Text](#Text)* input |
| addItem | string\|Text $item | void | Adds a textual member to list based on *string* or *[Text](#Text)* input. |
| addList | [AbstractList](#AbstractList) | void | Adds a [AbstractList](#AbstractList) member to list |
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
