# Elementify

Elementify is a sleek and minimalist WordPress blog theme.

## Requirements

- Node.js 24 (`nvm use`) and pnpm (the version is pinned in `package.json` and fetched automatically)
- PHP 8.x and Composer for the QA tools. The theme itself supports PHP 7.4 and later.
- Docker, for the local wp-env WordPress site and E2E tests

## Setup

```sh
pnpm install
composer install
```

## Development

| Command | What it does |
| --- | --- |
| `pnpm start` | Watch and rebuild `assets/src` into `assets/build` |
| `pnpm build` | Production build (minified CSS/JS, RTL stylesheet, `*.asset.php` manifests) |
| `pnpm env:start` | Start WordPress with the theme active: dev site at http://localhost:8888, tests site at :8889 (login `admin` / `password`) |
| `pnpm lint` | ESLint, Stylelint, package.json lint and PHPCS |
| `pnpm lint:js:fix`, `pnpm lint:css:fix`, `pnpm lint:php:fix` | Auto-fix coding-standard issues |
| `pnpm analyze:php` | PHPStan (WordPress extension, with a baseline in `phpstan-baseline.neon`) |
| `pnpm test:e2e` | Playwright E2E tests against the wp-env tests site |
| `pnpm i18n:pot` | Regenerate `languages/elementify.pot` (needs WP-CLI) |
| `pnpm release` | Clean build, regenerate the POT file and create `elementify.zip` |

On macOS 13 and older, Playwright no longer ships Chromium, so run the E2E tests with your installed Chrome:

```sh
PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 PLAYWRIGHT_CHANNEL=chrome pnpm test:e2e
```

The Theme Check plugin is installed in wp-env. Use it under Appearance → Theme Check before submitting to WordPress.org.
