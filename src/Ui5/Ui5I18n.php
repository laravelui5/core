<?php

namespace LaravelUi5\Core\Ui5;

use LogicException;

final readonly class Ui5I18n
{
    /**
     * @param array<string,string> $entries
     */
    private function __construct(
        private array $entries
    )
    {
    }

    /* -- API -------------------------------------------------------------- */

    public function get(string $key): ?string
    {
        return $this->entries[$key] ?? null;
    }

    public function getTitle(): string
    {
        return $this->entries['appTitle']
            ?? throw new LogicException('Missing i18n key: appTitle');
    }

    public function getDescription(): string
    {
        return $this->entries['appDescription']
            ?? throw new LogicException('Missing i18n key: appDescription');
    }

    /**
     * @return array<string,string>
     */
    public function all(): array
    {
        return $this->entries;
    }

    /* -- Factory ---------------------------------------------------------- */

    public static function fromI18nProperties(string $path): self
    {
        $i18nPath = "{$path}/i18n/i18n.properties";

        if (!is_file($i18nPath)) {
            throw new LogicException("i18n.properties not found at {$i18nPath}");
        }

        $entries = [];

        foreach (file($i18nPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            // ignore comments
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // simple key=value parsing (matches current command behavior)
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $entries[trim($key)] = trim($value);
        }

        return new self($entries);
    }
}
