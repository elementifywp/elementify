---
name: php-doc-comments
description: Add or update PHPDoc-style doc comments for every PHP function, method, and class in the given file(s), following the project's established docblock style (short summary line, blank line, extended description if useful, @param per parameter with type and description, @return with type and description, @throws where relevant). Use this whenever the user asks to "add comments", "document this function/class", "add PHPDoc", "add docblocks", or references commenting PHP code, even if they don't name PHPDoc explicitly.
---

# PHP Doc Comments

Add complete, accurate PHPDoc docblocks above every function, method, and class
in the target PHP file(s) — without changing any actual code logic.

## Reference style

Match this format (taken from the project's existing code):

```php
/**
 * Output CSS custom properties for a plain (property/variable-name-keyed)
 * setting's saved value, cascading mobile → tablet → desktop per state
 * (e.g. normal/hover), the same way border() cascades breakpoints.
 *
 * Each breakpoint's saved value may be either:
 *  - a flat scalar (single variable), e.g. 'row-reverse'
 *  - an ordered array of values matched positionally to $variables
 *
 * @param array  $selectors Selectors keyed by tab/state, e.g.
 *                          ['normal' => '.btn', 'hover' => '.btn:hover'].
 * @param string $setting   Customizer/theme-mod setting key.
 * @param array  $variables Ordered list of CSS variable/property names.
 * @return void
 */
public static function css_variable(array $selectors, string $setting, array $variables = []): void
{
    ...
}
```

Key traits to reproduce:

- Opens with `/**` on its own line, each subsequent line starts with a
  vertically aligned `*`, closes with `*/` on its own line.
- First line (or first paragraph) is a concise, one-sentence summary written
  in third person / imperative ("Output ...", "Build ...", "Return ..."),
  not "This function...".
- A blank `*` line separates the summary from any extended description.
- Extended description is optional — include it when the behavior has
  non-obvious nuance (edge cases, cascading logic, accepted shapes of a
  parameter, etc.), as in the example's bullet list about scalar vs array
  values.
- `@param` tags: one per parameter, in declaration order. Align the type,
  variable name, and description in columns when there are multiple
  params (as in the example: `array  $selectors`, `string $setting`,
  `array  $variables`). Wrap long descriptions onto a second line indented
  to match the start of the description column.
- `@return` tag always present, even for `void`.
- Add `@throws ExceptionType Explanation.` when the method can throw.
- For classes/interfaces/traits, add a short docblock above the
  declaration describing its responsibility — no `@param`/`@return`,
  but `@property` or `@method` tags are fine if the class uses magic
  properties/methods worth documenting.
- Use real, accurate types matching the PHP signature (scalar type hints,
  `array`, a specific class name, or `mixed`/`?Type` for nullable).
  Don't invent details not evidenced by the code.

## Workflow

1. Read the target file(s) in full before writing anything — comments must
   describe what the code actually does, not a guess from the function name.
2. For each class, interface, or trait declaration: add/update its docblock.
3. For each function or method: add/update its docblock using the style
   above. Skip anonymous closures unless the user asks for those too.
4. If a docblock already exists, update it to stay accurate and consistent
   with this style rather than duplicating it or leaving stale info.
5. Never alter code logic, formatting of code lines, or reorder
   methods — only add or edit the comment blocks themselves.
6. If a parameter's purpose or a return value's meaning is ambiguous from
   the code alone, make a reasonable best-effort description rather than
   leaving it blank, but don't fabricate specific behavioral claims (e.g.
   specific default values, side effects) that aren't visible in the code.
7. After editing, show the user a short summary of which
   classes/functions/methods were documented or updated.
