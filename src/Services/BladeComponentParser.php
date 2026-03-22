<?php

namespace DavidGut\Boson\Services;

class BladeComponentParser
{
    protected string $componentsPath;
    protected string $cssPath;
    protected string $jsPath;

    public function __construct(string $basePath)
    {
        $this->componentsPath = $basePath . '/resources/views/components';
        $this->cssPath = $basePath . '/resources/css/components';
        $this->jsPath = $basePath . '/resources/js/components';
    }

    public function all(): array
    {
        $components = [];

        foreach ($this->getComponentDirectories() as $directory) {
            $name = basename($directory);
            $parsed = $this->parse($name);

            if ($parsed !== null) {
                $components[$name] = $parsed;
            }
        }

        ksort($components);

        return $components;
    }

    public function parse(string $componentName): array | null
    {
        $componentPath = $this->componentsPath . '/' . $componentName;
        $bladeFile = $componentPath . '/index.blade.php';

        if ( ! file_exists($bladeFile)) {
            return null;
        }

        $content = file_get_contents($bladeFile);

        $annotatedPrefixes = $this->extractAnnotation($content, 'prefixes');

        return [
            'name' => $componentName,
            'description' => $this->extractAnnotation($content, 'description'),
            'usage' => $this->extractAnnotation($content, 'usage'),
            'props' => $this->extractProps($content),
            'prefixes' => $annotatedPrefixes ?? $this->formatPrefixes($this->extractPrefixes($content)),
            'dependencies' => $this->extractDependencies($content, $componentName),
            'subcomponents' => $this->findSubcomponents($componentPath, $componentName),
            'variants' => $this->extractAnnotation($content, 'variants'),
            'sizes' => $this->extractAnnotation($content, 'sizes'),
            'positions' => $this->extractAnnotation($content, 'positions'),
            'placements' => $this->extractAnnotation($content, 'placements'),
            'shapes' => $this->extractAnnotation($content, 'shapes'),
            'colors' => $this->extractAnnotation($content, 'colors'),
            'ratios' => $this->extractAnnotation($content, 'ratios'),
            'cssBase' => $this->extractCssBase($content),
            'css' => $this->findCss($componentName),
            'js' => $this->findJs($componentName),
        ];
    }

    protected function extractAnnotation(string $content, string $name): string | null
    {
        if (preg_match('/\{\{--\s*.*?@' . $name . '\s+(.+?)(?:\n|--\}\})/s', $content, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    protected function extractProps(string $content): array
    {
        if ( ! preg_match('/@props\(\s*\[(.*?)\]\s*\)/s', $content, $matches)) {
            return [];
        }

        $propsString = $matches[1];
        $props = [];

        preg_match_all("/['\"]([^'\"]+)['\"]\s*(?:=>\s*([^,\]]+))?/", $propsString, $propMatches, PREG_SET_ORDER);

        foreach ($propMatches as $match) {
            $propName = $match[1];
            $props[] = $propName;
        }

        return $props;
    }

    protected function extractPrefixes(string $content): array
    {
        $prefixes = [];

        preg_match_all('/Boson::extract\(\s*\$\w+,\s*[\'"](\w+)[\'"]\s*\)/', $content, $matches);

        foreach ($matches[1] as $prefix) {
            if ( ! in_array($prefix, $prefixes)) {
                $prefixes[] = $prefix;
            }
        }

        return $prefixes;
    }

    protected function formatPrefixes(array $prefixes): string | null
    {
        if (empty($prefixes)) {
            return null;
        }

        return implode(', ', $prefixes);
    }

    protected function extractDependencies(string $content, string $currentComponent): array
    {
        $deps = [];

        preg_match_all('/<x-boson::([a-z][a-z0-9.-]*)/', $content, $matches);

        foreach ($matches[1] as $dep) {
            $baseDep = explode('.', $dep)[0];

            if ($baseDep !== $currentComponent && ! in_array($baseDep, $deps)) {
                $deps[] = $baseDep;
            }
        }

        sort($deps);

        return $deps;
    }

    protected function extractCssBase(string $content): string | null
    {
        if (preg_match('/->base\(\s*[\'"]([a-z-]+)[\'"]\s*\)/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function findSubcomponents(string $componentPath, string $componentName): array
    {
        $subs = [];

        if ( ! is_dir($componentPath)) {
            return $subs;
        }

        $files = glob($componentPath . '/*.blade.php');

        foreach ($files as $file) {
            $filename = basename($file);
            $subName = str_replace('.blade.php', '', $filename);

            if ($subName !== 'index') {
                $subs[] = "{$componentName}.{$subName}";
            }
        }

        sort($subs);

        return $subs;
    }

    protected function findCss(string $componentName): string | null
    {
        $cssFile = $this->cssPath . '/' . $componentName . '.css';

        return file_exists($cssFile) ? "resources/css/components/{$componentName}.css" : null;
    }

    protected function findJs(string $componentName): string | null
    {
        $jsFile = $this->jsPath . '/' . $componentName . '.js';

        return file_exists($jsFile) ? "resources/js/components/{$componentName}.js" : null;
    }

    protected function getComponentDirectories(): array
    {
        if ( ! is_dir($this->componentsPath)) {
            return [];
        }

        return glob($this->componentsPath . '/*', GLOB_ONLYDIR);
    }
}
