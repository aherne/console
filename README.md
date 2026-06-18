# Lucinda Console

Lucinda Console renders HTML-like console markup into terminal output.

It is not a template engine. It does not bind variables, resolve includes, or produce browser HTML. A framework or view layer should aggregate the final markup first, then pass that markup to this package for console rendering.

## Requirements

- PHP 8.1+
- `ext-mbstring`

## Installation

```bash
composer require lucinda/console
```

## Basic Usage

Use `Wrapper` when you want the default terminal environment to be detected automatically:

```php
use Lucinda\Console\Wrapper;

$view = new Wrapper(
    '<success>User created successfully.</success>'
);

$view->display();
```

Use `Engine` when you need direct control over the terminal environment:

```php
use Lucinda\Console\Engine;
use Lucinda\Console\RenderMode;
use Lucinda\Console\Terminal\EnvironmentDetector;

$markup = <<<'CONSOLE'
<view>
    <h1>Users</h1>
    <box title="Account" border="rounded" padding="1" width="60">
        <p><strong>Name:</strong> Lucian</p>
        <p><strong>Status:</strong> <badge color="green">ACTIVE</badge></p>
    </box>
</view>
CONSOLE;

$environment = (new EnvironmentDetector())->getResults();

echo (new Engine())->render($markup, $environment, RenderMode::ANSI);
```

## Rendering Modes

There are two rendering modes:

- `RenderMode::ANSI`: emits ANSI escape sequences for supported styling.
- `RenderMode::PLAIN_TEXT`: renders the same layout without ANSI styling.

Plain text is useful for logs, files, tests, or output targets where terminal control sequences are not wanted.

```php
use Lucinda\Console\Engine;
use Lucinda\Console\RenderMode;
use Lucinda\Console\Terminal\EnvironmentDetector;

$environment = (new EnvironmentDetector())->getResults();
$engine = new Engine();

$ansi = $engine->render('<error>Failure</error>', $environment, RenderMode::ANSI);
$plain = $engine->render('<error>Failure</error>', $environment, RenderMode::PLAIN_TEXT);
```

## Terminal Environment

`EnvironmentDetector` detects:

- terminal width and height
- color depth
- Unicode support
- OSC 8 hyperlink support
- whether output is interactive

The result is stored in `Lucinda\Console\Terminal\EnvironmentDetector\Results`.

For deterministic output, construct `Results` manually:

```php
use Lucinda\Console\Terminal\ColorDepth;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

$environment = new Results();
$environment->setWidth(80);
$environment->setHeight(24);
$environment->setColorDepth(ColorDepth::ANSI16);
$environment->setUnicode(true);
$environment->setHyperlinks(false);
$environment->setInteractive(false);
```

## Markup Overview

The root element is usually `view`, but any valid block element can be rendered.

```html
<view>
    <h1>Application Status</h1>
    <p>The application is running.</p>
</view>
```

Unknown tags and unsupported attributes are rejected during parsing.

## Text Elements

Supported block and semantic text tags:

```html
<h1>Application Status</h1>
<h2>Database</h2>
<h3>Connection</h3>

<p>Normal paragraph.</p>
<success>Deployment completed.</success>
<info>A newer version is available.</info>
<warning>Disk space is low.</warning>
<error>Unable to connect.</error>
```

Supported inline tags:

```html
<p>
    <strong>Bold</strong>,
    <em>italic</em>,
    <u>underline</u>,
    <s>strikethrough</s>,
    <code>code</code>,
    <kbd>Ctrl+C</kbd>,
    <badge>READY</badge>
</p>
```

Aliases:

- `strong` and `b`
- `em` and `i`

## Styling

Styles can be declared as attributes:

```html
<span color="bright-blue" bold underline>Styled text</span>
<span color="ansi-208">256-color text</span>
<span color="#ff8800" background="rgb(20,20,20)">True color</span>
```

Or through a limited `style` attribute:

```html
<p style="align: center; color: cyan">Centered text</p>
```

Supported style properties:

- `color`, `background`
- `bold`, `dim`, `italic`, `underline`, `blink`, `inverse`, `hidden`, `strikethrough`
- `width`, `min-width`, `max-width`
- `align`, `vertical-align`
- `wrap`, `overflow`
- `indent`, `padding`, `margin`, `margin-top`, `margin-bottom`

Valid values:

- `align`: `left`, `center`, `right`
- `vertical-align`: `top`, `middle`, `bottom`
- `wrap`: `word`, `character`, `none`
- `overflow`: `wrap`, `clip`, `ellipsis`

Supported colors:

- named ANSI colors: `red`, `green`, `bright-blue`, etc.
- `default`
- `ansi-0` through `ansi-255`
- hex colors: `#ff8800`
- RGB colors: `rgb(255, 136, 0)`

Color output degrades according to the configured terminal color depth.

## Themes

`Theme` provides default styles for semantic tags and CSS-like classes.

```php
use Lucinda\Console\Engine;
use Lucinda\Console\Styling\Theme;

$theme = (new Theme())->define("notice", [
    "color" => "bright-cyan",
    "bold" => true
]);

$engine = new Engine(theme: $theme);
```

Built-in theme variants:

```php
$theme = (new Theme())->withLightColors();
$theme = (new Theme())->withoutColors();
$theme = (new Theme())->withHighContrast();
```

Use classes in markup:

```html
<p class="notice">Important message</p>
```

## Layout

### Wrapping

```html
<p width="30" wrap="word">
    Text wraps on word boundaries.
</p>

<p width="10" wrap="character">
    Text wraps by character.
</p>

<p width="20" wrap="none" overflow="ellipsis">
    This line will be truncated.
</p>
```

### Spacing

```html
<p margin-bottom="1">First paragraph</p>
<p indent="4" padding="1">Indented paragraph</p>
<spacer lines="2"/>
```

### Responsive Blocks

```html
<show min-width="100">
    <p>Detailed output for wide terminals.</p>
</show>

<show max-width="99">
    <p>Compact output.</p>
</show>
```

## Boxes

```html
<box title="Result" border="rounded" padding="1" width="60">
    <success>The operation completed successfully.</success>
</box>
```

Supported borders:

- `none`
- `ascii`
- `single`
- `double`
- `rounded`
- `heavy`

Unicode borders fall back to ASCII when the environment does not support Unicode.

## Columns

```html
<columns gap="2">
    <column width="30%" vertical-align="top">
        <p>Navigation</p>
    </column>
    <column>
        <p>Main content</p>
    </column>
</columns>
```

Columns support fixed widths, percentages, and flexible remaining width.

## Tables

```html
<table border="rounded" width="100%" zebra>
    <column width="40%"/>
    <column/>
    <thead>
        <tr>
            <th>Name</th>
            <th align="right">Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Primary account</td>
            <td align="right">125.00</td>
        </tr>
        <tr>
            <td colspan="2">Shared note</td>
        </tr>
    </tbody>
</table>
```

Tables support:

- `thead` and `tbody`
- `tr`, `th`, `td`
- optional `column` width definitions
- `colspan`
- alignment
- border styles
- `zebra`
- `empty`

## Lists

```html
<ol marker="roman" start="4">
    <li>Install dependencies</li>
    <li>
        Run checks
        <ul marker="checkmark">
            <li>Static analysis</li>
            <li>Unit tests</li>
        </ul>
    </li>
</ol>
```

Supported markers:

- `decimal`
- `alphabetic`
- `roman`
- `bullet`
- `dash`
- `checkmark`
- any custom marker string

## Links

```html
<link href="https://example.com">Documentation</link>
```

In ANSI mode, links use OSC 8 when the environment supports hyperlinks. In plain-text mode, the URL is appended after the text:

```text
Documentation (https://example.com)
```

## Progress and Spinner

Static markup:

```html
<progress value="42" max="100" width="30"/>
<spinner label="Loading"/>
```

Interactive helpers:

```php
use Lucinda\Console\Engine;
use Lucinda\Console\Interactive\LiveProgress;
use Lucinda\Console\Interactive\LiveSpinner;

$progress = new LiveProgress(new Engine(), max: 100);
$progress->update(50, "Half done");
$progress->finish(100, "Done");

$spinner = new LiveSpinner(label: "Loading");
$spinner->tick();
$spinner->finish("Done");
```

Interactive helpers require an interactive terminal and reject redirected output.

## Validation and Limits

The parser validates the markup before rendering:

- unknown tags are rejected
- unsupported attributes are rejected
- invalid colors and dimensions are rejected
- invalid table/list structure is rejected
- parser errors include line and column
- input size is limited to 1 MB
- token count is limited to 10,000
- nesting depth is limited to 100

Text content is decoded from HTML entities and terminal control sequences are stripped before output.

## Testing

Run unit tests:

```bash
composer test
```

Run static analysis:

```bash
composer analyse
```

The test suite is generated and executed with `lucinda/unit-testing`.
