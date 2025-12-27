<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use DOMDocument;
use DOMElement;
use DOMXPath;
use JsonException;
use LogicException;

final readonly class Ui5Bootstrap
{
    private function __construct(
        private array  $attributes,
        private array  $resourceNamespaces,
        private string $inlineScript,
        private string $inlineCss
    )
    {
    }

    /* -- API -------------------------------------------------------------- */

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string[]
     */
    public function getResourceNamespaces(): array
    {
        return $this->resourceNamespaces;
    }

    public function getInlineScript(): string
    {
        return $this->inlineScript;
    }

    public function getInlineCss(): string
    {
        return $this->inlineCss;
    }

    /* -- Factory ---------------------------------------------------------- */

    /**
     * @throws JsonException
     */
    public static function fromIndexHtml(string $path): self
    {
        $indexPath = "{$path}/index.html";

        if (!is_file($indexPath)) {
            throw new LogicException("index.html not found at {$indexPath}");
        }

        $html = file_get_contents($indexPath);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $bootstrapScript = $xpath
            ->query('//script[@id="sap-ui-bootstrap"]')
            ->item(0);

        if (!$bootstrapScript instanceof DOMElement) {
            throw new LogicException('sap-ui-bootstrap script tag not found in index.html');
        }

        $attributes = [];
        $namespaces = [];

        foreach ($bootstrapScript->attributes as $attr) {
            if (str_starts_with($attr->name, 'data-sap-ui-')) {
                $key = str_replace('data-sap-ui-', '', $attr->name);

                if ($key === 'resourceroots') {
                    $roots = json_decode(
                        $attr->value,
                        true,
                        512,
                        JSON_THROW_ON_ERROR
                    );
                    $namespaces = array_keys($roots);
                } else {
                    $attributes[$key] = $attr->value;
                }
            }
        }

        // First inline <script> without src
        $inlineScript = trim(
            $xpath->query('//script[not(@src)]')->item(0)?->nodeValue ?? ''
        );

        // First <style>
        $inlineCss = trim(
            $xpath->query('//style')->item(0)?->nodeValue ?? ''
        );

        return new self(
            attributes: $attributes,
            resourceNamespaces: $namespaces,
            inlineScript: $inlineScript,
            inlineCss: $inlineCss
        );
    }
}

