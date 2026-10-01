---
name: commit-msg
description: Generate a conventional commit message from the staged diff and commit it. Use when the user says "write a commit message", "generate a commit", "commit my changes", or runs /commit-msg.
---

# Commit message generator

Workflow:

1. Run `git diff --staged` (or `git diff --staged --stat` first if the diff is large) to check for staged changes.
   - If there is nothing staged, stop immediately and tell the user to stage their changes first (e.g. with `git add`). Do not commit, and do not fall back to unstaged changes.
2. Read the full staged diff to understand what changed and why.
3. Generate a commit message in this exact format:

   ```
   type(scope): short subject

   - bullet of what changed
   - bullet of why
   ```

   - `type` is one of: `feat`, `fix`, `refactor`, `chore`, `docs`, `style`, `test`.
   - `scope` is a short identifier for the affected area (e.g. a directory, module, or feature name) inferred from the diff. Omit the `(scope)` parentheses if no single scope fits.
   - `subject` is under 60 characters, imperative mood, no trailing period.
   - Body bullets are optional but encouraged — include them unless the change is trivial (e.g. a one-line typo fix). Keep bullets concise; cover what changed and, when it's not obvious from the diff alone, why.
   - Never include a `Co-Authored-By` trailer or any other trailer.
4. Run `git commit -m "$(cat <<'EOF'
   <message>
   EOF
   )"` with the generated message (heredoc keeps formatting/quoting safe).
5. Confirm the commit succeeded (e.g. show the resulting `git log -1` or `git status`).

Do not stage additional files yourself — only commit what is already staged.
