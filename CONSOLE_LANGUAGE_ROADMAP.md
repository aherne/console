# Console View Language Roadmap

## Vision

Lucinda Console should provide a small HTML-like view language designed specifically for terminal applications and console MVC frameworks.

The goal is not to reproduce all of HTML and CSS. The language should offer familiar markup while respecting terminal constraints such as character-cell layout, terminal width, color capabilities, redirected output, and limited interactivity.

Example:

```html
<view>
    <h1>Users</h1>

    <success>User created successfully.</success>

    <box title="Account" border="rounded" padding="1">
        <p><strong>Name:</strong> Lucian</p>
        <p><strong>Status:</strong> <badge color="green">ACTIVE</badge></p>
    </box>
</view>
```

## Design Principles

1. Use familiar HTML-like syntax without claiming full HTML compatibility.
2. Prefer semantic elements such as `<error>` over repeated low-level styling.
3. Produce readable output when ANSI styling is unavailable.
4. Keep parsing, layout, and rendering separate.
5. Make terminal width and display-cell width part of layout decisions.
6. Validate markup and report errors with line and column information.
7. Escape untrusted content and terminal control sequences by default.
8. Keep the language deterministic and easy to test.

## Proposed Architecture

The current compiler pipeline should eventually be replaced by four distinct stages.

### 1. Parser

The parser converts source markup into a document tree.

```text
Markup -> Tokens -> Document AST
```

Possible node types:

- `DocumentNode`
- `TextNode`
- `ElementNode`
- `TableNode`
- `ListNode`
- `BoxNode`

Each node should retain its source line and column so invalid markup can produce useful error messages.

Regex replacements are suitable for the current small language, but become unreliable with deeply nested tags, quoted attributes, escaping, and mixed content.

### 2. Validation

Validation checks the document tree against the language schema.

Examples:

- `<table>` may contain `<thead>` and `<tbody>`.
- `<tr>` may contain `<th>` or `<td>`.
- `color` must be a supported color value.
- `width` must contain a valid unit.
- Unknown tags and attributes should produce explicit errors.

### 3. Layout Engine

The layout engine determines:

- Available terminal width
- Text wrapping
- Padding and margins
- Column widths
- Alignment
- Truncation and overflow
- Unicode display width

It should produce a layout tree containing positioned lines or blocks, without directly emitting ANSI sequences.

### 4. Renderer

Renderers convert the layout tree into a target format.

Recommended render modes:

```php
$view->render(RenderMode::ANSI);
$view->render(RenderMode::PLAIN_TEXT);
$view->render(RenderMode::HTML);
```

Initial renderers should be:

- ANSI terminal renderer
- Plain-text renderer for logs, pipes, files, and tests

An HTML renderer could later allow the same MVC view to be previewed in a browser.

## Feature Proposals

### Semantic Text Elements

```html
<h1>Application Status</h1>
<h2>Database</h2>
<p>The connection is available.</p>

<success>Deployment completed.</success>
<info>A newer version is available.</info>
<warning>Disk space is low.</warning>
<error>Unable to connect.</error>

<code>composer install</code>
<kbd>Ctrl+C</kbd>
```

Semantic elements should obtain their visual appearance from the active theme.

### Complete ANSI Styling

Suggested text attributes:

- Bold
- Dim
- Italic
- Underline
- Blink
- Inverse
- Hidden
- Strikethrough
- Foreground color
- Background color

Suggested color formats:

```html
<span color="red">Standard color</span>
<span color="bright-blue">Bright color</span>
<span color="ansi-208">256-color palette</span>
<span color="#ff8800">True color</span>
<span color="rgb(255, 136, 0)">True color</span>
```

Color values should degrade according to terminal capabilities:

```text
24-bit color -> 256 colors -> 16 colors -> plain text
```

### Paragraphs and Text Flow

```html
<p width="60" align="center" wrap="word">
    Long text that is wrapped according to the available terminal width.
</p>
```

Useful attributes:

- `width`
- `min-width`
- `max-width`
- `align="left|center|right"`
- `wrap="word|character|none"`
- `overflow="wrap|clip|ellipsis"`
- `indent`

### Boxes and Panels

```html
<box title="Result" border="rounded" padding="1" width="60">
    <success>The operation completed successfully.</success>
</box>
```

Suggested borders:

- `none`
- `ascii`
- `single`
- `double`
- `rounded`
- `heavy`

Unicode borders should fall back to ASCII when Unicode output is unavailable.

### Horizontal Layout

```html
<columns gap="2">
    <column width="30%">
        Navigation
    </column>
    <column>
        Main content
    </column>
</columns>
```

This could support fixed widths, percentages, and flexible remaining space.

### Improved Tables

```html
<table border="rounded" width="100%">
    <columns>
        <column width="40%" align="left"/>
        <column align="right"/>
    </columns>
    <thead>
        <tr>
            <th>Name</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Primary account</td>
            <td>125.00</td>
        </tr>
    </tbody>
</table>
```

Recommended table features:

- `<th>` headers
- Column alignment
- Explicit and flexible widths
- Wrapped multiline cells
- Numeric alignment
- Border styles
- Optional zebra styling
- Empty-state content
- Optional `colspan`
- Terminal-width-aware shrinking

### Lists

```html
<ol marker="decimal">
    <li>Install dependencies</li>
    <li>
        Run checks
        <ul marker="bullet">
            <li>Lint</li>
            <li>Tests</li>
        </ul>
    </li>
</ol>
```

Possible markers:

- Decimal
- Alphabetic
- Roman numerals
- Bullet
- Dash
- Checkmark
- Custom marker

### Terminal-Specific Elements

```html
<hr/>
<br/>
<spacer lines="2"/>
<link href="https://example.com">Documentation</link>
<badge color="green">READY</badge>
<progress value="42" max="100" width="30"/>
```

OSC 8 hyperlinks should be emitted only when supported. Plain output should include the URL in a readable form.

Progress bars should have separate static and interactive rendering behavior. Redirected output must not contain cursor movement sequences.

### Themes and Classes

Views should express meaning while themes control appearance.

```html
<h1 class="page-title">Users</h1>
<p class="muted">No additional information.</p>
<span class="danger">Database unavailable</span>
```

Theme configuration could use PHP rather than implementing full CSS:

```php
$theme->define("page-title", [
    "foreground" => "cyan",
    "bold" => true,
    "margin-bottom" => 1,
]);

$theme->define("danger", [
    "foreground" => "red",
    "bold" => true,
]);
```

Themes could include:

- Default dark-terminal theme
- Light-terminal theme
- Monochrome theme
- Accessible high-contrast theme
- Test theme with ANSI disabled

### Responsive Views

```html
<show min-width="100">
    Detailed account information
</show>

<show max-width="99">
    Account summary
</show>
```

Responsive behavior should be based on renderer capabilities rather than global operating-system checks.

Useful capability values include:

- Terminal width and height
- ANSI support
- Color depth
- Unicode support
- Hyperlink support
- Interactive TTY status

### MVC View Composition

```html
<include view="partials/header"/>

<section name="content">
    <h1>Users</h1>
</section>
```

Recommended responsibilities:

- The MVC view layer resolves templates and includes.
- The console language parser handles markup only.
- Data interpolation is performed before or during view construction.
- Values are escaped by default.
- Raw markup requires an explicit trusted-value type.

Avoid embedding a complete programming language into templates. Conditions and loops can be handled by the MVC view engine or a deliberately small set of structural directives.

## Security and Reliability

### Escaping

User-provided content may contain ANSI, OSC, or cursor-control sequences. These should be removed or escaped by default.

```php
$view->text($untrustedValue);       // Escaped
$view->raw($trustedMarkup);         // Explicitly trusted
```

### Resource Limits

The parser and renderer should enforce reasonable limits for:

- Maximum nesting depth
- Maximum node count
- Maximum rendered width and height
- Maximum table dimensions
- Maximum input size

### Errors

Errors should identify the source location and reason:

```text
views/users.console:14:18
Invalid value "middle" for attribute "align".
Expected one of: left, center, right.
```

## Suggested Public API

```php
$environment = new ConsoleEnvironment(
    width: 120,
    colorDepth: ColorDepth::TRUE_COLOR,
    unicode: true,
    hyperlinks: true,
    interactive: true,
);

$view = $engine->compile($template);
$output = $view->render(
    data: ["user" => $user],
    environment: $environment,
    mode: RenderMode::ANSI,
);
```

Convenient MVC usage could remain simple:

```php
return $this->consoleView("users/list", [
    "users" => $users,
]);
```

## Implementation Roadmap

### Phase 1: Language Foundation

- Define the language grammar.
- Introduce tokenizer and AST nodes.
- Add source-positioned parser errors.
- Add strict tag and attribute validation.
- Preserve the existing API through an adapter where practical.

### Phase 2: Rendering Foundation

- Introduce terminal capability objects.
- Add separate ANSI and plain-text renderers.
- Centralize ANSI sequence generation.
- Escape control sequences in text values.
- Add snapshot-style rendering tests.

### Phase 3: Text and Semantics

- Support complete ANSI text attributes.
- Add 16-color, 256-color, and true-color values.
- Add headings, paragraphs, status elements, code, and badges.
- Add themes and semantic classes.

### Phase 4: Layout

- Implement terminal-width-aware wrapping.
- Add alignment, padding, margins, and overflow.
- Add boxes and horizontal rules.
- Add columns and responsive visibility.

### Phase 5: Structured Content

- Improve tables with headers, widths, alignment, and multiline cells.
- Expand list markers and nesting behavior.
- Add links and static progress bars.

### Phase 6: MVC Integration

- Add view resolution and includes.
- Define escaped values and trusted markup.
- Add framework-level themes and environment configuration.
- Add integration tests for full console responses.

### Phase 7: Interactive Components

- Add live progress bars and spinners.
- Add safe cursor movement and screen updates.
- Keep interactive rendering isolated from static document rendering.
- Ensure non-TTY output remains stable and readable.

## Testing Strategy

The project should test output at several levels:

1. Parser tests for valid and invalid markup.
2. AST tests independent of rendering.
3. Layout tests at multiple terminal widths.
4. ANSI renderer snapshots.
5. Plain-text renderer snapshots.
6. Unicode and wide-character table tests.
7. Capability fallback tests.
8. Security tests using embedded ANSI and OSC sequences.
9. MVC integration tests with real view files.

Representative environments:

```text
80 columns, no ANSI, ASCII only
80 columns, 16 colors, Unicode
120 columns, 256 colors, Unicode
160 columns, true color, hyperlinks
Redirected output with all interactive features disabled
```

## Scope Boundaries

The language should intentionally avoid:

- General browser HTML compatibility
- A complete CSS implementation
- JavaScript-like execution
- Browser layout concepts that do not translate to character cells
- Interactive cursor behavior inside the basic static renderer

A focused terminal document language will be easier to validate, document, theme, test, and integrate into an MVC framework than a partial browser engine.

## Recommended Next Step

The next implementation milestone should be a minimal AST parser supporting:

- `<view>`
- `<p>`
- `<span>`
- `<strong>`
- `<em>`
- `<u>`
- `<ol>` and `<ul>`
- `<table>`

It should render through both ANSI and plain-text renderers. This establishes the architecture before adding boxes, responsive layout, themes, or interactive components.
