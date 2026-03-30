# Boson

Minimal, well-designed, flexible Laravel Blade components. Turbo-ready out of the box.

## Requirements

- PHP 8.2+
- Laravel 12.0+

## Installation

```bash
composer require davidgut/boson
```

The package auto-registers its service provider via Laravel's package discovery.

### Include CSS & JS

Import the Boson stylesheet and script in your application's entry points:

```css
/* resources/css/app.css */
@import '../../vendor/davidgut/boson/resources/css/boson.css';
```

```js
// resources/js/app.js
import '../../vendor/davidgut/boson/resources/js/boson.js';
```

Then compile your assets as usual with Vite (or your bundler of choice).

## Usage

All components are available under the `boson::` namespace:

```blade
<x-boson::button>Click me</x-boson::button>

<x-boson::card>Content goes here.</x-boson::card>

<x-boson::input label="Email" type="email" name="email" />
```

### Available Components

Accordion, Avatar, Badge, Button, Card, Checkbox, Combobox, Description, Dropdown, Error, Field, Form, Heading, Icon, Img, Input, Label, Link, Listbox, Modal, Navbar, Radio, Select, Separator, Spacer, Table, Textarea, Toast.

### Toasts

Add `<x-boson::toast />` once in your layout. Then flash toasts from PHP or trigger them from JavaScript.

**PHP** — flash via the `Toast` helper in controllers or middleware:

```php
use DavidGut\Boson\Toast;

Toast::show('Something happened.');
Toast::success('Saved successfully!');
Toast::warning('Check your input.', 'Heads up');
Toast::danger('Something went wrong.');
```

All methods accept an optional `$heading` and `$duration` (in ms, default `5000`).

**JavaScript** — trigger toasts client-side via the global `$toast` helper:

```js
$toast.show('Something happened.');
$toast.success('Saved!');
$toast.warning({ heading: 'Heads up', text: 'Check your input.' });
$toast.danger({ heading: 'Error', text: 'Something went wrong.', duration: 8000 });
```

You can also dismiss a toast programmatically:

```js
const toast = $toast.success('Done!');
$toast.dismiss(toast);
```

### Fetch Forms

Add `fetch` to `<x-boson::form>` for JavaScript-powered submission. Response handling is automatic:

| Controller returns | Boson does |
|---|---|
| `redirect('/dashboard')` | Navigates normally |
| `response()->json(['data' => $model])` | Updates all `[data-field]` elements in-place, resets the form, closes the parent modal |
| `422` validation response | Populates matching `<x-boson::error>` components |

```blade
<x-boson::form fetch action="/users" method="POST">
```

Without `fetch`, the form submits normally (Turbo handles it if installed, browser handles it otherwise).

**GET forms** send form data as URL query parameters automatically.

**In-page updates** support dot-notation for nested data:

```php
return response()->json([
    'data' => ['team' => ['name' => 'Acme']]
]);
```

```blade
<span data-field="team.name">Old Name</span>  {{-- updates to "Acme" --}}
```

### Events

Boson provides a declarative event system via `on:` attributes — write inline expressions directly in Blade:

```blade
<form on:success="$toast.success('Saved!')">

<button on:click="console.log('clicked')">Click me</button>

<form on:success="$('#badge').text($data.status).data('color', $match($data.status, { active: 'green', pending: 'gray' }))">
```

Inside a handler, these helpers are available:

**`$event`** — the raw DOM event:

```blade
<button on:click="console.log($event.target.tagName)">Inspect</button>
```

**`$data`** — shorthand for `$event.detail.data` (the response payload on form success):

```blade
<form on:success="console.log($data.id, $data.name)">
```

**`$(selector)`** — chainable DOM helper for updating page elements without a reload:

```blade
{{-- Update text content --}}
<form on:success="$('#user-name').text($data.name)">

{{-- Set a data attribute (e.g. for CSS-driven badge colors) --}}
<form on:success="$('#status').text($data.status).data('color', 'green')">

{{-- Toggle visibility --}}
<button on:click="$('#panel').toggle()">
```

Available methods: `.text(value)`, `.class(name, force?)`, `.data(key, value)`, `.attr(key, value)`, `.toggle()`.

**`$match(value, map, fallback?)`** — value lookup, like PHP's `match`:

```blade
<form on:success="$('#badge').data('color', $match($data.status, { active: 'green', pending: 'yellow', archived: 'gray' }, 'red'))">
```

**`$toast`** — trigger toast notifications:

```blade
<form on:success="$toast.success('Saved!')">
<button on:click="$toast.danger({ heading: 'Error', text: 'Something went wrong.' })">
```

**`this`** — the element that owns the `on:` attribute:

```blade
<button on:click="this.textContent = 'Clicked!'">Click me</button>
```

The system supports both native DOM events (`click`, `submit`, `keydown`, etc.) and custom Boson events (`success`, `error`, `open`, `close`, `change`, `select`, `deselect`).

Register custom events at runtime:

```js
import { $events } from '../../vendor/davidgut/boson/resources/js/boson.js';

$events.register('myevent');        // custom (dispatched as boson:myevent)
$events.register('scroll', true);   // native
```

### Component Data Attributes

All interactive Boson components follow a consistent `data-controller` / `data-{name}-target` convention:

```html
<!-- Root element identifies the component -->
<div data-controller="dropdown">
    <!-- Children declare their role within the component -->
    <button data-dropdown-target="trigger">Toggle</button>
    <div data-dropdown-target="menu">...</div>
</div>
```

Access component instances programmatically via `el.boson`:

```js
const el = document.querySelector('[data-controller="modal"]');
el.boson.open();
el.boson.close();
el.boson.destroy();  // removes all event listeners
```

| Component | Controller |
|---|---|
| Accordion | `accordion` |
| Combobox | `combobox` |
| Dropdown | `dropdown` |
| Form | `form` |
| Listbox | `listbox` |
| Modal | `modal` |
| Navlist | `navlist` |
| Sidebar | `sidebar` |
| Toast | `toast` |

### Turbo Compatibility

Boson works out of the box with [Turbo Laravel](https://turbo-laravel.com/) — no extra configuration needed.

The internal lifecycle system automatically handles:

- **Turbo Drive** — components re-initialize after every page navigation and clean up before Turbo caches the page
- **Turbo Frames** — components inside frames initialize when the frame loads new content
- **Turbo Streams** — dynamically inserted components are initialized automatically via `MutationObserver`

If your app doesn't use Turbo, nothing changes — components initialize on `DOMContentLoaded` as usual.

**Building custom components?** Follow the same protocol to get Turbo compatibility for free:

```js
import { lifecycle } from '../../vendor/davidgut/boson/resources/js/core/lifecycle.js';

class MyComponent {
    constructor(element) {
        this.element = element;
        this.abortController = new AbortController();
        // bind events with { signal: this.abortController.signal }
    }

    destroy() {
        this.abortController.abort();
    }
}

lifecycle.register('my-component', MyComponent);
```

Then in Blade:

```html
<div data-controller="my-component">...</div>
```


## AI Rules Generation

Boson can generate a compact `.mdc` context file containing all component documentation, props, and usage examples — designed to be consumed by AI coding assistants.

```bash
php artisan boson:rules
```

This parses every Boson component's `@description`, `@usage`, and props, then writes a single `boson.mdc` file to your IDE's rules directory.

### Options

| Option | Default | Description |
|--------|---------|-------------|
| `--ide=` | `cursor` | Target IDE: `cursor` (writes to `.cursor/rules/`) or `antigravity` (writes to `.agent/rules/`) |
| `--output=` | *(auto)* | Custom output directory |
| `--canary` | `false` | Include a verification string to test context loading |

The canary flag can also be enabled globally via the `boson.rules.canary` config option.

## Publishing

Publish the config file:

```bash
php artisan vendor:publish --tag=boson-config
```

Publish the views for customization:

```bash
php artisan vendor:publish --tag=boson-views
```

## License

MIT — see [LICENSE](LICENSE) for details.
