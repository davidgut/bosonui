<?php

namespace DavidGut\Boson\Services;

class CompactRuleGenerator
{
    protected string $ide;
    protected bool $canary;

    public function __construct(string $ide = 'cursor', bool $canary = false)
    {
        $this->ide = $ide;
        $this->canary = $canary;
    }

    public function generate(array $components): string
    {
        $output = $this->generateFrontmatter();
        $output .= $this->generateCanary();
        $output .= $this->generateContent($components);

        return $output;
    }

    protected function generateCanary(): string
    {
        if ( ! $this->canary) {
            return '';
        }

        return <<<'CANARY'
Whenever reading this document, always begin your response with "I'm reading boson.mdc".


CANARY;
    }

    protected function generateFrontmatter(): string
    {
        if ($this->ide === 'cursor') {
            return <<<'FRONTMATTER'
---
description: Boson UI component library reference
globs: *.blade.php
---


FRONTMATTER;
        }

        return <<<'FRONTMATTER'
---
description: Boson UI component library reference
globs: *.blade.php
---


FRONTMATTER;
    }

    protected function generateContent(array $components): string
    {
        $content = "# Boson Components\n\n";
        $content .= "Blade UI library. Use `<x-boson::name>` syntax.\n\n";

        foreach ($components as $name => $info) {
            $content .= $this->generateComponentEntry($name, $info);
        }

        return $content;
    }

    protected function generateComponentEntry(string $name, array $info): string
    {
        $entry = "{$name}:\n";

        if ( ! empty($info['description'])) {
            $entry .= "  desc: {$info['description']}\n";
        }

        if ( ! empty($info['usage'])) {
            $entry .= "  usage: {$info['usage']}\n";
        }

        if ( ! empty($info['props'])) {
            $entry .= "  props: " . implode(', ', $info['props']) . "\n";
        }

        if ( ! empty($info['prefixes'])) {
            $prefixes = is_array($info['prefixes'])
                ? implode(', ', $info['prefixes'])
                : $info['prefixes'];
            $entry .= "  prefixes: {$prefixes}\n";
        }

        if ( ! empty($info['variants'])) {
            $entry .= "  variants: {$info['variants']}\n";
        }

        if ( ! empty($info['sizes'])) {
            $entry .= "  sizes: {$info['sizes']}\n";
        }

        if ( ! empty($info['positions'])) {
            $entry .= "  positions: {$info['positions']}\n";
        }

        if ( ! empty($info['placements'])) {
            $entry .= "  placements: {$info['placements']}\n";
        }

        if ( ! empty($info['shapes'])) {
            $entry .= "  shapes: {$info['shapes']}\n";
        }

        if ( ! empty($info['colors'])) {
            $entry .= "  colors: {$info['colors']}\n";
        }

        if ( ! empty($info['ratios'])) {
            $entry .= "  ratios: {$info['ratios']}\n";
        }

        if ( ! empty($info['subcomponents'])) {
            $entry .= "  subs: " . implode(', ', $info['subcomponents']) . "\n";
        }

        if ( ! empty($info['dependencies'])) {
            $entry .= "  uses: " . implode(', ', $info['dependencies']) . "\n";
        }

        if ( ! empty($info['js'])) {
            $entry .= "  js: {$info['js']}\n";
        }

        $entry .= "\n";

        return $entry;
    }

    public function write(string $outputPath, string $content): void
    {
        $dir = dirname($outputPath);

        if ( ! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($outputPath, $content);
    }
}
