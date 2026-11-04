# WP Review: Elementify (theme)

- **Target:** `wp-content/themes/elementify` (classic theme, v1.0.0, requires WP 6.4 / PHP 7.4, tested up to 7.1)
- **Security level:** standard
- **Date:** 2026-11-04
- **Scope:** 44 theme PHP files, `assets/src/js`, `theme.json` and bundled fonts, including uncommitted working-tree changes (Inter font bundle); `vendor/`, `node_modules/`, `tests/`, `dist/`, `assets/build/` and `.claude/` excluded

> The skill's PowerShell engine (`run-review.ps1`, rules, module) is not present in `.claude/skills/wp-review/`, and PowerShell is not installed, so this review was run by hand with equivalent checks: the WPCS security and WordPress.org sniffs via PHPCS, pattern searches for the skill's standard-level rules, the required-features checks Theme Check runs, and a manual read of every hit. Scores use the skill's documented formula.

## Score

| | Score | Grade |
|---|---|---|
| Security | **100** / 100 | A |
| Standards | **98** / 100 | A |
| **Combined** (70 / 30) | **99** / 100 | **A** |

**Verdict: pass.** No security findings and two low standards findings. All 10 findings from the previous review are resolved.

## Security

No findings.

| Check | Result |
|---|---|
| Output escaping (`WordPress.Security.EscapeOutput`) | Clean |
| Input sanitisation, nonces (`ValidatedSanitizedInput`, `NonceVerification`) | Clean. No forms, AJAX handlers or REST routes. |
| SQL (`WordPress.DB.PreparedSQL`) | No direct `$wpdb` use |
| eval, obfuscation, shell, unserialize, remote calls, TLS, secrets, redirects, cookies | None found. The theme no longer reads any cookie. |

Pattern hits checked by reading the code and judged harmless:

- `assets/src/js/main.js:122`: `innerHTML` assigns a fixed string. The dynamic label is set with `textContent`.
- `inc/helpers/autoloader.php:82`: `require_once` of a validated path inside the theme directory.
- `inc/classes/class-assets.php:208`, `inc/classes/class-woocommerce.php:514`: `require` of fixed build manifests.

## Standards

### Low

**1. 404 heading order skips levels**
`inc/helpers/template-functions.php:744`: the "Error code: 404" line is an `<h6>` placed before the page's `<h1>`. Heading levels should describe structure, not styling.
*Fix:* make it `<p class="entry-sub-title">` and give `.entry-sub-title` the `h6` styles it relies on now.

**2. 404 image has no dimensions**
`inc/helpers/template-functions.php:752`: the `<img>` has no `width`/`height`, so the layout shifts while it loads.
*Fix:* add the image's intrinsic `width` and `height`. Its `data-ratio="4x3"` wrapper suggests 4:3.

### Passed

| Area | Result |
|---|---|
| Theme headers (`style.css`) | Valid licence URI (GPL v2 URL), `https` URIs, version, requirements and text domain match `readme.txt` and `package.json` |
| `readme.txt` | Contributors, description, FAQ, changelog, and Copyright and Images sections crediting every bundled resource |
| Screenshot | 1200 × 900 |
| Internationalisation (`WordPress.WP.I18n`, text domain `elementify`) | Clean; no literal UI strings left in templates |
| Prefixing | Theme hooks use `elementify_`. The remaining unprefixed calls are core filters (`the_content`, `the_title`, `page_css_class`, `post_type_archive_title`) and are annotated. |
| Enqueuing, remote assets, plugin territory | No hard-coded `<script>`/`<link>` tags, no CDN or Google Fonts calls, no CPTs, shortcodes or analytics |
| Required features | `title-tag`, `automatic-feed-links`, `html5`, `wp_body_open`, `comment-reply`, `wp_link_pages`, `post_class`, `body_class`, skip link, editor styles, `theme.json` |
| Declared tags | `custom-logo`, `custom-background`, `custom-menu`, `featured-images`, `threaded-comments` and `translation-ready` all have matching support in the code |
| Images | All `<img>` tags have `alt`; every page type has exactly one `<h1>` |
| Bundled fonts | Inter 5.3.0 (SIL OFL 1.1, GPL-compatible) in `assets/src/fonts/inter`, copied to `assets/build/fonts` and shipped in the release zip; `OFL.txt` included; credited in `readme.txt`; all four `theme.json` `fontFace` sources exist; `font-display: swap` with unicode-range subsets; no remote font requests |
| Build | A production `pnpm build` leaves `assets/build` matching the sources apart from the uncommitted font change, so CI's stale-build check will pass once it is committed |

### False positives

- **`PrefixAllGlobals` "global variables" (75 hits, all in `template-parts/`):** these files load only through `get_template_part()` → `load_template()`, which `require`s them inside a function, so their variables are local, not globals. Top-level templates such as `index.php` and `single.php` do run at global scope, but they produced no hits.
