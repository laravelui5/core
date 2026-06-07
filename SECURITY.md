# Security Policy

`laravelui5/core` is the integration foundation for running OpenUI5 frontends on
Laravel — it owns CSRF / `X-CSRF-Token` handling, the UI5 + OData routing and
authentication turnstile, and the source/resource strategies. That makes it
security-relevant, and we appreciate responsible disclosure.

## Reporting a vulnerability

**Please do not open a public issue for security reports.**

Report privately by **encrypted email to `security@pragmatiqu.io`**, using the PGP key
published at https://laravelui5.com/security. GitHub *private vulnerability reporting*
is also enabled on this repository.

Helpful to include:

- the affected version (`composer show laravelui5/core`),
- a description of the issue and its impact (e.g. CSRF bypass, an auth turnstile that
  fails open, route/resource exposure, token leakage),
- steps to reproduce or a proof of concept,
- any suggested remediation.

## What to expect

- **Acknowledgement** within **3 business days**.
- An initial assessment (severity, affected versions) shortly after.
- A coordinated fix and release; we will keep you informed of progress.
- Credit in the release notes if you wish (and consent to disclosure timing).

We ask that you give us a reasonable window to remediate before any public
disclosure.

## Supported versions

Security fixes are issued for the **latest released minor**.

| Version | Supported |
|:--|:--|
| latest `1.x` | ✅ |
| older `1.x` minors | ⚠️ by arrangement |

## Scope

**In scope** — vulnerabilities in the `laravelui5/core` package itself: CSRF /
`X-CSRF-Token` handling, the UI5/OData route groups and the authentication turnstile
middleware, the manifest/proxy infrastructure, and the source/resource strategies.

**Out of scope** —

- **Host misconfiguration.** Core asks whether authentication is required; the **host**
  answers it (its guard, login, and identity binding). A host that mis-wires its guard
  or fails to require auth on a protected route is a host issue.
- **`laravelui5/sdk` and `laravelui5/odata`** — report against their repositories.
- **Third-party dependencies** — report upstream; tell us if it affects this package
  so we can pin or mitigate.

See https://laravelui5.com/security for the full ecosystem policy.
