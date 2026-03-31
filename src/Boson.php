<?php

namespace DavidGut\Boson;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use InvalidArgumentException;

class Boson
{
    protected array $classes = [];

    protected array $attributes = [];

    protected string $element;

    protected string | null $prefix = null;

    protected static array $sizes = ['xs', 'sm', 'md', 'lg', 'xl'];

    public function __construct(string $element)
    {
        $this->element = $element;
    }

    /**
     * Create a Boson instance for an element.
     */
    public static function element(string | null $element = null, string $default = 'div'): static
    {
        return new static($element ?? $default);
    }

    /**
     * Create a Boson instance for a heading element (h1-h6 or div).
     */
    public static function heading(int | string | null $level = null): static
    {
        $element = is_numeric($level) && $level >= 1 && $level <= 6
            ? "h{$level}"
            : 'div';

        return new static($element);
    }

    /**
     * Set the base class and prefix for modifiers.
     * e.g. base('avatar') adds 'avatar' class and sets prefix to 'avatar-'
     */
    public function base(string $name, string $separator = '-'): static
    {
        $this->prefix = $name . $separator;
        $this->classes[] = $name;

        return $this;
    }

    /**
     * Add a modifier class using the base prefix.
     * No-op if suffix is null/empty.
     *
     * Usage:
     *   ->mod('lg')                         // adds {prefix}lg
     *   ->mod($size)                        // adds {prefix}{size} if $size is truthy
     *   ->when($isSquare, 'mod', 'square')  // conditional modifier
     */
    public function mod(string | null $suffix): static
    {
        if ($suffix && $this->prefix) {
            $this->classes[] = $this->prefix . $suffix;
        }

        return $this;
    }

    /**
     * Extract attributes with a given prefix, stripping the prefix.
     * e.g. extract($attributes, 'wrapper') extracts 'wrapper:class' as 'class'
     */
    public static function extract(ComponentAttributeBag $attributes, string $prefix): ComponentAttributeBag
    {
        $filtered = $attributes->whereStartsWith("{$prefix}:")->getAttributes();

        $mapped = [];
        foreach ($filtered as $key => $value) {
            $mapped[Str::after($key, "{$prefix}:")] = $value;
        }

        return new ComponentAttributeBag($mapped);
    }

    /**
     * Get attributes excluding those with given prefix(es).
     */
    public static function except(ComponentAttributeBag $attributes, string | array $prefixes): ComponentAttributeBag
    {
        $prefixes = (array) $prefixes;
        $patterns = array_map(fn ($p) => "{$p}:", $prefixes);

        return $attributes->filter(
            fn ($value, $key) => ! Str::startsWith($key, $patterns)
        );
    }


    /**
     * Add a class or array of classes.
     * Empty/whitespace-only classes are ignored.
     *
     * Usage:
     *   ->class('flex items-center')               // adds multiple classes
     *   ->class(['flex', 'items-center'])          // array syntax
     *   ->when($isActive, 'class', 'active')       // conditional class
     */
    public function class(string | array $classes): static
    {
        $classes = is_string($classes) ? explode(' ', $classes) : $classes;

        foreach ($classes as $class) {
            if ($class = trim($class)) {
                $this->classes[] = $class;
            }
        }

        return $this;
    }

    /**
     * Add a validated size modifier with a default value.
     */
    public function size(string | null $size = null, string $default = 'md'): static
    {
        $resolved = $size ?? $default;

        if (! in_array($resolved, static::$sizes, true)) {
            throw new InvalidArgumentException("Invalid size: {$resolved}");
        }

        return $this->mod($resolved);
    }

    /**
     * Add an attribute. No-op if value is null.
     *
     * Usage:
     *   ->attribute('type', 'text')                    // always adds
     *   ->attribute('title', $maybeNull)               // no-op if null
     *   ->when($condition, 'attribute', 'key', 'val')  // conditional attribute
     */
    public function attribute(string $key, mixed $value): static
    {
        if ($value !== null) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * Add a data attribute (auto-prefixes with 'data-').
     * Booleans are cast to 'true'/'false' strings.
     * No-op if value is null or empty string.
     *
     * Usage:
     *   ->data('id', $id)                          // adds data-id if $id is truthy
     *   ->data('active', true)                     // adds data-active="true"
     *   ->when($show, 'data', 'visible', 'yes')    // conditional data attribute
     */
    public function data(string $key, mixed $value): static
    {
        if ($value === null || $value === '') {
            return $this;
        }

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $this->attributes["data-{$key}"] = $value;

        return $this;
    }

    /**
     * Conditional builder for cleaner branching.
     *
     * Usage:
     *   ->when($condition, fn($el) => $el->class('active'))  // closure syntax
     *   ->when($disabled, 'flag', 'disabled')                // string method shorthand
     *   ->when($href, 'href', $href)                         // calls ->href($href) if $href is truthy
     */
    public function when(mixed $condition, callable | string $callback, mixed ...$args): static
    {
        if ($condition) {
            if (is_string($callback)) {
                $this->$callback(...$args);
            } else {
                $callback($this);
            }
        }

        return $this;
    }

    /**
     * Start a conditional block. Methods chained after only() are skipped if condition is falsy.
     * End the block with ->end() to return to normal execution.
     *
     * Usage:
     *   ->only($external)
     *       ->external()
     *       ->referrerPolicy($policy)
     *   ->end()
     */
    public function only(mixed $condition): BosonConditionalProxy | static
    {
        return $condition ? $this : new BosonConditionalProxy($this);
    }

    /**
     * End a conditional block started with only().
     * When condition was true, this is a no-op that maintains the chain.
     */
    public function end(): static
    {
        return $this;
    }

    /**
     * Shortcut for href attribute. No-op if null.
     */
    public function href(string | null $href): static
    {
        return $this->attribute('href', $href);
    }

    /**
     * Add external link attributes (target="_blank" with security headers).
     */
    public function external(): static
    {
        $this->attributes['target'] = '_blank';
        $this->attributes['rel'] = 'noopener noreferrer';

        return $this;
    }

    /**
     * Set the referrer policy attribute.
     */
    public function referrerPolicy(string | null $policy): static
    {
        if ($policy) {
            $this->attributes['referrerpolicy'] = $policy;
        }

        return $this;
    }

    /**
     * Shortcut for name attribute with array suffix support.
     */
    public function name(string | null $name, bool $array = false): static
    {
        if ($name) {
            $this->attributes['name'] = $array ? "{$name}[]" : $name;
        }

        return $this;
    }

    /**
     * Add a boolean HTML attribute flag (e.g. multiple, disabled, required).
     *
     * Usage:
     *   ->flag('disabled')                      // always adds disabled
     *   ->when($isDisabled, 'flag', 'disabled') // conditional flag
     */
    public function flag(string $name): static
    {
        $this->attributes[$name] = true;

        return $this;
    }

    /**
     * Set the loading attribute (lazy by default, eager if true).
     */
    public function loading(bool $eager = false): static
    {
        $this->attributes['loading'] = $eager ? 'eager' : 'lazy';

        return $this;
    }

    /**
     * Set the form method attribute (normalizes to GET or POST for HTML forms).
     */
    public function method(string $method): static
    {
        $upper = strtoupper($method);
        $this->attributes['method'] = $upper === 'GET' ? 'GET' : 'POST';

        return $this;
    }

    /**
     * Set the role attribute.
     */
    public function role(string | null $role): static
    {
        if ($role) {
            $this->attributes['role'] = $role;
        }

        return $this;
    }

    /**
     * Set the tabindex attribute.
     */
    public function tabindex(int $index): static
    {
        $this->attributes['tabindex'] = $index;

        return $this;
    }

    /**
     * Add an aria attribute (auto-prefixes with 'aria-').
     * Booleans are cast to 'true'/'false' strings.
     * No-op if value is null or empty string.
     *
     * Usage:
     *   ->aria('label', 'Close')                    // adds aria-label
     *   ->aria('expanded', false)                   // adds aria-expanded="false"
     *   ->when($condition, 'aria', 'hidden', true)  // conditional aria
     */
    public function aria(string $key, mixed $value): static
    {
        if ($value === null || $value === '') {
            return $this;
        }

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $this->attributes["aria-{$key}"] = $value;

        return $this;
    }

    /**
     * Apply Turbo data attributes from an extracted turbo:* attribute bag.
     * Maps each key to data-turbo-{key} (e.g. turbo:frame → data-turbo-frame).
     * Booleans are cast to 'true'/'false' strings.
     * No-op for null/empty values.
     *
     * Usage:
     *   $turboAttrs = Boson::extract($attributes, 'turbo');
     *   ->turbo($turboAttrs)
     */
    public function turbo(ComponentAttributeBag $turboAttrs): static
    {
        foreach ($turboAttrs->getAttributes() as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $this->attributes["data-turbo-{$key}"] = $value;
        }

        return $this;
    }

    /**
     * Dynamic attribute assignment.
     */
    public function __call(string $method, array $parameters): static
    {
        $value = $parameters[0] ?? true;

        return $this->attribute($method, $value);
    }

    public function getElement(): string
    {
        return $this->element;
    }

    public function getClasses(): array
    {
        return array_unique($this->classes);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getMergeAttributes(): array
    {
        return [
            'class' => $this->toClassString(),
            ...$this->attributes,
        ];
    }

    public function toClassString(): string
    {
        return implode(' ', $this->getClasses());
    }
}

/**
 * Proxy that absorbs method calls when a condition is falsy.
 * Used by Boson::only() for fluent conditional blocks.
 */
class BosonConditionalProxy
{
    public function __construct(private Boson $boson) {}

    /**
     * Absorb any method call and return self for chaining.
     */
    public function __call(string $method, array $args): static
    {
        return $this;
    }

    /**
     * End the conditional block and return to the Boson instance.
     */
    public function end(): Boson
    {
        return $this->boson;
    }
}