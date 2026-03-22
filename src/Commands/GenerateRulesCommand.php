<?php

namespace DavidGut\Boson\Commands;

use DavidGut\Boson\Services\BladeComponentParser;
use DavidGut\Boson\Services\CompactRuleGenerator;
use Illuminate\Console\Command;

class GenerateRulesCommand extends Command
{
    protected $signature = 'boson:rules
                            {--output= : Output directory for rule files}
                            {--ide=cursor : Target IDE (cursor or antigravity)}
                            {--canary : Include canary verification string}';

    protected $description = 'Generate a compact LLM context file (.mdc) for Boson components';

    public function handle(): int
    {
        $ide = $this->option('ide');
        $output = $this->option('output') ?? $this->getDefaultOutput($ide);
        $outputFile = rtrim($output, '/') . '/boson.mdc';

        $this->info("Generating Boson component rules for {$ide}...");
        $this->newLine();

        $canary = $this->option('canary') || config('boson.rules.canary', false);

        $basePath = $this->getBasePath();
        $parser = new BladeComponentParser($basePath);
        $generator = new CompactRuleGenerator($ide, $canary);

        $components = $parser->all();

        $this->info('Parsing ' . count($components) . ' components...');

        foreach ($components as $name => $info) {
            $hasDesc = ! empty($info['description']);
            $indicator = $hasDesc ? '✓' : '○';
            $this->line("  {$indicator} {$name}");
        }

        $this->newLine();
        $this->info('Generating compact reference file...');

        $content = $generator->generate($components);
        $generator->write($outputFile, $content);

        $tokenEstimate = $this->estimateTokens($content);

        $this->newLine();
        $this->info("Generated: {$outputFile}");
        $this->line("  Size: " . strlen($content) . " bytes");
        $this->line("  Estimated tokens: ~{$tokenEstimate}");

        $this->newLine();
        $this->comment('Legend: ✓ = has @description, ○ = props only');

        return self::SUCCESS;
    }

    protected function estimateTokens(string $content): int
    {
        return (int) ceil(strlen($content) / 4);
    }

    protected function getDefaultOutput(string $ide): string
    {
        return match ($ide) {
            'cursor' => base_path('.cursor/rules'),
            default => base_path('.agent/rules'),
        };
    }

    protected function getBasePath(): string
    {
        $publishedPath = resource_path('views/vendor/boson');

        if (is_dir($publishedPath)) {
            return dirname($publishedPath, 2);
        }

        return dirname(__DIR__, 2);
    }
}
