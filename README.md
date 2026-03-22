# Boson

Minimal, well-designed, flexible Laravel Blade components.

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

Flash toast notifications from your controllers or middleware using the `Toast` helper:

```php
use DavidGut\Boson\Toast;

Toast::show('Something happened.');
Toast::success('Saved successfully!');
Toast::warning('Check your input.', 'Heads up');
Toast::danger('Something went wrong.');
```

All methods accept an optional `$heading` and `$duration` (in ms, default `5000`). Toasts are flashed to the session and rendered automatically by the `<x-boson::toast>` component.

### Events

Boson includes a declarative event system via `on:` attributes — no inline JavaScript needed in your Blade:

```blade
<form on:success="BosonToast.success('Saved!')">

<button on:click="console.log('clicked')">Click me</button>
```

Inside a handler, `$event` is the DOM event and `this` is the element. The system supports both native DOM events (`click`, `submit`, `keydown`, etc.) and custom Boson events (`success`, `error`, `open`, `close`, `change`, `select`, `deselect`, etc.).

You can also register custom events at runtime:

```js
import { BosonEvents } from '../../vendor/davidgut/boson/resources/js/boson.js';

BosonEvents.register('myevent');        // custom (dispatched as boson:myevent)
BosonEvents.register('scroll', true);   // native
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
