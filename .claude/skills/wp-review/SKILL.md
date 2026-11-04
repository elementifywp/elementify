---
name: wp-review
description: >
  WordPress theme and plugin review skill. Detects whether a target path is a
  theme or plugin, runs security and standards checks, scores the findings, and
  writes a markdown report. Use whenever the user wants to review, audit, scan
  or check a WordPress theme or plugin directory, asks whether WordPress code is
  secure or ready for WordPress.org submission, mentions nonces, escaping,
  sanitization or SQL injection in a theme or plugin, or wants a basic, standard
  or high strictness security review, even if they do not say "review".
compatibility: Windows PowerShell 5.1 or PowerShell 7+. Built for Claude Code on Windows; installed at %USERPROFILE%\.claude\skills\wp-review.
allowed-tools: Read, Grep, Glob, Bash, Write
---

# WP Review

Use this skill when the user provides a path to a WordPress theme or plugin and wants a review report.

## When To Use

Use `wp-review` when the user asks to:

- review a WordPress theme or plugin
- scan a WordPress project for security issues
- check a theme or plugin before submitting it to WordPress.org
- generate a WordPress review report
- run a basic, standard, or high security review

If the user gives only a project path, default to a markdown report with `standard` security.

## Commands

Unified dispatcher:

```powershell
powershell -ExecutionPolicy Bypass -File "$env:USERPROFILE\.claude\skills\wp-review\run-review.ps1" -Path "C:\path\to\wordpress-project"
```

Full review (high security level plus standards):

```powershell
powershell -ExecutionPolicy Bypass -File "$env:USERPROFILE\.claude\skills\wp-review\run-full-review.ps1" -Path "C:\path\to\wordpress-project"
```

Quick security-only review (basic level, standards skipped):

```powershell
powershell -ExecutionPolicy Bypass -File "$env:USERPROFILE\.claude\skills\wp-review\run-quick-review.ps1" -Path "C:\path\to\wordpress-project"
```

Security review strictness:

```powershell
powershell -ExecutionPolicy Bypass -File "$env:USERPROFILE\.claude\skills\wp-review\run-review.ps1" -Path "C:\path\to\wordpress-project" -SecurityLevel basic
powershell -ExecutionPolicy Bypass -File "$env:USERPROFILE\.claude\skills\wp-review\run-review.ps1" -Path "C:\path\to\wordpress-project" -SecurityLevel standard
powershell -ExecutionPolicy Bypass -File "$env:USERPROFILE\.claude\skills\wp-review\run-review.ps1" -Path "C:\path\to\wordpress-project" -SecurityLevel high
```

JSON output:

```powershell
powershell -ExecutionPolicy Bypass -File "$env:USERPROFILE\.claude\skills\wp-review\run-review.ps1" -Path "C:\path\to\wordpress-project" -SecurityLevel standard -PrintJson
```

### Parameters (run-review.ps1)

| Parameter | Effect |
|---|---|
| `-Path` | Theme or plugin directory, or the plugin main file. Required. |
| `-SecurityLevel` | `basic`, `standard` (default) or `high`. |
| `-PrintJson` | Print the full result as JSON on stdout instead of the console summary. The markdown report is still written. |
| `-CriticalOnly` | Skip the standards review. It does not filter security findings by severity. |
| `-OutputPath` | Write the markdown report somewhere other than `<Path>\review.report.md`. |
| `-NoMarkdown` | Do not write the markdown report (useful with `-PrintJson`). |
| `-IncludeVendor` | Also scan `vendor\` and `node_modules\` (excluded by default). |

`run-full-review.ps1` and `run-quick-review.ps1` accept `-Path`, `-PrintJson`, `-OutputPath`, `-NoMarkdown` and `-IncludeVendor`.

Exit codes: `0` review completed (regardless of findings), `2` path missing or not a theme/plugin, `3` internal error (message on stderr).

## Workflow

1. Verify the provided path exists. If the user points at a whole WordPress install, ask which theme or plugin under `wp-content` to review.
2. Default to `standard` security unless the user explicitly asks for `basic` or `high`.
3. Run `run-review.ps1` with the requested security level (or the full or quick script when asked).
4. Prefer markdown output unless the user explicitly asks for JSON.
5. If markdown output is used, tell the user where `review.report.md` was written and give the score, grade and verdict from the console summary.
6. If JSON output is used, summarize the main findings and score instead of dumping raw JSON unless the user asked for it.
7. Triage before presenting conclusions. For every `critical` and `high` finding (up to about ten), open the file at the reported line and confirm it is real. Static matching cannot see data flow: values may be sanitized on an earlier line, and file-level rules such as `nonce-missing-ajax` fire when the check lives in another file. Tell the user which findings you confirmed, which look like false positives, and why.
8. Offer to fix confirmed issues, using the `Fix` text of each finding as the starting point.

Use `run-full-review.ps1` when the user wants the broadest currently implemented pass.
Use `run-quick-review.ps1` when the user wants a fast `basic` security-only pass.

## Security Levels

- `basic`: high-confidence checks only, quietest output (eval, obfuscation, request input reaching SQL, shell, include, unserialize, echo, auth cookies)
- `standard`: balanced default; adds nonce and capability checks, interpolated SQL, unsanitized input, REST permissions, redirects, TLS, secrets, JS eval
- `high`: broader, noisier triage; adds unescaped variable output, variable queries and includes, base64, innerHTML, jQuery HTML sinks, nopriv AJAX

Recommended: `basic` for quick confidence checks with few false positives, `standard` for normal runs, `high` for exploratory review when the user accepts noisier findings.

## Scoring

Security and standards are each scored out of 100. Each rule deducts per occurrence (critical 25, high 10, medium 4, low 1, info 0), counting at most three occurrences per rule so one noisy rule cannot zero the score. Combined score is 70% security and 30% standards, or security alone when standards are skipped. Grades: A 90+, B 80+, C 70+, D 60+, F below. Verdict is `fail` if any critical security finding exists, `needs-work` if any high security finding exists or the combined score is under 80, otherwise `pass`.

## Output

- Markdown report: `review.report.md` in the target project directory by default, with findings grouped by severity and rule, file and line locations, snippets, and fix guidance
- JSON: theme/plugin fingerprint (type, subtype, headers), settings, scan stats, security findings, standards findings, and combined score

## Files

```
wp-review/
  SKILL.md
  run-review.ps1             dispatcher and console output
  run-full-review.ps1        high level + standards
  run-quick-review.ps1       basic level, security only
  lib/WpReview.psm1          detection, scanning, scoring, markdown writer
  rules/security-rules.psd1  security rules (data)
  rules/standards-rules.psd1 standards rules (data)
```

## Adding or tuning rules

Rules are data in `rules/*.psd1`; the engine does not need changing. Each rule is a hashtable with `Id`, `Title`, `Severity`, `Message`, `Fix` and either:

- a line rule: `Pattern` (regex matched per code line) and optional `Unless` (suppresses the match if it also matches the line), or
- a file rule: `Require` (array; all must appear in the file) and optional `Absent` (must not appear).

Optional keys: `Level` (security only: basic, standard, high), `Confidence`, `Category`, `Cwe`, `Extensions` (default `.php`, `.inc`, `.phtml`), `AppliesTo` (`any`, `theme`, `plugin`), `Group` (only the most severe finding of a group is kept per line), `Fallback = $true` (dropped when another line rule already flagged that line). Patterns are case-insensitive .NET regex in single-quoted strings, so write a literal single quote as two single quotes. Full-line comments and docblocks are skipped before matching.

When the user reports a false positive, prefer tightening `Unless` on the rule over deleting it, and tell them which rule you changed.