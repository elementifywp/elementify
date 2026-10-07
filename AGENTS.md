# AGENTS.md

Guidance for coding agents working in this repository. `CLAUDE.md` points here.

## What this is

Elementify is a **classic** (PHP-template, not block/FSE) WordPress blog theme, originally based on Underscores. Minimum supported versions are PHP 7.4 and WordPress 6.4, declared in `style.css` and `readme.txt`. Do not use PHP 8-only syntax in theme code. PHPCompatibility (`testVersion 7.4-`) and PHPStan (`phpVersion.min 70400`) enforce this.

## Toolchain and commands

Use Node 24 (`nvm use` reads `.nvmrc`; `engines` in `package.json` requires `^22.22.2 || >=24.15.0`) and **pnpm only** (pinned via `packageManager`). Build-script approvals and security `overrides` live in `pnpm-workspace.yaml`. Do not create `package-lock.json` or `yarn.lock`; both are gitignored.

```sh
pnpm install && composer install
pnpm start                 # watch build
pnpm build                 # production build -> assets/build (committed to git; CI fails if stale)
pnpm lint                  # lint:js + lint:css + lint:pkg + lint:php in parallel
pnpm lint:js:fix | lint:css:fix | lint:php:fix
pnpm analyze:php           # PHPStan level 5
composer run lint          # parallel-lint syntax check + phpcs
composer run lint:syntax   # syntax only, no style rules
vendor/bin/phpcs inc/classes/class-assets.php   # PHPCS on one file
pnpm env:start / env:stop  # wp-env (Docker): dev site :8888, tests site :8889, admin/password
pnpm test:e2e              # Playwright against the wp-env *tests* site (:8889)
pnpm test:e2e -- tests/e2e/specs/admin.spec.js -g "dashboard"   # single spec / single test
pnpm i18n:pot              # regenerate languages/elementify.pot (needs WP-CLI)
pnpm release               # clean build + make-pot + wp-scripts plugin-zip -> elementify.zip
```

### Lint state (measured, not aspirational)

Running the whole suite will not come back clean. Current baseline on `main`:

| Command | State |
| --- | --- |
| `pnpm run lint:js` | passes |
| `pnpm run lint:pkg` | passes |
| `pnpm run analyze:php` | passes (baseline absorbs pre-existing issues) |
| `composer run lint:syntax` | passes |
| `pnpm run lint:css` | passes (`no-descending-specificity` is disabled in `.stylelintrc.json`; reordering selectors in `main.css` would change the cascade) |
| `vendor/bin/phpcs` | **fails**, 407 errors / 149 warnings across 43 files. Only 38 auto-fixable |

So: lint the files you touched rather than the repo, and do not treat these pre-existing counts as regressions you introduced.

Note that CI runs `vendor/bin/phpcs -q --report=checkstyle | cs2pr` without `pipefail`, so the PHP job does not actually fail on PHPCS violations, while the JS job's `pnpm run lint:css` does fail, so keep CSS lint clean.

- On macOS 13 and older, Playwright ships no Chromium build. Run E2E with `PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 PLAYWRIGHT_CHANNEL=chrome pnpm test:e2e`. `playwright.config.js` forwards `PLAYWRIGHT_CHANNEL` into every project.
- `playwright.config.js` sets `webServer.command` to `pnpm run env:start`, so Playwright boots wp-env itself. It needs Docker running.
- wp-env's `afterStart` activates the theme on both sites and installs the Theme Check plugin. Tests log in via REST using `@wordpress/e2e-test-utils-playwright` (`admin`, `requestUtils` fixtures); specs in `tests/e2e/specs/` create their own menus, posts and pages via `requestUtils`.
- PHPStan uses `phpstan-baseline.neon` for pre-existing issues and `tests/phpstan/bootstrap.php` to define the `ELEMENTIFY_*` constants. When you fix a baselined issue, regenerate with `vendor/bin/phpstan analyse --memory-limit=2G --generate-baseline phpstan-baseline.neon`.
- `phpcs.xml` and `phpstan.neon` are gitignored local overrides of the `.dist` files. Edit the `.dist` files.

## Build pipeline

`webpack.config.js` extends the `@wordpress/scripts` default config and overrides entries and output. Entries are `js/main`, `js/customizer`, `css/main` and `css/editor`; output path is `assets/build` with `clean: true`. Each entry gets a sibling `*.asset.php` manifest (dependencies and content hash), and CSS entries also get a `-rtl.css`. Images in `assets/src/images` are copied verbatim. `RemoveEmptyScriptsPlugin` drops the empty `js/main.js` webpack emits for the CSS-only entries.

There is no Sass and no Tailwind (Tailwind was deliberately removed). `assets/src/css/main.css` is about 12k lines of hand-written CSS using `--ele-*` custom properties and 3,386 `ele-` prefixed class references. Utility class names in templates only work if `main.css` defines them.

`theme.json` (version 2, because the theme supports WordPress 6.4) drives editor settings and global styles. Its palette, layout widths and shadow are `var(--ele-…, fallback)` references to the `:root` tokens in `main.css`, so the light/dark `[data-theme]` switch also recolours block presets. Core button and link element styles are set to `false`, as Kadence and Blocksy do, so `main.css` controls them. Inside `.entry-content`, `main.css` resets `--wp--style--global--content-size` to `100%`, because the post column next to the sidebar is already narrower than the 750px `contentSize`.

The block editor loads `assets/build/css/main.css` plus `assets/build/css/editor.css` via `add_editor_style()`. `editor.css` mirrors the few `main.css` rules scoped to front-end-only wrappers (the `.ele-button-fill` body class, `.entry-content`). Each mirror block cites the `main.css` rule it copies; when you change one of those rules, update its mirror.

Dark mode is server-driven: `elementify_html_attributes()` (`inc/helpers/functions.php:652`) sets `data-theme="dark"` when the `darkMode` cookie exists, else `light`. `main.css` keys everything off `[data-theme="dark"]` / `[data-theme="light"]`.

`postcss.config.js` and `babel.config.js` exist at the project root, so wp-scripts uses them instead of its built-in defaults. `postcss.config.js` therefore has to add cssnano itself for production builds.

## PHP architecture

- **Bootstrap:** `functions.php` defines `ELEMENTIFY_*` constants and requires `inc/helpers/{autoloader,template-functions,template-tags,functions}.php`. It then calls `elementify_get_theme_instance()`, which instantiates `Elementify\Inc\Elementify` (`inc/classes/class-elementify.php`). That class registers theme supports and boots the singleton services: `Assets`, `Utils`, `Customizer`, `Menus` and `Sidebars`.
- **Autoloading** is a custom PSR-like loader (`inc/helpers/autoloader.php`), not Composer:
  - `Elementify\Inc\Foo_Bar` maps to `inc/classes/class-foo-bar.php`.
  - `Elementify\Inc\Traits\X` maps to `inc/traits/trait-x.php`.
  - `Inc\Widgets\X` and `Inc\Blocks\X` map to `inc/classes/{widgets,blocks}/class-x.php`. Neither directory exists yet; the switch case falls through to `classes/` when the segment is missing.
  - Service classes `use Elementify\Inc\Traits\Singleton` and are obtained with `::get_instance()`.
- **Templates are hook-driven.** Root templates (`index.php`, `archive.php`, `header.php`, etc.) contain little markup. They fire namespaced actions such as `elementify/before_content`, `elementify/content_top`, `elementify/header` and `elementify/content/before_loop`. The callbacks that render markup are registered at the bottom of `inc/helpers/template-functions.php` (around lines 65-608), often by including `template-parts/...`. To change what a page outputs, find the hook callback there; the template file itself usually won't have it. Each `do_action` docblock lists its `@hooked` callbacks and priorities. Keep those comments in sync.
- **Template parts:** `template-parts/content-*.php` are loop entries. `template-parts/components/` holds shared pieces (entry header, meta, image, title, read-more, cats, tags, footer); `template-parts/components/blog/` holds the card variants. Also `template-parts/header/nav.php` and `template-parts/footer/copyright.php`.
- **Helper layers:**
  - `inc/helpers/functions.php`: site identity, nav menus, SVG icon accessors, pagination, breadcrumbs, html attributes.
  - `inc/helpers/template-tags.php`: thumbnails, posted-on/by, excerpts, entry footer.
  - `Utils`: `clsx` class-name builder and attribute-string helpers.
  - `Svg_Icons`: an inline SVG icon registry.
  - `Breadcrumb_Trail`: a vendored breadcrumb library, and the source of most baseline entries.
- **Hard-coded settings:** components contain "settings" arrays such as `$header_preset = ['desktop' => '1']` (`template-parts/components/entry-header.php:11`). These are placeholders for future Customizer options, which is why the surrounding `in_array`/`array_key_exists` guards read as dead code and produce "always true/false" PHPStan findings.
- **Assets:** `Assets` enqueues `assets/build/...` using literal `ELEMENTIFY_DIR_URI . '/assets/build/...'` paths, reading version and dependencies through `get_asset_meta()` from the `*.asset.php` manifests (it falls back to the theme version if the manifest is missing). `main.js` loads deferred and reads translated submenu toggle labels from `window.elementifyMenu`, injected as an inline script before it. The customizer preview script depends on `customize-preview` and `jquery`.
- **Naming:** text domain `elementify`. Global prefixes are `elementify_` (functions), `ELEMENTIFY_` (constants) and `Elementify` (classes), enforced by PHPCS `PrefixAllGlobals`. Some legacy functions break this and are a large share of the current PHPCS errors: `bizness_get_nav_menus` (`inc/helpers/functions.php:27`), `twenty_twenty_one_get_social_link_svg` and `twenty_twenty_one_nav_menu_social_icons` (`inc/helpers/functions.php:505`, `:520`), and the misspelled `elemetify_pagination` (`inc/helpers/functions.php:363`, called from `template-functions.php:323`). `wpml-config.xml` lists the translatable `theme_mods_elementify` keys; update it when adding text Customizer settings.

## Known inconsistencies

- The `ELEMENTIFY_BUILD_*` and `ELEMENTIFY_IMG_URI` constants in `functions.php` point at `/build`, but the real output is `/assets/build`. Nothing in the theme reads them (`Assets` hard-codes the correct path), so they are dead and misleading. The PHPStan bootstrap mirrors the same wrong values. Renaming them to `ELEMENTIFY_ASSETS_BUILD_*` and pointing them at `/assets/build` would be the consistent fix.
- `readme.txt` still carries Underscores boilerplate: "A starter theme called Elementify.", `Contributors: automattic`, and a 2015 changelog. Worth cleaning before a WordPress.org submission.
- `style.css` has `Author: Elementify Themes` and a `screenshot.png` of 264 bytes. Both matter for Theme Check.
- The theme claims WooCommerce and Jetpack Infinite Scroll support in `readme.txt`, but no such support is registered in `class-elementify.php`.

## Working conventions

- Repo-local skills exist in `.opencode/skills/`: `commit-msg` (conventional commits from the staged diff), `changelog` (dated `CHANGELOG.md` entries; no `CHANGELOG.md` exists yet), `php-doc-comments`, `wpcs`. Invoke them by name rather than improvising.
- Conventional Commit types: `feat`, `fix`, `refactor`, `chore`, `docs`, `style`, `test`. Historical subjects are inconsistent (`fixes`, `sytle fixes`), so follow the skill's format for new commits.
- `.gitignore` excludes `/assets/build/**/*.map`, so `pnpm start` leaves no untracked files. A production `pnpm build` does change committed files; CI's `git diff --exit-code -- assets/build` catches a stale build, so run `pnpm build` before pushing CSS or JS changes.
- The release zip contents come from the `files` field in `package.json` (`*.php`, `style.css`, `theme.json`, `screenshot.png`, `readme.txt`, `wpml-config.xml`, `assets/build`, `inc`, `languages`, `template-parts`), not from an ignore list. Anything new the theme needs at runtime must be added there.
- Dependabot runs weekly and groups `@wordpress/*` separately from other npm packages, and all Composer dev tools into one group. Expect grouped PRs.