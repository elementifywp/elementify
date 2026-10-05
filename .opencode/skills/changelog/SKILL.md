---
name: changelog
description: Generate or update CHANGELOG.md from today's git commits, grouped into Added/Fixed/Changed/Removed sections under a dated version entry, matching the project's existing changelog format. Use when the user says "update the changelog", "generate a changelog", "add today's commits to the changelog", "changelog for today", or runs /changelog.
---

# Changelog generator

Turn a day's worth of commits into a new dated entry in `CHANGELOG.md`,
written for humans (not a raw commit dump).

## Workflow

1. **Find the changelog file.** Look for `CHANGELOG.md` at the repo root.
   If it doesn't exist, create one with this header:
   ```markdown
   # Changelog

   All notable changes to this project are documented in this file.

   ```

2. **Get today's commits.** Determine today's date range and run:
   ```bash
   git log --since="midnight" --until="now" --pretty=format:"%H%x09%s%x09%b%x09%an" --no-merges
   ```
   - If the user specifies a different day or range ("yesterday's commits",
     "commits since Monday"), adjust `--since`/`--until` accordingly.
   - If there are no commits in range, tell the user and stop — don't
     fabricate an entry.
   - Exclude merge commits unless the user asks to include them.

3. **Read the full diff of each commit** (`git show <hash>`) when the
   subject line alone doesn't make the actual change clear enough to
   describe accurately. Don't guess at behavior from the subject line if
   the diff would clarify it.

4. **Classify each commit** into a changelog section based on its content
   and, if present, its Conventional Commit type prefix (`feat`, `fix`,
   `refactor`, `chore`, `docs`, `style`, `test`):
   - `feat` → **Added**
   - `fix` → **Fixed**
   - `refactor`, `perf`, `style`, `chore`, `docs`, config/tooling/dependency
     changes → **Changed**
   - deletions of features/files/flags → **Removed**
   - If a commit doesn't use a conventional prefix, classify by what the
     diff actually does, not by guesswork from the message alone.
   - Skip purely mechanical commits (e.g. "wip", "typo", formatting-only
     commits with no user-facing effect) unless the user wants full detail —
     ask if unsure whether to be terse or exhaustive.

5. **Group and rewrite each commit as a changelog bullet:**
   - One bullet per logical change (merge multiple commits describing the
     same change into one bullet; don't just list raw commit subjects).
   - Written in plain, past-tense-neutral description style matching the
     example below — lead with the area/component in bold or backticks
     when it aids scanning, followed by a colon and the specific change.
   - Concise: one line where possible, wrap only when necessary to convey
     specifics (e.g. which function or file was affected).
   - Never copy raw commit subject lines verbatim if they're a code-y
     shorthand ("fix: bg_image null check") — expand into a real sentence
     ("Fixed an undefined array offset warning when a `bg_image`
     attachment is missing...").

6. **Determine the version number** for the new entry:
   - Look at the most recent `## [x.y.z]` entry in the existing changelog.
   - If any commit indicates a breaking change (`BREAKING CHANGE`, `!` after
     type/scope, or explicit user instruction) → bump major.
   - Else if any commit is a `feat` → bump minor.
   - Else (only `fix`/`chore`/etc.) → bump patch.
   - If the user tells you the version explicitly, use that instead of
     inferring it.

7. **Format the new entry** matching the project's existing style exactly
   (heading level, section names, bullet style, date format). Example:
   ```markdown
   ## [2.0.1] - 2026-09-02

   ### Added
   - Description of new capability.

   ### Fixed
   - Description of the bug and what specifically triggered it.

   ### Changed
   - Description of the behavior/config/dependency change.

   ### Removed
   - Description of what was removed and why, if evident from the diff.
   ```
   Omit any section with no entries — don't emit an empty `### Removed`
   heading if nothing was removed.

8. **Insert the new entry** directly below the changelog's header (and
   above the previous most-recent entry), preserving all existing entries
   below unchanged.

9. **Show the user a summary** of what was added (number of commits
   processed, sections populated, inferred version bump) before/after
   writing the file, so they can correct the version or grouping if needed.

## Notes

- If commits span multiple calendar days and the user asked for "today,"
  only include today's commits — don't silently expand the range.
- If the same fix/feature was committed and then amended/reverted within
  the day, collapse it into one accurate net-effect bullet rather than
  listing both.
- Never invent changes not evidenced by the commit diffs.
