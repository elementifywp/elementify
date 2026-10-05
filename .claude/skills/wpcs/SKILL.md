---
name: wpcs
description: Lint and auto-fix PHP code against WordPress Coding Standards (WPCS) using phpcs and phpcbf. Use whenever the user asks to check, lint, review, or clean up PHP/WordPress plugin or theme code against WordPress standards, or mentions "phpcs", "phpcbf", "WPCS", or "WordPress coding standards".
---

# WordPress Coding Standards (WPCS)

Lint and auto-fix PHP files or directories against the WordPress Coding
Standards ruleset, using `phpcs` (reports issues) and `phpcbf` (auto-fixes
what it safely can).

## Prerequisites

Before running anything, verify the tools are available:

```bash
phpcs --version
phpcbf --version
phpcs -i   # confirms the WordPress standard is registered
```

If `WordPress` doesn't appear in the installed standards list, install it
first (requires Composer and network access to Packagist — if that's not
available in this environment, tell the user and stop rather than guessing):

```bash
composer global require wp-coding-standards/wpcs
composer global require dealerdirect/phpcodesniffer-composer-installer
```

Do not silently skip this check — a missing standard causes `phpcs` to error
out in a way that looks like "no issues found," which is misleading.

## Workflow

1. **Identify the target.** Use the file or directory path the user gave you.
   If they didn't name one, ask which file/directory, or infer it from
   context (e.g. a file they just edited).
2. **Lint first, always** — even if the user asked for a fix — so you can
   report what's being changed:
   ```bash
   phpcs --standard=WordPress <path>
   ```
3. **If the user wants fixes applied**, run:
   ```bash
   phpcbf --standard=WordPress <path>
   ```
   Note `phpcbf` can only auto-fix a subset of issues (mostly formatting/
   whitespace/spacing rules). Anything it can't fix will still show up if you
   re-run `phpcs` afterward.
4. **Re-run `phpcs`** after `phpcbf` to show the user what remains
   unresolved and needs manual attention.
5. **Summarize results** in plain language: how many errors/warnings found,
   how many were auto-fixed, and a short list of what still needs manual
   review (with file:line references from the `phpcs` output).

## Notes

- Default to `--standard=WordPress`. If the user's project has a
  `phpcs.xml` or `phpcs.xml.dist` in its root, prefer that instead (it may
  reference `WordPress-Extra` or `WordPress-Docs`, or exclude specific
  sniffs) — check for it before assuming the bare `WordPress` ruleset.
- Never modify files with `phpcbf` without telling the user first that it
  will rewrite files in place.
- For large directories, consider `--report=summary` first to gauge scope
  before dumping full output.
