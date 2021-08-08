# Lucinda Console

This API was created to give an ability of styling console responses so they are easy to read and pleasurable to see. It does this in two steps:

1. Defining a platform to create and format texts via following classes:
    - **[Text](#Text)**: class encapsulating a text, able to be applied any of above three UNIX styling options: 
        - **[BackgroundColor](https://github.com/aherne/console_table/blob/master/src/BackgroundColor.php)**: enum encapsulating background colors UNIX console texts can have 
        - **[ForegroundColor](https://github.com/aherne/console_table/blob/master/src/ForegroundColor.php)**: enum encapsulating foreground colors UNIX console texts can have  
        - **[FontStyle](https://github.com/aherne/console_table/blob/master/src/FontStyle.php)**: enum encapsulating font styles UNIX console texts can have (eg: bold)
    - **[Table](#Table)**: class encapsulating a table, not able to include sub-tables
    - **[OrderedList](#OrderedList)**: class encapsulating an ordered list, able to contain leaves that point to other ordered lists
    - **[UnorderedList](#UnorderedList)**: class encapsulating a unordered list, able to contain leaves that point to other unordered lists
2. Defining a HTML-like templating engine that points to above structures behind the scenes, helping developers to implement console frontend without programming via following tags:
    - **[&lt;span&gt;](#span-tag)**: same as HTML tag but only supporting *style* attribute. Latter supports following CSS directives:
        - *font-weight*: value must be one of [FontStyle](https://github.com/aherne/console_table/blob/master/src/FontStyle.php) constant names
        - *background-color*: value must be one of [BackgroundColor](https://github.com/aherne/console_table/blob/master/src/BackgroundColor.php) constant names
        - *color*: value must be one of [ForegroundColor](https://github.com/aherne/console_table/blob/master/src/ForegroundColor.php) constant names
    - **[<table>](#table-tag)**: same as HTML tag but with following restrictions:
        - must have a <thead> child
        - must have a <tbody> child
        - any <tr> inside supports no attributes
        - any <td> inside supports only *style* attribute 
    - **[<ol>](#ol-tag)**: same as HTML tag but with following differences and restrictions:
        - can contain a <caption> tag defining what ordered list is about (behaves as <span>). If present it MUST be first child!
        - must contain <li> subtags supporting only *style* attribute
        - if <li> branches to another <ol>, latter must be the only child
    - **[<ul>](#ul-tag)**: same as HTML tag, with equivalent differences and restrictions as <ol>
    - **[<br>](#br-tag)**: same as HTML tag
3. Defining a class able to bind templated text at point #2 with structures at point #3 in order to build the final view:
     - **[Wrapper](#Wrapper)**: class encapsulating a table

API requires no dependency other than PHP 7.1+ interpreter and SimpleXML extension. All classes inside belong to **Lucinda\Console** interface!

## Example Usage

```php
// defines text to be compiled
$text = '
<span style="font-weight: bold">hello</span>
    
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
    <caption style="color: blue">Is Lucinda a genius?</caption>
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

## Templating Language



## Reference Guide

### Table

Class [Lucinda\Console\Table](https://github.com/aherne/console_table/blob/master/src/Table.php) draws a table on a unix console or windows command prompt, defining following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | array $columns | void | Sets table columns based on *string* or Lucinda\Console\Text array input |
| addRow | array $row | void | Adds a row to table based on *string* or Lucinda\Console\Text array input |
| display | void | void | Outputs table on console (or command prompt) |

### Text

Class [Lucinda\Console\Text](https://github.com/aherne/console_table/blob/master/src/Text.php) styles a UNIX console text, defining following public methods:


| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $text | void | Sets text to style |
| setFontStyle | [Lucinda\Console\FontStyle](https://github.com/aherne/console_table/blob/master/src/FontStyle.php) $style | void | Sets text style (eg: makes it bold) from input enum member. |
| setBackgroundColor | [Lucinda\Console\BackgroundColor](https://github.com/aherne/console_table/blob/master/src/BackgroundColor.php) $color | void | Sets text background color from input enum member. |
| setForegroundColor | [Lucinda\Console\ForegroundColor](https://github.com/aherne/console_table/blob/master/src/ForegroundColor.php) $color | void | Sets text foreground color from input enum member. |
| getOriginalValue | void | string | Gets original text before styling |
| getStyledValue | void | string | Gets final text after styling |
