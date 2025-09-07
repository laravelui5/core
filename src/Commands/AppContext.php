<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelUi5\Core\Contracts\AppContextInterface;
use RuntimeException;

class AppContext implements AppContextInterface
{
    private string $appName;
    private string $ui5AppFolderName;
    private string $sourcePath;

    protected array $cache = [];

    /**
     * Creates a new UI5 app context based on the given name.
     *
     * Accepts either a short CamelCase name (e.g. "BusinessPartner") or
     * a fully qualified namespace (e.g. "io.pragmatiqu.businesspartner").
     *
     * - CamelCase input: Generates folder name in kebab-case prefixed with "ui5-".
     *   Example: "BusinessPartner" → "ui5-business-partner"
     *
     * - FQDN input: Uses the exact input as folder name.
     *   Example: "io.pragmatiqu.businesspartner" → "io.pragmatiqu.businesspartner"
     *
     * Sets the internal app name, UI5 folder name, and source path accordingly.
     *
     * @param string $name The UI5 app name, either in CamelCase or dot-notation namespace.
     *
     * @throws RuntimeException If the name format is invalid or the expected folder does not exist.
     */
    public function __construct(string $name)
    {
        if (str_contains($name, '.')) {
            $segments = explode('.', $name);
            $this->appName = Str::studly($segments[count($segments) - 1]);
            $this->ui5AppFolderName = $name;
            $this->sourcePath = base_path("../{$name}");
        }
        else {
            if (!preg_match('/^[A-Z][a-zA-Z0-9]+$/', $name)) {
                throw new RuntimeException("Invalid UI5 app name: must be CamelCase (e.g. Offers, ProjectKpi).");
            }
            $this->appName = $name;
            $this->ui5AppFolderName = Str::kebab($name);
            $this->sourcePath = base_path("../ui5-{$this->ui5AppFolderName}");
        }

        if (!File::exists($this->sourcePath())) {
            throw new RuntimeException("UI5 app folder does not exist at path: {$this->sourcePath}");
        }
    }

    public function checkSourceFiles(array $requiredFiles): void
    {
        $missing = array_filter($requiredFiles, function ($path) {
            return !File::exists($path);
        });

        if (!empty($missing)) {
            $report = collect($missing)
                ->map(fn($path, $label) => "Missing: {$label}\n expected at: {$path}")
                ->implode("\n");

            throw new RuntimeException("Source check failed:\n\n{$report}\n\nHint: Make sure to run `npm run deploy` inside your UI5 app.");
        }
    }

    public function appName(): string
    {
        return $this->appName;
    }

    public function ui5AppFolderName(): string
    {
        return $this->ui5AppFolderName;
    }

    public function sourcePath(): string
    {
        return $this->sourcePath;
    }

    public function manifestPath(): string
    {
        return $this->sourcePath . '/dist/manifest.json';
    }

    public function i18nPath(): string
    {
        return $this->sourcePath . '/dist/i18n/i18n.properties';
    }

    // TODO Ein baseCheck sollte sicherstellen, dass die id gesetzt ist!
    public function namespace(): string
    {
        $manifest = $this->manifest();
        return $manifest['sap.app']['id'];
    }

    // TODO Und es sollte auch sichergestellt sein, dass er aussieht wie erwartet…
    /*
    public function expectedNamespace(): string
    {
        $lastDot = strrpos($appId, '.');
        $expectedId = $lastDot === false
            ? $module
            : substr($appId, 0, $lastDot) . '.' . $module;
    }
    */

    public function manifest(): array
    {
        if (!isset($this->cache['manifest'])) {

            $json = json_decode(File::get($this->manifestPath()), true);

            if (!is_array($json)) {
                throw new RuntimeException("Invalid JSON in manifest.json");
            }

            $this->cache['manifest'] = $json;
        }

        return $this->cache['manifest'];
    }

    public function i18n(): array
    {
        if (!isset($this->cache['i18n'])) {
            if (!File::exists($this->i18nPath())) {
                throw new RuntimeException("i18n.properties not found at: {$this->i18nPath()}");
            }

            $raw = File::get($this->i18nPath());
            $utf8 = mb_convert_encoding($raw, 'UTF-8', 'ISO-8859-1');
            $lines = collect(explode("\n", $utf8))
                ->filter(fn($line) => '' !== trim($line) && !str_starts_with(trim($line), '#'))
                ->mapWithKeys(function ($line) {
                    [$key, $value] = explode('=', $line, 2);
                    return [trim($key) => trim($value)];
                });

            $this->cache['i18n'] = $lines->toArray();
        }

        return $this->cache['i18n'];
    }
}
