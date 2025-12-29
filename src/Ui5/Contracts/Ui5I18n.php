<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use Illuminate\Support\Facades\File;
use LogicException;

final readonly class Ui5I18n
{
    /**
     * @param array<string, array<string,string>> $entries
     */
    private function __construct(
        private array $entries
    )
    {
    }

    /* -- API -------------------------------------------------------------- */

    public function get(string $key, ?string $locale = null): ?string
    {
        if (null !== $locale && isset($this->entries[$locale][$key])) {
            return $this->entries[$locale][$key];
        }

        return $this->entries['default'][$key] ?? null;
    }

    public function getTitle(?string $locale = null): string
    {
        return $this->get('appTitle', $locale)
            ?? throw new LogicException('Missing i18n key: appTitle');
    }

    public function getDescription(?string $locale = null): string
    {
        return $this->get('appDescription', $locale)
            ?? throw new LogicException('Missing i18n key: appDescription');
    }

    /**
     * @return array<string,string>
     */
    public function all(?string $locale = null): array
    {
        return $locale ? $this->entries[$locale] : [];
    }

    /**
     * Returns the list of available locales derived from i18n*.properties files.
     *
     * The implicit base locale (i18n.properties) is intentionally excluded, as it
     * represents the default fallback and not a concrete locale.
     *
     * @return string[] List of locale identifiers (e.g. ['de', 'en'])
     */
    public function getAvailableLocales(): array
    {
        return array_values(
            array_filter(
                array_keys($this->entries),
                fn (string $locale) => $locale !== 'default'
            )
        );
    }

    /* -- Factory ---------------------------------------------------------- */

    public static function fromI18nProperties(string $path): self
    {
        $dir = "{$path}/i18n";

        if (!is_dir($dir)) {
            throw new LogicException("i18n directory not found at {$dir}");
        }

        $locales = [];

        foreach (glob($dir . '/i18n*.properties') as $file) {
            $locale = self::resolveLocaleFromFilename($file);
            $locales[$locale] = self::parsePropertiesFile($file);
        }

        if (!isset($locales['default'])) {
            throw new LogicException('Missing base i18n.properties file');
        }

        return new self($locales);
    }

    public static function fromMessageBundles(string $path, string $namespace): self
    {
        $srcDir = $path
            . '/dist/resources/'
            . str_replace('.', '/', $namespace);

        if (!File::exists($srcDir)) {
            throw new LogicException("Missing .library file at {$path}. Run builder first.");
        }

        return self::fromBundles($srcDir);
    }

    public static function fromBundles(string $path): self
    {
        $locales = [];

        foreach (glob($path . '/messagebundle*.properties') as $file) {
            $locale = self::resolveLocaleFromFilename($file);
            $locales[$locale] = self::parsePropertiesFile($file);
        }

        if (!isset($locales['default'])) {
            throw new LogicException('Missing base i18n.properties file');
        }

        return new self($locales);
    }

    private static function resolveLocaleFromFilename(string $file): string
    {
        if ('i18n.properties' === basename($file) || 'messagebundle.properties' === basename($file)) {
            return 'default';
        }

        if (preg_match('/i18n_([a-zA-Z_]+)\.properties$/', $file, $m)) {
            return strtolower($m[1]);
        }

        if (preg_match('/messagebundle_([a-zA-Z_]+)\.properties$/', $file, $m)) {
            return strtolower($m[1]);
        }

        return 'default';
    }

    /**
     * @return array<string,string>
     */
    private static function parsePropertiesFile(string $file): array
    {
        $entries = [];

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $entries[trim($key)] = trim($value);
        }

        return $entries;
    }
}
