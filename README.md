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
