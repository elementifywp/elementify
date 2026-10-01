# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Elementify is a **classic** (PHP-template, not block/FSE) WordPress blog theme, originally based on Underscores. The minimum supported versions are PHP 7.4 and WordPress 6.4, declared in `style.css` and `readme.txt`. Do not use PHP 8-only syntax in theme code. PHPCompatibility (`testVersion 7.4-`) and PHPStan (`phpVersion.min 70400`) enforce this.

## Toolchain and commands

Use Node 24 (`nvm use` reads `.nvmrc`; `@wordpress/scripts` 36 needs Node 22.22 or later) and **pnpm only**. The version is pinned in `packageManager`. Build-script approvals and security `overrides` live in `pnpm-workspace.yaml`. Do not create `package-lock.json` or `yarn.lock`.

```sh
pnpm install && composer install
pnpm start                 # watch build
pnpm build                 # production build -> assets/build (committed to git; CI fails if stale)
pnpm lint                  # lint:js + lint:css + lint:pkg + lint:php in parallel
pnpm lint:js:fix | lint:css:fix | lint:php:fix
pnpm analyze:php           # PHPStan level 5 (composer run phpstan)
composer run lint          # parallel-lint syntax check + phpcs
vendor/bin/phpcs inc/classes/class-assets.php   # PHPCS on one file
pnpm env:start / env:stop  # wp-env (Docker): dev site :8888, tests site :8889, admin/password
pnpm test:e2e              # Playwright against the wp-env *tests* site (:8889)
pnpm test:e2e -- tests/e2e/specs/admin.spec.js -g "dashboard"   # single spec / single test
pnpm release               # clean build + make-pot (WP-CLI) + wp-scripts plugin-zip -> elementify.zip
```

- On this macOS 13 machine, Playwright ships no Chromium build. Run E2E with `PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 PLAYWRIGHT_CHANNEL=chrome pnpm test:e2e`.
- wp-env's `afterStart` activates the theme on both sites. Tests log in via REST using `@wordpress/e2e-test-utils-playwright` (`admin`, `requestUtils` fixtures).
- PHPStan uses `phpstan-baseline.neon` for pre-existing issues and `tests/phpstan/bootstrap.php` to define the `ELEMENTIFY_*` constants. When you fix a baselined issue, regenerate the baseline with `vendor/bin/phpstan analyse --memory-limit=2G --generate-baseline phpstan-baseline.neon`.
- Existing code does not yet pass PHPCS, Stylelint or ESLint. There are thousands of auto-fixable formatting violations. Lint the files you touch rather than expecting a clean `pnpm lint`.
- The release zip contents come from the `files` field in `package.json`, not from an ignore list.

## Build pipeline

`webpack.config.js` extends the `@wordpress/scripts` default config. Its entries are `js/main`, `js/customizer` and `css/main`. Each output gets a sibling `*.asset.php` manifest (dependencies and content hash). `css/main` also gets `main-rtl.css`. Images in `assets/src/images` are copied verbatim.

There is no Sass and no Tailwind (Tailwind was deliberately removed). `assets/src/css/main.css` is about 11k lines of hand-written CSS. It uses `--ele-*` custom properties and `ele-` prefixed utility-like classes. Utility class names in templates only work if `main.css` defines them.

`postcss.config.js` and `babel.config.js` exist at the project root, so wp-scripts uses them instead of its built-in defaults. `postcss.config.js` therefore has to add cssnano itself for production builds.

## PHP architecture

- **Bootstrap:** `functions.php` defines `ELEMENTIFY_*` constants and requires `inc/helpers/{autoloader,template-functions,template-tags,functions}.php`. It then calls `elementify_get_theme_instance()`, which instantiates `Elementify\Inc\Elementify` (`inc/classes/class-elementify.php`). That class registers theme supports and boots the singleton services: `Assets`, `Utils`, `Customizer`, `Menus` and `Sidebars`.
- **Autoloading** is a custom PSR-like loader (`inc/helpers/autoloader.php`), not Composer:
  - `Elementify\Inc\Foo_Bar` maps to `inc/classes/class-foo-bar.php`.
  - `Elementify\Inc\Traits\X` maps to `inc/traits/trait-x.php`.
  - `Inc\Widgets\X` and `Inc\Blocks\X` map to `inc/classes/{widgets,blocks}/class-x.php`.
  - Service classes `use Elementify\Inc\Traits\Singleton` and are obtained with `::get_instance()`.
- **Templates are hook-driven.** Root templates (`index.php`, `archive.php`, `header.php`, etc.) contain little markup. They fire namespaced actions such as `elementify/before_content`, `elementify/content_top`, `elementify/header` and `elementify/content/before_loop`. The callbacks that render markup are registered in `inc/helpers/template-functions.php`, often by including `template-parts/...`. To change what a page outputs, find the hook callback there; the template file itself usually won't have it. Each `do_action` docblock lists its `@hooked` callbacks and priorities. Keep those comments in sync.
- **Template parts:** `template-parts/content-*.php` are loop entries. `template-parts/components/` (and `components/blog/`) hold the pieces: entry header, meta, image, title, read-more and so on.
- **Helper layers:**
  - `inc/helpers/functions.php`: site identity, nav menus, SVG icon accessors, pagination, breadcrumbs.
  - `inc/helpers/template-tags.php`: thumbnails, posted-on/by, excerpts, entry footer.
  - `Utils`: `clsx` class-name builder and attribute-string helpers.
  - `Svg_Icons`: an inline SVG icon registry.
  - `Breadcrumb_Trail`: a vendored breadcrumb library.
- **Hard-coded settings:** many components contain "settings" arrays such as `$header_preset = ['desktop' => '1']`. These are placeholders for future Customizer options. Most of the PHPStan baseline consists of "always true/false" findings caused by them.
- **Assets:** `Assets` enqueues `assets/build/...`, reading version and dependencies through `get_asset_meta()` from the `*.asset.php` manifests. The main script loads deferred. The customizer preview script depends on `customize-preview` and `jquery`.
- **Naming:** text domain `elementify`. Global prefixes are `elementify_` (functions), `ELEMENTIFY_` (constants) and `Elementify` (classes), enforced by PHPCS `PrefixAllGlobals`. Some legacy functions break this (`bizness_get_nav_menus`, `twenty_twenty_one_*`, the misspelled `elemetify_pagination`). `wpml-config.xml` lists the translatable `theme_mods_elementify` keys; update it when adding text Customizer settings.

## Known inconsistencies

- The `ELEMENTIFY_BUILD_*` and `ELEMENTIFY_IMG_URI` constants point at `/build`, but the real output is `/assets/build`.
- `add_editor_style('build/css/main.css')` in `class-elementify.php` uses the same wrong path, so the editor stylesheet doesn't load.