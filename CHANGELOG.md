# Changelog

All notable changes to LaravelUi5 Core are documented here, newest first. The
format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/); from
1.0.0 onward Core adheres to [Semantic Versioning](https://semver.org/).

## [5.0.0] - 2026-10-26 — The doctor, and Core's library speaks only for Core

A major. It brings `ui5:doctor`, a diagnosis of your checkout, and it hands two shell controls and their texts over to the LaravelUi5 SDK, where they always worked. On Core alone the upgrade is two lines in `.gitignore`; *Upgrading from 4.x* lists every step.

> **Using the LaravelUi5 SDK?** Move to SDK 2.0 with this major. SDK 1.x apps use the two controls Core no longer carries.

### Upgrading from 4.x

1. **Keep `.ui5-sources.php` out of git.** The file is the workspace state of one checkout, not code. Add it to `.gitignore` and remove it from the index where it is checked in: `git rm --cached .ui5-sources.php`. A new host gets the line from `ui5:app` or `ui5:lib`, which now write it for you.
2. **Implemented `Ui5SourceOverrideStoreInterface` yourself?** Add `ignored()`, which lists the entries whose path is not a directory, and let `put()` return `bool`: `true` when it created the file and added it to `.gitignore`.
3. **Using `com.laravelui5.core.Context` or `com.laravelui5.core.controls.Weave` in a view?** Both are gone from Core's library, with the texts of the shell and the SDK. They live in the SDK's library as `com.laravelui5.sdk.Context` and `com.laravelui5.sdk.Weave`. Without the SDK's shell neither did anything. `LaravelUi5.getWeave()`, `showHelp()` and `hasShell()` stay in the facade.
4. **Catching dashboard faults as an `HttpException`?** A card manifest URL that cannot be formed, a dashboard namespace of the wrong shape and a group child its module never registered are declaration faults. They now extend Core's wiring exception, a `LogicException`, and leave a line in the log. They still answer 500.
5. **Run `php artisan ui5:doctor` once.**

### `ui5:doctor`

`php artisan ui5:doctor` reads the UI5 projects your modules resolve, their manifests, yamls and translation files, and `.ui5-sources.php`. It writes nothing and touches no database. The report is grouped by severity, every error and warning ends in its fix, and it exits `1` on an error only, so it fits a CI step. Core checks five things:

- every `sap.*` library is declared in `manifest.json` **and** in every ui5 yaml (the cause of *failed to load 'library.js'*);
- workspace links point to UI5 projects, `.ui5-sources.php` stays out of git, and which strategy resolved each project;
- the component calls `LaravelUi5.init(this)`;
- every key of a bundle is present in each locale;
- no broken characters, no placeholder that differs between locales, no key twice.

When the registry does not load, the doctor says so and goes on, so it can still name the broken link behind it. A package or your host adds a rule of its own by tagging a class `DoctorRuleInterface`. The generators `ui5:app`, `ui5:lib` and `ui5:assemble` end their next steps with the doctor.

### A card sees the dashboard's period

A card's provider now receives its slot values as `provide(array $slots, …)`, the way a report's does: the period, the locale, whatever the dashboard resolved for it. Until now only `$context->slot()` reached them.

### An action names its verb, handler and request as constants

```php
class ApproveAction extends AbstractUi5Action
{
    public const HttpMethod METHOD = HttpMethod::POST;
    public const string HANDLER    = ApproveHandler::class;
    public const string REQUEST    = ApproveRequest::class;
}
```

The same form an artifact's identity already has, and the one `ui5:action` writes. A missing `METHOD` or `HANDLER` throws a message that names the constant. An action that still overrides the getters keeps working.

### For translation files

`Ui5I18n::readEntries()` returns every entry of a `.properties` file in order, with its line, so a duplicate key stays visible; `Ui5I18n::placeholders()` returns the `{N}` indices a pattern uses. What the server reads from a bundle is unchanged.

## [4.4.0] - 2026-10-23 — The client knows which languages a company works in

A minor. Nothing to upgrade.

`LaravelUi5.getLanguages()` tells an app the company's base language and the further languages its master data is
kept in, so a form can offer one input per language beside a translatable field. With the LaravelUi5 SDK the
answer comes from the company's configuration; on Core alone it is the user's language and nothing more.

The API reference reads cleaner: docblocks no longer cite internal planning documents you could not open.

## [4.3.0] - 2026-10-23 — The shell and the SDK speak your language

A minor. Nothing to upgrade.

### Texts for the shell and the server

Core's UI5 library now carries the texts the LaravelUi5 shell shows and the messages the LaravelUi5 SDK sends: the search palette, the help viewer, why an action was refused. They come in English and German and follow the user's language. If you build on Core alone, nothing changes on screen.

### Faster startup

An app read its whole source — `ui5.yaml`, the manifest, `index.html`, every translation file — each time it was asked. It now reads it once. Large registries start noticeably faster.

### For client code

- `LaravelUi5.hasShell()` tells whether the LaravelUi5 shell is attached.
- A business message that arrives with a finished `text` from the server shows that text as is.

## [4.2.0] - 2026-10-23 — Every UI5 route speaks the user's language

A minor. Nothing to upgrade if your host keeps Core's default middleware. If you override it, add one line.

### The request's language reaches the UI5 routes

Core already read the language of an OData request. Now every `ui5/*` route does too: app, manifest, card, report and action. `ResolveRequestLanguage` sits in the default `middleware` stack, before the context is built. It takes `sap-language`, else the browser's `Accept-Language`, and keeps the language part: `de-AT` becomes `de`.

What you will notice: on these routes `app()->getLocale()` now follows the user. Laravel's own texts, such as the rule sentences of a 422 from `lang/`, arrive in the user's language wherever you ship that language.

> **Overriding `middleware` in `config/ui5.php`?** Add the line after `'web'` and before `ResolveUi5Context`:
>
> ```php
> 'middleware' => [
>     'web',
>     \LaravelUi5\Core\Http\Middleware\ResolveRequestLanguage::class,
>     \LaravelUi5\Core\Http\Middleware\ResolveUi5Context::class,
>     // …
> ],
> ```

### The fallback order, as an API

`Ui5I18n::fallbackChain($locale)` returns the order in which `get()` looks for a text: `de-AT` gives `de_at`, `de`, `en`, then the base bundle. Use it when you keep texts per language yourself, so they fall back in the same order as the bundles.

## [4.1.0] - 2026-10-23 — The server reads your translations the way UI5 does

A minor. Nothing to upgrade, no signature broke. Core reads an app's `i18n` bundles on the server, for the app's title and description and for whatever you read through `getI18n()`. Until now that reader was much simpler than UI5's own. From this release on both sides read the same file the same way.

### Umlauts and other non-ASCII text come through

The UI5 build writes every non-ASCII character as an escape: `Geschäftspartner` becomes `Gesch\u00e4ftspartner`. The old reader passed the escape through. Any German, French or Czech text read on the server showed it. The reader is now a port of UI5's own `Properties.js` and decodes it, emoji included. Your source bundles stay as they are: type `ä`, `é` and `ß` in UTF-8, and both the plain and the escaped form read correctly.

The rest of the `.properties` format is read the same way:

- `key: value` and `key value` alongside `key=value`
- `!` comments alongside `#`
- a trailing `\` that continues the line
- escaped `\=` and `\:` inside a key
- whitespace at the end of a value is kept

### A missing translation falls back like the browser does

`get('key', 'de_AT')` now tries `de_AT`, then `de`, then `en`, then the base bundle. That is the order UI5 follows on the client, so server and screen never show two languages for one key. `de-AT` and `de_AT` are the same locale. Without a locale the base bundle answers, as before.

### Placeholders, formatted like the client

`Ui5I18n::formatMessage($pattern, $args)` formats a text exactly as UI5's `formatMessage` does, and `get()` takes the arguments as third parameter:

```php
$i18n->get('order.created', 'de', [$order->number]);
```

`{0}` takes the first argument, and `''` writes one apostrophe. A single `'` starts a quoted section, just as on the client: `partner's {0}` shows its placeholder literally. Write `partner''s {0}`.

### Broken bundles fail loudly

A bundle that is not UTF-8, or that has an incomplete `\u` escape, now throws an exception that names the file. Before, it was read with mangled text. `all()` without a locale returns the base bundle, where it returned an empty array before.

## [4.0.0] - 2026-10-22 — A module says what it contains, in one place

A major. It changes how a module lists its artifacts and how an artifact names itself. Both mechanical edits. And it hands the artifact types that belong to the SDK over to the SDK. Most applications upgrade in a few minutes per module; *Upgrading from 3.x* lists every step.

> **Using the LaravelUi5 SDK?** Stay on Core 3.x until SDK 2.0, which is built on Core 4.

### Upgrading from 3.x

1. **Replace the module's getters with two constants.** The root — what `getApp()` or `getLibrary()` returned — becomes `ARTIFACT_ROOT`; everything else is listed in `ARTIFACTS`, by class name:

   ```php
   class SalesModule extends AbstractUi5Module
   {
       public const string ARTIFACT_ROOT = SalesApp::class;

       public const array ARTIFACTS = [
           Cards\PipelineCard::class,
           Actions\ApproveAction::class,
       ];
   }
   ```

   `getCards()`, `getTiles()`, `getCharts()`, `getActions()`, `getResources()`, `getDashboards()`, `getReports()`, `getDialogs()`, `getValueHelps()`, `getApp()`, `getLibrary()`, `hasApp()` and `hasLibrary()` are gone. To read a module's artifacts of one kind, ask `$module->get(Ui5ActionInterface::class)`.
2. **Name the type of every identity constant.** `public const NAMESPACE = '…';` becomes `public const string NAMESPACE = '…';`, the same for `VERSION`, `TITLE` and `DESCRIPTION`. An untyped one no longer loads, and PHP's message names it.
3. **`ArtifactType` is now `CoreArtifactType`.** `ArtifactType::Application` becomes `CoreArtifactType::Application`. A method that declares the type in its signature returns `ArtifactTypeInterface`. The numeric values changed; if you stored them, map them anew.
4. **`Ui5DialogInterface` and `Ui5ValueHelpInterface` moved to the SDK**, with the dialog and value-help types. Core no longer names them.
5. **The registry returns `ArtifactInterface`.** If you pass `getArtifact()`'s result on to an API that wants a UI5 artifact, check `instanceof Ui5ArtifactInterface` first.

### A module that is half migrated fails at boot

A module without its root — still written with the old getters, say — no longer loads empty with its app missing and its routes answering 404. The registry asks every module for its root as it loads, and names the module that has none. A second root in `ARTIFACTS` is refused the same way.

### An artifact that forgets its name says so

An artifact that leaves an identity constant empty now throws a message that names the class and the constant, and how to declare it — instead of PHP's bare *undefined constant*. Static analysis sees the constants too.

### Faster lookups

Asking a module for its namespace or its root builds the root alone; the rest of its artifacts are built once, when they are first needed.

### Scaffolding

- `ui5:app` and `ui5:lib` write the module in the new form, and every generator ends with the line to add to `ARTIFACTS`.
- The generated `{App}AppBase` is marked `@internal`: it is rewritten on every refresh and is never a class to extend.

## [3.0.1] - 2026-10-09 — Core is checked by static analysis, and what that found

A patch. Nothing to upgrade, no signature changed. From this release on, every Core build is checked by PHPStan at level 5 — over the source and the test suite, with nothing parked in a baseline. The check found one real fault and a handful of type declarations that said something false.

### A model parameter without a model class fails the way it should

`#[Parameter(type: ParameterType::Model)]` requires `model:` — the attribute says so. Leaving it out was never caught where it should have been: the request ended in a PHP `Error` and a bare 500. It now throws `InvalidParameterException`, which names the parameter and lands in your log as a configuration fault.

### Type declarations you can rely on

If your IDE or your own static analysis reads Core's types, these now tell the truth:

- `Ui5AppInterface::getResourceNamespaces()` returns `list<string>` — a plain list of JS namespaces,
  as the generated app has always written it. The declaration claimed an array keyed by namespace.
  Nothing changes at runtime; an analyser that flagged your correct list will stop.
- A few internal declarations no longer hide the checks behind them from analysis.

> **Running PHPStan on your own app?** Reading a setting — `$this->maxItems` in a handler or
> provider — is reported as an undefined property, because settings arrive through a magic getter
> PHPStan cannot see. Core will ship a PHPStan extension that makes those reads known and
> type-checked. Until then, an ignore for `property.notFound` on those classes is the honest answer.

## [3.0.0] - 2026-10-08 — The known gaps close, and a slot is one call away

A major. It closes nearly every gap the roadmap listed between what the documentation promised and
what Core did, and it removes a few things that were wrong rather than incomplete. Most applications
upgrade with a few edits; *Upgrading from 2.x* lists them all.

> **Using the LaravelUi5 SDK?** Stay on Core 2.x until the SDK release that supports Core 3. The SDK
> implements Core's context contract, which gains a method in this release.

### Upgrading from 2.x

1. **Add the language middleware to your published `config/ui5.php`.** Put
   `\LaravelUi5\Core\Http\Middleware\ResolveRequestLanguage::class` into `odata_middleware`, right
   after `'web'`. `composer update` does not change a published config.
2. **Stop reading the `currency` slot.** It is gone. A base currency is not a per-request value — an
   installation has one — so a slot that let a URL override it either repeated it or claimed something
   false. Core now ships six slots: `locale`, `timezone`, `period`, `date`, `date_from`, `date_to`.
3. **A context of your own implements `slot()`.** If you bind your own `Ui5ContextInterface` through
   `ui5.context_factory`, add `slot(string|BackedEnum $name): string|int|float|bool`; the one-line
   implementation is in the documentation.
4. **Send slot values in the query string.** Slots are no longer read from the request body or from
   route parameters. A path segment that names a record belongs in `#[Parameter]`.
5. **Remove `catch (ParameterPipelineCycleException)`.** The class is gone; nothing ever threw it.
6. **Name your vendor when you scaffold.** `ui5:app --create` needs `--package-prefix`,
   `--php-ns-prefix` and `--vendor`; `ui5:lib --create` needs the first two. They no longer default
   to our names, and a run without them stops and prints the line to use. `--refresh` needs none.
7. **Declare every `#[Setting]` on its artifact.** A setting on a handler or provider is no longer
   read; the artifact is the one place a setting is declared.
8. **An app that implements `Ui5AppInterface` directly adds `getUi5Version(): ?string`** — return
   `null` to keep using `ui5.version`. Apps extending `AbstractUi5App` need nothing.
9. **Catch Core's configuration faults as `LogicException`.** The exceptions that answered with a
   500 — an unknown slot, a parameter of the wrong type, a module without a root artifact and their
   siblings — are no longer HTTP exceptions, so they reach your log.

### Read a slot anywhere

`$context->slot(CoreSlots::Period)` returns a slot's value wherever you have the context — an action
handler, a resource provider, an OData resolver. It is the same answer the dashboard's tiles get: the
query string, else a dashboard's composition, else the declared default, typed as the slot declares.

### Settings: one declaration, every provider

A `#[Setting]` lives on the artifact — the card, the action, the report — and its value reaches the
class that serves it on every path: actions, resources and cards as before, and now tiles, charts and
reports too. A tile receives its own settings, not its dashboard's. Settings are read-only: assigning
to one throws, and `isset()` answers for them.

### The client's language reaches OData

OData requests now take their language from `sap-language`, else from `Accept-Language` — which
OpenUI5 sends on every request, code lists included. Translations, and anything that reads the
application locale, follow the language of the UI.

### Parameters and resources

- **Every parameter type arrives.** A `#[Parameter]` typed `int`, `float`, `string` or `bool`,
  `Decimal` (as a numeric string) or `DateTime` (as a Carbon) now reaches the handler. Until now
  only models and dates got through. A segment that does not cast answers 400.
- **Resources take path parameters.** The URL a resource's manifest advertises — one segment per
  `#[Parameter]` — now exists.

### Scaffolding

- **A library is a package.** `ui5:lib --create` writes a `composer.json` and a service provider, and
  prints how to register the library with your host, exactly as `ui5:app` does.
- **The `composer.json` that `ui5:app` writes is valid.** Its autoload prefix lacked the closing
  namespace separator.
- **One registration hint, with the right line.** Every generator names the module method to edit
  and a line you can paste — an instance, `new …($this)`. `ui5:dashboard` no longer points at
  `config/ui5.php`.
- **Kebab-case URL segments.** New actions, resources, cards, tiles, charts and reports are slugged
  `next-best-action`, like dashboards and groups. Existing artifacts keep their names.
- **Builds use your project's tools.** The build runs as `npm run build`, so a project whose build
  tools are local devDependencies builds as well as one using a global CLI.
- **Stable generated files.** Refreshing an app no longer rewrites unchanged arrays in a different
  layout, and `.ui5-sources.php` is written cleanly.
- **An app can name its OpenUI5 version.** `ui5:app` takes `framework.version` from your project's
  `ui5.yaml`; in built mode the app loads that version, else `ui5.version`.

### Errors that say what is wrong

- **Unknown OData addresses answer as OData.** An unknown namespace (404) or an artifact without an
  OData endpoint (400) now carries the OData error format and `OData-Version`, so the UI5 client
  reports the real problem instead of a protocol error.
- **Configuration faults are logged.** See step 9 above — including those of a dashboard child that
  was left out.
- **A source override that points nowhere** in `.ui5-sources.php` is logged instead of silently
  ignored.
- **A duplicate infrastructure key** is refused even when the first module contributed nothing.

### Dashboards

A new extension point decides visibility **after** a dashboard child is built, when its press-intent
is known: register an `ElementGateInterface`, and a child it hides is simply left out. Nothing
registered changes nothing.

### Global dialogs save from anywhere

A global dialog opened from another app saves through its own module's action. The bundled
`@laravelui5/core` used to look the action up only in the hosting app and reported it as unknown.

### Removed

- The `currency` slot (step 2) and `ParameterPipelineCycleException` (step 5).
- The multi-app source convention `resources/ui5-<slug>`; give each app its own module folder.
- `EnsureFrontendVersionIsLatest` — it was not part of any default middleware stack.
- Five exception classes that nothing threw any more, among them `InvalidSettingException`.

## [2.11.0] - 2026-09-14 — A slot value always has the type its slot declares

A minor. Slot values now arrive in one predictable form, a value that does not fit is refused instead
of passed along, and a few declaration mistakes that used to surface much later now stop the
application at boot. Read *What you may notice* before upgrading.

### Every slot value arrives in one form

Until now a provider received whatever the answering source happened to hold. The same date slot
could be a string when the default answered and a date object when a stored value did, so there was
no single way to write the code that reads it. Now every value leaves the resolution chain in the
form its declared type names, whichever source filled it:

| Type | You receive |
|---|---|
| `String` | `string` |
| `Integer` | `int` |
| `Float` | `float` |
| `Boolean` | `bool` |
| `Decimal` | a numeric `string`, e.g. `'19.99'` |
| `Date` | a `string`, `Y-m-d`, e.g. `'2026-09-14'` |
| `DateTime` | a `string`, ISO-8601 with offset, e.g. `'2026-09-14T08:30:00+02:00'` |

Dates and decimals are strings on purpose. Slot values travel in URLs and JSON — a card's manifest
URL is built from them — and a string survives that trip unchanged. A decimal stays a string because
a float is exactly what a decimal type exists to avoid. Parse where you compute:
`Carbon::parse($slots['date_from'])`.

### A value that does not fit is refused, loudly

- **From the request.** `?date_from=someday` for a date slot now answers **422**, with a message
  that names the slot and the form it expected. It does not quietly fall back to the default: a
  broken link that answers as if it were fine is the harder bug to find.
- **From anywhere else.** A stored value or a dashboard's proposal of the wrong type raises
  `InvalidSlotSourceValueException`, which names the source that produced it and reaches your log.

The check is strict wherever a lenient parser would change the answer: `2026-02-30` is not rolled
over into March, `true` is not the string `'1'`, and an unresolved `@today` is not read as a Unix
timestamp.

### Declaration mistakes stop the boot

Each of these used to register without complaint and fail later, somewhere else:

- **A slot default that does not fit its type** — `#[Slot(type: ParameterType::Integer, default:
  'many')]`.
- **An unknown date sentinel** — `@tomorow` used to survive as a literal string. `@today`, `@now`
  and `@first-of-month` are the ones Core knows.
- **A `#[Slot]` on an artifact class.** Slots belong on the module class; elsewhere the attribute
  was silently ignored.
- **A `Model` or `ModelArray` setting without a `modelClass`**, or with one that does not exist. It
  used to register and then fail on every write.

Each now raises an exception at boot that names the class to fix. And a slot an artifact requests
must be in the catalog; an unknown name fails before any source is asked.

### What you may notice

- A value from the request is no longer passed through raw. An `Integer` slot sent as `?id=12`
  arrives as `12`, not `'12'`; a `Boolean` sent as `?flag=false` arrives as `false`. Before, the
  string `'false'` was truthy. Providers that cast their slot values (`(int) $slots['id']`) are
  unaffected; code that compares them strictly as strings is not.
- An application whose declarations contain one of the mistakes above no longer boots. Check your
  `#[Slot]` and `#[Setting]` declarations before you upgrade — the message says exactly what to
  change.

### Deprecated

- `ParameterPipelineCycleException`. Nothing throws it — the resolution chain cannot contain a cycle
  — and it is removed in 3.0. A `catch` that names it keeps compiling until then.

### Documentation in the code

Several docblocks described behaviour the code does not have, which is what your IDE and the
[API reference](https://laravelui5.com/api/core/index.html) showed you. Corrected: `#[Slot]` (what `editable`
does, and the date sentinels spelled as Core matches them), `#[Setting]` (a working example, and no
longer listing report providers as a target), `EditLevel::allows()`, `ParameterType` (which now says,
per type, what a slot consumer receives), the `ui5:slot` command description, and the module and
dialog contracts, which named a configuration key and a controller path that nothing reads.

## [2.10.0] - 2026-09-09 — An assembled app no longer locks itself out

A minor: `ui5:assemble` writes a different module than it did in 2.9.1. Applications you have
already generated are untouched — they carry their own `Module.php`.

### The generated module declares `requiresAuth(): false`

Core's turnstile gates a module when two things are true at once: the module asks for
authentication, and the application has a route named `login`. A module inherits `true`, so an
assembled app stays open only for as long as no login route exists. Install a package that brings
one — an authentication package, for instance — and every visitor is redirected to a screen your
application may have no way through.

The blueprint therefore declares the override, as Core's own module has always done. What
`ui5:assemble` produces is a self-contained application to build and demonstrate with, and it is
not the place where your identity decisions belong. An application that should be gated deletes the
method and inherits `true` again.

### Docblocks link to the documentation

Class documentation across the package pointed at design notes that live in a private repository.
Those references now point at the pages on laravelui5.com that cover the same ground, so the API
reference and your IDE's quick documentation lead somewhere you can actually open.

## [2.9.1] - 2026-08-29 — `ui5:wire` prints instructions you can actually follow

A patch, and a narrow one: nothing the command *writes* has changed. A floorplan generated by 2.9.0
and one generated by 2.9.1 are byte for byte the same. What changed is the block printed afterwards.

### The three steps it leaves to you did not describe themselves correctly

`ui5:wire` stops short of lighting up OData on purpose and prints the three edits it leaves to you.
That block exists to be followed — and two of its three lines were wrong about what they were asking
for.

**Step 2 asked for a line in a method you do not have.** It read *"In `configure(EdmBuilderInterface
$builder)`, add: `$this->discoverModel(User::class);`"*, as though `configure()` were sitting in your
app class waiting for a line. It is not. The app the generator writes carries no `configure()` at
all; the method arrives only once step 1 has swapped `implements Ui5AppInterface` for
`extends AbstractUi5App`, and it comes down from the OData engine — `protected`, and returning an
`EdmBuilderInterface`. Step 2 is an **override**. Written the way the hint asked for it, you got a
method with no return type and no return value, against a parent that declares both.

The step now says so, and prints the whole method with its import:

```
    2. In ShowcaseApp.php, override configure() — it is inherited, not generated:

           use LaravelUi5\OData\Service\Contracts\EdmBuilderInterface;

           protected function configure(EdmBuilderInterface $builder): EdmBuilderInterface
           {
               $this->discoverModel(\App\Models\User::class);

               return $builder->namespace($this->namespace());
           }
```

**And the heading filed all three steps under one file.** It read *"…until you reveal the data. In
`ShowcaseApp.php`:"* and then listed a third step that lives in `manifest.json`. Every step now names
its own file, and the heading claims nothing.

If you have wired a model before, none of the edits themselves have changed — only the description
of them, which is now the description of the edits you actually make.

## [2.9.0] - 2026-08-28 — Room for a second screen, and one keystroke to the UI5 docs

Two changes, both additive. If you already have an assembled app, nothing about it changes; if you
assemble a new one, you get a shape that has room in it.

### `ui5:assemble` now gives you a shell, not just a screen

The app the `launchpad-app` blueprint produced was complete and closed. `App.view.xml` held the
dashboard directly, which is fine right up until you want a second screen — and the very next
command you are likely to run, `ui5:wire`, exists to give you one. Getting there meant rearranging
the file the generator had just written for you, which is a strange thing for a generator to ask.

Now the generated app arrives already knowing how to grow:

- `App.view.xml` is the shell. It holds a navigation container and nothing else.
- `Dashboard.view.xml` and its controller are new, and carry the dashboard.
- `manifest.json` ships a `routing` section that puts the dashboard on the app's initial route.

Adding a screen is a route and a target — three lines in a list that already exists. That is also
exactly what `ui5:wire` asks for when it prints the steps it will not take for you, so the two
commands now fit together without an edit in between.

`ui5:wire` still prints those steps rather than performing them. That is on purpose: lighting up
OData is a decision, not plumbing, and a generator that made it silently would leave you without the
one seam worth knowing about. It also means the command can be run in front of an audience.

One smaller fix rides along: the generated `Component.js` now starts the router only after the Core
front-end facade has finished initializing, so the first route never navigates into a view whose
model is not ready yet.

### The typed UI5 controls now link to SAP's documentation

Core ships typed PHP mirrors of the UI5 controls — `GenericTile`, `NumericContent`, `Panel`, `Card`
and the rest — so you build a dashboard with named arguments and IDE completion instead of XML. They
are pleasant to write and were unpleasant to *discover*: hover one in your editor and you got its
constructor, which for `GenericTile` is twenty-seven parameters with nothing above them explaining
what any of it meant.

We did not fix that by copying SAP's property documentation into PHP. It is better where it is, and
a copy would be wrong by the next OpenUI5 release. Instead every control's constructor now carries
the part SAP's page cannot tell you — that each parameter maps to the UI5 property of the same name,
that leaving one `null` means "no opinion, take UI5's default", and which few seats are ours rather
than SAP's — plus a link to that control's API page.

The practical result: **put your caret on a control's class name and press Shift+F1**, and the
OpenUI5 documentation for it opens in your browser. One keystroke, always the current version, never
a stale copy.

Nineteen controls, every one with a constructor.

### Added

- A `Dashboard` view and controller in the `launchpad-app` blueprint, with the app's routing
  configured to land on it.
- Constructor documentation and OpenUI5 API links on all nineteen typed control classes.

### Changed

- The blueprint's `App.view.xml` is now a navigation shell; the dashboard moved into its own view.
- The generated `Component.js` starts the router after the Core facade is ready.

## [2.8.2] - 2026-08-27 — Core asks for the PHP version it has always needed

Core's `composer.json` said it ran on PHP 8.3 or newer. It never did. Core requires
`laravelui5/odata`, and odata has asked for PHP 8.4 since the day it was published — so no
installation of Core has resolved on 8.3 at any point.

The declaration is now `^8.4`, which is what it always was in practice. Nothing breaks, because there
is no working 8.3 installation anywhere to break. What changes is the message you get if you try: on
PHP 8.3, Composer now tells you plainly that your PHP version is too old, instead of reporting a
dependency conflict inside odata and leaving you to work out what it meant.

`laravelui5/auth` moved to the same floor in the same pass.

The floor moves again only when Laravel's own minimum reaches PHP 8.5 — we follow Laravel here rather
than the newest release.

### Fixed

- `composer.json` requires `php ^8.4`, matching the version every Core install has actually needed
  since the OData engine changed.

## [2.8.1] - 2026-08-27 — The wiring instructions now fit the host you actually have

2.8.0 taught `ui5:app` and `ui5:assemble` to finish by printing the exact lines that wire a freshly
scaffolded app into your host. Those lines were right in isolation and wrong in context, and this
release fixes that.

You reached Core by telling Composer where our registry lives:

```bash
composer config repositories.pragmatiqu composer https://packages.pragmatiqu.io
```

That command writes your `repositories` section as a **named block** — each repository under a key.
The generator then handed you a snippet in the other shape Composer accepts, a plain **list**. Both
are valid, but a file holds one or the other, so pasting the list replaced the named block — and took
your `laravelui5` entry out with it. Nothing complained at the time. The failure arrived one command
later as `Could not find a matching version of package laravelui5/core`, which reads like a typo in a
package name when in fact the registry it came from was no longer in the file.

Both generators now print commands instead of a snippet:

```bash
composer config repositories.showcase path ui5/Showcase
composer require example/showcase:@dev
```

Composer edits its own manifest, so the shape stays consistent whichever way you set it up. It places
the app ahead of the registry, which is what you want from a local path repository — your own tree
wins. And `composer require` installs in the same step, so the separate `composer update` that used to
follow is gone. There is nothing left to "refresh" afterwards, which also removes a trap: reaching for
`composer dump-autoload` at this point does nothing at all, because the app isn't in your lock file
yet, and the app then fails to boot with a message pointing at the registry rather than at the
autoloader.

Listing the module in `config/ui5.php` is unchanged and still yours to do. It is now the only edit,
and the generator says why it stays one: which apps a host serves is a product decision, not a
directory listing.

**If you already have apps wired, do nothing.** A list-shaped `repositories` section is perfectly
valid where it is the only shape in the file, and hosts set up that way keep working exactly as
before. This changes what newly scaffolded apps tell you to do.

The Quickstart and the self-contained-app guide carried the same contradiction — telling you to run
`composer config` for the registry, then to paste the list form for your app — and have been rewritten
against the commands.

### Fixed

- `ui5:app` and `ui5:assemble` close with `composer config` + `composer require` instead of a
  `composer.json` snippet whose shape could silently drop the registry you installed Core from.
- The Quickstart and self-contained-app documentation were corrected to match.
- API-reference corrections: the registry no longer describes modules as being keyed by a "slug"
  (they are keyed by the namespace they declare), `getModuleByClass()` is documented as returning a
  module rather than an artifact, the shape returned by `settings()` is described accurately, and the
  example URL for `resolve()` uses the current `{path}@{version}` form.

## [2.8.0] - 2026-08-18 — Wiring a scaffolded app into your host is two edits, not three

When you scaffolded an app with `ui5:app` or `ui5:assemble`, the generator handed you the files and
then asked you to introduce them to your host three separate times: repeat the app's namespace in your
root `composer.json`, add the generated service provider to `bootstrap/providers.php`, and declare the
module in `config/ui5.php`. Two of those three were work Composer and Laravel already know how to do.

A generated app now ships a complete package manifest of its own. It declares its own autoload root and
its own service provider, so the host only has to point at it: a `path` repository plus a `require`
line, and the app is loaded — namespace resolved, provider registered by Laravel's package discovery.
Your root `composer.json` keeps its `autoload` block untouched, and `bootstrap/providers.php` stays as
it was. Because path repositories link rather than copy, the app stays live in your tree: edit, reload.

What stays a manual step, on purpose, is listing the module in `config/ui5.php`. That entry is the
statement that the app is in scope — visible, supported, part of your product. It is a decision, not
plumbing, and the platform will not quietly make it for you.

Both generators now close by printing the exact lines to add, so none of it is guesswork. Nothing is
written into your root `composer.json` on your behalf — that file is yours.

Existing apps are unaffected. However you wired them, they keep working; only newly generated apps get
the fuller manifest.

### Changed

- A scaffolded app's `composer.json` is now a complete package manifest — it declares its own autoload
  root and its service provider, so a `path` repository plus a `require` is all the host needs. No
  namespace entry in your root `composer.json`, and no line in `bootstrap/providers.php`.
- `ui5:app` and `ui5:assemble` finish with the exact host lines to add — the path repository, the
  `require`, and the module entry for `config/ui5.php` — followed by the `composer update` that
  installs it. `ui5:app` previously printed no registration guidance at all.

## [2.7.2] - 2026-07-22 — Signing back in returns you where you were

A small follow-on to 2.7.1. When an expired session sent you to sign in again, you'd come back to a
default landing page — not the screen you'd been on. Now the app remembers the exact place you were
and takes you straight back there after you sign in, so an expired session is a brief detour rather
than a lost spot.

Nothing to configure — it works with the sign-in flow the platform already provides.

### Changed

- After re-authenticating on an expired session, the app resumes at the exact view you were on,
  instead of dropping you on a default landing page.

## [2.7.1] - 2026-07-22 — A stale session no longer swallows a click

If you left the app open long enough for your session to expire, some actions could quietly do
nothing — you'd click, and there'd be no error, no message, and no page change. That dead click
is the problem this release closes. When your session has lapsed, the app is now told so plainly,
instead of a background request being quietly bounced to the sign-in page and its answer lost —
so an expired session can send you back to sign in and return you where you were, rather than
leaving you staring at a button that seems broken.

Nothing changes while you're signed in; this only touches the moment a session has already
expired. There is nothing to configure.

### Fixed

- An expired session is now reported clearly to the app instead of silently redirecting its
  background requests to the sign-in page — so a click made after a session has lapsed leads to a
  clean return to sign-in, not a dead click that appears to do nothing.

## [2.7.0] - 2026-07-21 — Opening a picker to a named list

Building on 2.6.0's multi-list value helps: a field can now **open a picker to a named list**, and
which list it opens — and which rows fill it — is decided and enforced on the server, not by the page.

You ask for the picker by its business object and the list you want ("partners, the *colleagues*
list"); the server resolves that to the right set, checks you're allowed to read it, and hands the
picker the rows. The page names *which list*, never *which rows* — so a picker can't be steered to
show more than it should. And a list you're not allowed to read comes back as a clear message **inside
the dialog**, not a silently empty table that would read as "no matches."

There's one thing to know if you already open pickers: the call now takes the **name of the list** —
you say which list you want, where before there was only the picker. For the everyday "people in my
own organization" list you pass nothing else — the server scopes it to whoever you're signed in as,
never to anything the page hands it. The value-help runtime is the SDK's, so on Core alone a picker
doesn't open and none of this changes for you.

### Changed

- **Opening a value-help picker now names the list (scope) it opens.** The server resolves the named
  list to its underlying set, authorizes it, and returns the rows; a denied list surfaces as an
  in-dialog message rather than an empty table. A picker that shows "your own organization" is scoped
  to your signed-in identity — the page passes no id. The picker runtime is provided by the SDK; on
  Core alone pickers don't open, so this is inert without it.

## [2.6.0] - 2026-07-20 — A value help can offer more than one list

A small, additive release. A **value help** — the search-help a field pops open to pick a business
object — used to be a single list. Now it can be one picker that offers **several named lists of the
same shape**: a partner picker that shows *colleagues* in one place and *everyone with a login* in
another — a different set underneath each, but the same columns and the same selection on top.

To make that work, a value help now declares two things: the **shape** it presents (the columns your
selection comes back with) and the **named lists** — scopes — it accepts. A field asks for the picker
by name and the list it wants, and the right set is chosen, and authorized, on the server. One picker
per business object, many facets — the same idea will carry products, projects, and the rest.

This is the foundation the scoped picker builds on. Like the Related menu, it is inert on Core alone
and comes alive with the SDK, which owns the value-help runtime. It's purely additive — nothing you
already wrote changes.

### Changed

- **A value help now declares its shape and its scopes.** It states the columns its list returns and
  the named lists it offers, so one picker can serve many server-defined, server-authorized lists of
  the same shape. Existing value helps are unaffected.

## [2.5.0] - 2026-07-17 — The Related menu is now a single tag

A small, additive release that turns 2.4.0's **Related** menu into a drop-in control. Where you
previously wired the menu by hand — fetch the doorways, render the button, handle the click — you now
write one tag on the detail page and it does all of it:

```xml
<lux:Weave/>
```

Drop it on the detail page — nothing to configure. It renders the doorways declared toward the record
the page is about, shows only the ones the current user may open, and on click takes you straight to
that record in the other module — no controller code, no click handler, nothing to keep in sync. The
record and its key are read from the page the tag sits on, so there is nothing to pass at all. When
there are no doorways, it renders nothing.

As before, this is inert on Core alone (the menu stays empty) and comes alive with the SDK. If you
hand-wired the menu against 2.4.0's `getWeave`, that accessor is unchanged — keep it, or replace your
wiring with the tag. Nothing you already wrote breaks.

### Added

- **`<lux:Weave>` — the Related menu as a control.** One tag renders a business object's related
  doorways, filters them to what the user may open, and navigates on click; it reads the record's key
  from the detail page it sits on and hides itself when there's nothing to show. Renders nothing on
  Core alone; lights up with the SDK.

## [2.4.0] - 2026-07-17 — A record can show links to what relates to it

A small, additive release. When you're looking at a business object — a partner, say — its detail
can now offer a **Related** menu: doorways into the other modules that work with that same object
(its orders, its invoices, its contracts). Each module declares the link toward the object once, and
the object's detail grows the doorway on its own — the page you're looking at is never edited to know
about the newcomer. The menu is filtered to what you're allowed to open, and clicking a doorway takes
you straight to that object in the other module.

On Core alone this is inert: there is no shell to resolve the links, so the menu is empty and the
entry point stays quiet. It comes alive with the SDK, which knows the modules and the links between
them. Write against it now and it lights up when the SDK is there. Nothing you already wrote changes.

### Added

- **`getWeave` on the client facade.** Ask for the related doorways declared toward a business
  object and get back the list, already filtered to the ones the current user may open — ready to
  render as a "Related" menu on a detail page. You never hold the destination's address: you hand
  back only which object you're on, and the system resolves where each doorway leads, so a module
  can offer a link into another without knowing its inner routes. On Core alone the list is empty;
  it fills once the SDK is present.

## [2.3.1] - 2026-07-15 — Value-help pickers show their own labels and data

A small fix on top of 2.3.0's value help. A picker opens in a shared overlay area, detached from the
app that owns it — so it could appear with raw label keys or an empty list. It now connects to that
app's translation and data models the moment it opens, and renders with the right text and content.
Nothing you wrote needs to change.

### Fixed

- **A value help picker now shows its own text and data.** It inherits the owning app's models
  (translations included) as it opens, so labels and the list render correctly — whether the picker
  belongs to the current app or to another module opened across the boundary. (Value help activates
  with the SDK; on Core alone the entry points stay inert, as before.)

- **The acting- and authenticated-partner accessors are wired to the shell.** `getActor()` and
  `getPrincipal()` now resolve through the shell — they return the current partner once the SDK is
  present; on Core alone they still return `null`, unchanged.

## [2.3.0] - 2026-07-15 — A field can open a picker and hand back a selection

A small, additive release: it introduces **value help** — a modal picker a form field opens to
browse a business object and return a selection. On Core alone the entry points are present but
inert; they come alive once you add the SDK, which hosts the picker and carries the selection back.
Write against them now and they light up when the SDK is there. Nothing you already wrote changes.

A value help is, in one line, *a dialog that returns a selection*. A field offers a "browse…"
affordance; the user opens a searchable list of whatever they're choosing, picks one or several, and
the chosen `{ key, text }` — optionally with more — flows back to the field. Because it is built on
the same global-dialog mechanism Core already ships, a picker can belong to the current app *or* to
another module entirely; you open it by name and await the result.

### Added

- **The ValueHelp artifact.** A value help is a small view — a dialog with your list inside — plus a
  short controller that shapes the list and returns the selection, declared alongside your app's
  other artifacts. It can optionally carry its own access gate; that gate matters only when another
  app opens the picker, so the picker itself decides who may browse it, not the field that calls it.

- **`openValueHelp` on the client facade.** Open a picker by name and `await` the outcome: the
  selected items, an empty list (a deliberate "cleared"), or nothing at all when the user backs out —
  so a cancel needs no error handling. At most one picker is open at a time. On Core alone the call
  is inert (there is no shell to host the picker); it activates with the SDK.

- **A picker controller base.** A base controller supplies `confirmSelection` and `cancel` and reads
  the mode and context the caller asked for, so a picker stays a short view and a short controller —
  and the app's models (translations, data) are available inside it, whether it is your own picker or
  one contributed by another module.

## [2.2.0] - 2026-07-13 — Telling apart the slots a component needs from the ones it offers

A small, additive release that sharpens one contract. If you build on Core's base classes — the
usual path — there is nothing to change and nothing to notice.

Core's "slot" capability quietly did two jobs through a single interface: declaring the slots a
component *needs* in order to render, and *offering* slot values up into a dashboard composition.
Most components only do the first. A report reads slots but contributes none — yet it still had to
carry an empty "what do you offer?" method that never did anything. That capability is now two
interfaces, so each component states only what is true of it.

### Added

- **`SlotProposableInterface`** — the "offers slot values to a dashboard" half, for the components
  that actually compose one: dashboards, cards, tiles, charts, and groups. It extends the base slot
  interface, so a composing component still declares both what it needs and what it offers through a
  single `implements`.

### Changed

- **`SlottableInterface` is now the "declares the slots it needs" half, by itself.** A component
  that only reads slots — a report is the first — implements this alone and no longer carries an
  empty "offers nothing" method; reports have dropped it. Components that extend Core's base classes
  are unaffected: each base already implements the right interface for its type, and every composing
  component keeps working unchanged. Only code that implements the slot interfaces by hand sees the
  split.

## [2.1.0] - 2026-07-12 — A brief confirmation on success, and room for packages to grow

A small, additive release. One thing becomes visible to the people using your apps, and one
thing opens up for packages built on Core. Nothing you already wrote needs to change.

### Added

- **A successful action can now say so.** When your backend returns a short summary line with a
  successful result, the platform shows it as a brief, self-dismissing confirmation — the
  familiar toast that slides in and fades. It's the counterpart to the inline messages and
  auto-refresh from 2.0: those carry the detail, this is the at-a-glance "done." Purely
  opt-in — a result that carries no summary line shows no toast, so nothing changes until your
  backend sends one.

- **Scaffolding is now extensible.** The `ui5:action`, `ui5:card`, `ui5:report`, `ui5:resource`,
  `ui5:tile`, and `ui5:chart` generators still create the same files the same way — but the one
  class each generates to hold your logic (a handler, a provider) can now be supplied by a package
  built on Core, so a higher-level toolkit can scaffold a richer starting point for that class
  without replacing the command or changing how you run it. If you use Core on its own, the
  generators behave exactly as before.

## [2.0.0] - 2026-07-12 — Artifacts name their code by class

The first major since 1.0. One thing changes in how you write an artifact — a mechanical,
one-line edit per class — and in return the declaration style gets cleaner and the platform
gains the groundwork for the richer action feedback below. If you don't touch actions, cards,
resources, reports, tiles, charts, or apps directly, there is nothing to do.

### Changed — a one-line migration per artifact

- **An artifact now *names* the class behind it instead of building it.** Where an action
  handed back its handler — or a card / resource / report / tile / chart handed back its
  provider, or an app its manifest — it now returns that **class name** and lets the platform
  create it:

  ```php
  // before
  public function getHandler(): ActionHandlerInterface { return app(CreatePartner::class); }
  // after
  public function getHandler(): string { return CreatePartner::class; }
  ```

  The same shape applies to `getProvider`, `getTileProvider`, `getChartProvider`, and an app's
  `getLaravelUiManifest`. It reads truer — a declaration just declares, and the platform owns the
  wiring — and it is what lets an action be dispatched with the richer contract the new feedback
  builds on. Every generator (`ui5:action`, `ui5:card`, … `ui5:app`) already emits the new shape,
  so freshly scaffolded code is correct out of the box; existing artifacts each take the one-line
  edit above when you move to 2.0.

### Added

- **Actions can declare their form request.** An action may now name the request class that
  validates its body — or `null` when it carries none — so the validation contract is explicit at
  the declaration rather than implied by the handler's signature. Entirely optional: handlers that
  type a request parameter keep validating exactly as before.

- **Richer results from an action call — messages and automatic refresh.** When your backend
  returns business messages — each with a severity, its own text, and an optional field it points
  at — or tells the call which data it changed, the platform now acts on them for you: messages
  land in the form's message area (and on the field itself, when targeted), and the lists showing
  the changed data refresh on their own — all from the single action call you already make. It
  stays completely dormant until your backend sends these, so nothing existing changes in the
  meantime, and a ready-to-mount message popover is included for a form header.

## [1.4.0] - 2026-07-10 — Validation errors land on the right fields

A small, additive release: when a form submission is rejected, the platform can now show
the errors **on the fields themselves**.

### Added

- **Server validation, shown inline.** Hand a form's model to the action call, and any
  validation errors your backend returns are placed directly on the matching fields — each
  one turns red with its message, and the full list is available to a message summary — with
  no per-field wiring and no separate client-side validation rules to keep in sync. The rule
  is simply that a field's name matches its place in the form, including rows in a table, so a
  message about "the third row's rate" finds the third row's rate. Your Laravel validation
  stays the single source of truth; the form just reflects it. Submissions that fail for other
  reasons still surface as before, so nothing existing changes until you opt a form in.

## [1.3.0] - 2026-07-08 — Export a table to a file

A small, additive release: your app can now let people **download a table as a file**.

### Added

- **One-line table export.** Point the platform at a list — carrying whatever filters and
  search the user has applied — and it hands back the matching rows as a file, saved straight
  to the browser with no extra tab and no page navigation. The work happens on the server: it
  collects the *full* filtered set (not just the rows currently loaded on screen), builds the
  file, and confirms the user is allowed to export — so your front-end stays a single call. On
  Core alone the entry point is present but inert; it activates once you add the SDK, which
  provides the export itself. Write against it now and it lights up when the SDK is there.

## [1.2.0] - 2026-07-07 — Groundwork for intent-based navigation

A small, additive release: it lays the client-side groundwork for **intent-based
navigation** and smooths a TypeScript rough edge.

### Added

- **A navigation-intent entry point on the client facade.** Your app can now express
  a navigation as an *intent* — "open this dialog," "switch to that app" — and let the
  platform carry it out, instead of constructing the destination itself. On Core alone
  it is inert (there is nothing to service it yet); it activates once you add the SDK,
  whose shell authorizes and performs the navigation. Write against it now and it lights
  up when the SDK is present — nothing existing changes in the meantime.

### Fixed

- **Extending the platform's base component in TypeScript compiles cleanly again.**
  Apps that build on the base component — for example, to host a global dialog — now
  typecheck, with the dialog-open helper properly typed. The type previously omitted its
  UI5 component lineage, which broke strict-TypeScript builds.

## [1.1.0] - 2026-07-03 — Compose dashboards across modules

Dashboards are no longer confined to the module that defines them. A module you
`composer require` can now contribute its own Tiles and Cards into **another**
module's dashboard group — without that dashboard knowing about it or being
edited. Add a KPI from a new module onto an existing dashboard by registering a
single line in your service provider, and the dashboard picks it up
automatically. It's composition across packages, the way the rest of LaravelUi5
already works — and the first minor release on the stable line.

### Added

- **Cross-module dashboard composition.** A new collector lets any module place a
  Tile or Card into another module's dashboard group by namespace. Contributions
  render after the group's own children, in registration order; a dashboard that
  receives none behaves exactly as before, so nothing existing changes.

### Changed

- Core now runs on **`laravelui5/odata` 2.x** as well as 1.x — no change to your
  code either way.

## [1.0.6] - 2026-06-27 — Multiple apps from one package

A single Composer package can now ship more than one UI5 application. Alongside
the existing single-app conventions, Core resolves each app in a multi-app
package from its own `resources/ui5-<name>` folder, with the app's module living
in a matching sub-namespace. Single-app packages are unaffected — the new layout
is additive and opt-in.

### Added

- **Multi-app package source resolution.** Ship several apps from one package,
  each served from a dedicated `resources/ui5-<name>` directory (the name derives
  from the app's module). The convention is apps-only.

> This convention exists for the SDK and its bundled apps. For your own packages,
> we recommend the one-app-per-package layout.

## [1.0.5] - 2026-06-25 — Asset hardening + config documentation

A security fix and a documentation pass. In deployed environments Core now serves
only built bundles (`*-preload.js`), stylesheets, and i18n property files over
HTTP — raw module source and source maps are refused. The hardening is automatic
everywhere except `local` and `testing` and needs no configuration (the previous
opt-in key, which no host actually set, has been removed). The published
`config/ui5.php` is also now documented on every key.

### Security

- **Raw UI5 source is no longer served in deployed environments** — only built
  bundles, CSS, and `.properties`. The gate is driven by the application
  environment instead of a config flag, closing a gap where deployed systems
  could serve raw modules and source maps.

### Changed

- `config/ui5.php` is fully documented; the obsolete `ui5.active` key is removed.

## [1.0.4] - 2026-06-09 — Per-request manifest extensions

A new opt-in seam lets a manifest tailor its own `sap.ui5/extends/extensions`
node per request and acting user — for example, flipping view-element visibility
based on the current user's permissions. Existing manifests are untouched; the
seam runs only for manifests that opt in.

### Added

- **`Ui5ManifestExtensionInterface`** — implement it on a manifest to receive and
  rewrite its `extensions` node at request time. Only the extensions node is
  exposed; Core stays stateless about identity.

## [1.0.3] - 2026-06-09 — Cacheable registry data objects

A new contract lets custom data objects survive the registry cache losslessly.
Previously only modules and artifacts round-tripped through the cache cleanly; a
plain data object could be flattened to its class name. Implement the contract
and the cache serializes and rebuilds it structurally.

### Added

- **`CacheSerializable`** (`toCache(): array` / `static fromCache(array): static`)
  — opt-in, scalar-only structural caching for registry data objects. Additive
  and non-breaking.

### Changed

- The `ui5:app` base/leaf migration hint is now presented as a styled manual.

## [1.0.2] - 2026-06-05 — `ui5:app --refresh` preserves your code

`ui5:app --refresh` no longer overwrites hand-authored members of a generated App
class. Each app is now split into a framework-owned base and a developer-owned
leaf: a refresh rewrites only the base — re-syncing title, description, and
bootstrap details after a UI5 rebuild — while your identity, OData configuration,
access gates, and anything else you added in the leaf are never touched.

### Changed

- **`ui5:app` generates a base/leaf pair** — `{App}AppBase` (framework-owned,
  rewritten on refresh) and `{App}App` (yours, created once). `--refresh` only
  ever writes the base.

### Migration

- Running `--refresh` on an app created before this release creates the base from
  current source, leaves your existing class untouched, and prints the members to
  relocate into the base. Until you move them, your class harmlessly overrides the
  base — nothing breaks mid-migration.

## [1.0.1] - 2026-06-04 — Honest 401 for the OData layer

An expired or missing session on an OData request now returns a proper `401` with
an OData error envelope instead of a redirect to the login page. Because the OData
layer is only ever called by the UI5 data model (never browser-navigated), the old
redirect was parsed as data and surfaced as a misleading `Expected 'OData-Version'
header` error. Clients can now detect a real authentication failure and
re-authenticate gracefully. App (HTML) routes still redirect to login as before.

### Fixed

- **`EnsureODataAuthenticated` returns `401` JSON** on an unauthenticated request;
  the app/index route keeps its `302 → login`. The rule: OData → 401 JSON; app
  HTML → 302.

## [1.0.0] - 2026-06-03 — First stable release

Core 1.0.0 is the first stable release. The artifact contract surface is now
frozen under Semantic Versioning — breaking a frozen contract requires a major
release. Every public surface carries an honest stability label.

### Frozen

- The artifact APIs — Application, Library, Module, Card, Tile, Chart, Dashboard,
  Report, and Action — together with the Parameter API, OData service
  integration, infrastructure contributions, and the command/scaffolding API.

### Provisional

- The Resource API (stable in practice, not yet contract-frozen) and the
  SDK-bound artifact types — AnalyticTile, Dialog, ValueHelp, AnalyticsSet, and
  AnalyticCard — which Core carries as vocabulary only.

## Pre-1.0.0 (0.9.x)

The 0.9.x line (April–June 2026) was Core's in-house development toward the 1.0.0
contract freeze and was not published as a public release. It introduced the BSL
1.1 license and the move to the `laravelui5/odata` engine, and stabilized the
artifact, parameter, dashboard, and scaffolding surfaces that 1.0.0 froze.
