<?php

namespace LaravelUi5\Core\Enums;

/**
 * Enum representing supported HTTP methods.
 *
 * This enum is used across the LaravelUi5 core to reason about
 * request semantics, especially in the context of Ui5Actions.
 *
 * - GET/HEAD/OPTIONS are considered safe/read-only methods.
 * - POST/PUT/PATCH/DELETE are considered write/unsafe methods.
 *
 * Note on Ui5Actions:
 *   Ui5Actions intentionally support only POST, PATCH and DELETE.
 *   PUT is included in the enum for completeness, but is excluded
 *   from Ui5Actions because full-resource replacement semantics
 *   are not practical in typical SAP/OpenUI5 scenarios.
 */
enum HttpMethod: int
{
    case GET = 1;
    case POST = 2;
    case PUT = 3;
    case PATCH = 4;
    case DELETE = 5;
    case OPTIONS = 6;
    case HEAD = 7;

    /**
     * Returns the canonical string label for the HTTP method.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::GET => 'GET',
            self::POST => 'POST',
            self::PUT => 'PUT',
            self::PATCH => 'PATCH',
            self::DELETE => 'DELETE',
            self::OPTIONS => 'OPTIONS',
            self::HEAD => 'HEAD',
        };
    }

    /**
     * Whether this method is considered read-only and safe.
     *
     * GET, HEAD and OPTIONS are classified as read operations
     * according to HTTP/1.1 semantics (RFC 7231).
     *
     * @return boolean
     */
    public function isRead(): bool
    {
        return in_array($this, [self::GET, self::HEAD, self::OPTIONS], true);
    }

    /**
     * Whether this method is considered write/unsafe.
     *
     * POST, PUT, PATCH and DELETE are classified as write operations.
     *
     * @return boolean
     */
    public function isWrite(): bool
    {
        return in_array($this, [self::POST, self::PUT, self::PATCH, self::DELETE], true);
    }

    /**
     * Whether this method is valid for Ui5Actions.
     *
     * Ui5Actions explicitly allow only POST, PATCH and DELETE:
     * - POST: execute a business action or create resource(s)
     * - PATCH: apply (partial) updates
     * - DELETE: remove a resource
     *
     * PUT is intentionally excluded because full-resource
     * replacement semantics are not useful in Ui5 contexts.
     *
     * @return boolean
     */
    public function isValidUi5ActionMethod(): bool
    {
        return in_array($this, [self::POST, self::PATCH, self::DELETE], true);
    }
}
