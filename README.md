# Lucinda Console

This API was created to give an ability of styling console responses of PHP libraries such as unit tests or migrations so they are easy to read and pleasurable to see. It does this via 5 classes, all members of **Lucinda\Console** interface:

- **[BackgroundColor](https://github.com/aherne/console_table/blob/master/src/BackgroundColor.php)**: enum encapsulating background colors a UNIX console text can have 
- **[ForegroundColor](https://github.com/aherne/console_table/blob/master/src/ForegroundColor.php)**: enum encapsulating foreground colors a UNIX console text can have  
- **[FontStyle](https://github.com/aherne/console_table/blob/master/src/FontStyle.php)**: enum encapsulating font styles a UNIX console text can have (eg: bold) 
- **[Text](#Text)**: class able to apply any of above three UNIX styling options to a given text 
- **[Table](#Table)**: class able to display tables on both UNIX console or WINDOWS command prompt

Usage example for UNIX console display:

```php
$failed = new Lucinda\Console\Text(" FAILED ");
$failed->setBackgroundColor(Lucinda\Console\BackgroundColor::RED);

$success = new Lucinda\Console\Text(" PASSED ");
$success->setBackgroundColor(Lucinda\Console\BackgroundColor::GREEN);

$table = new Lucinda\Console\Table(array_map(function ($column){
    $text = new Lucinda\Console\Text($column);
    $text->setFontStyle(Lucinda\Console\FontStyle::BOLD);
    return $text;
}, ["Class", "Status", "Message"]));
$table->addRow(["Foo\\Bar", $success, "this is good"]);
$table->addRow(["Foo\\Baz", $failed, "this is bad"]);
$table->display();
```

Usage example for Windows command prompt display:

```php
$table = new Lucinda\Console\Table(["Class", "Status", "Message"]);
$table->addRow(["Foo\\Bar", "PASSED", "this is good"]);
$table->addRow(["Foo\\Baz", "FAILED", "this is bad"]);
$table->display();
```

No styling is available on Windows command prompt, as you can see, but you may use GIT BASH or any equivalent for a better experience! 

API has no dependency and only requires PHP 7.1 interpreter. Unit tests are not functionaly possible, but two tests implementing examples above have been included (test-unix.php & test-windows.php).

## Table

Class [Lucinda\Console\Table](https://github.com/aherne/console_table/blob/master/src/Table.php) draws a table on a unix console or windows command prompt, defining following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | array $columns | void | Sets table columns based on *string* or Lucinda\Console\Text array input |
| addRow | array $row | void | Adds a row to table based on *string* or Lucinda\Console\Text array input |
| display | void | void | Outputs table on console (or command prompt) |

## Text

Class [Lucinda\Console\Text](https://github.com/aherne/console_table/blob/master/src/Text.php) styles a UNIX console text, defining following public methods:


| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $text | void | Sets text to style |
| setFontStyle | [Lucinda\Console\FontStyle](https://github.com/aherne/console_table/blob/master/src/FontStyle.php) $style | void | Sets text style (eg: makes it bold) from input enum member. |
| setBackgroundColor | [Lucinda\Console\BackgroundColor](https://github.com/aherne/console_table/blob/master/src/BackgroundColor.php) $color | void | Sets text background color from input enum member. |
| setForegroundColor | [Lucinda\Console\ForegroundColor](https://github.com/aherne/console_table/blob/master/src/ForegroundColor.php) $color | void | Sets text foreground color from input enum member. |
| getOriginalValue | void | string | Gets original text before styling |
| getStyledValue | void | string | Gets final text after styling |
