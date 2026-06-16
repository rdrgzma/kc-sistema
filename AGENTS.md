# AGENTS.md

Repository guidance for agentic coding in this project.

## Project Snapshot
- Laravel 13 + PHP 8.3.
- Livewire 4, Volt, Filament, Fortify, Reverb, Spatie Permission, Pest 4.
- Frontend assets are built with Vite and Tailwind v4.
- This repo uses Pest for tests and Pint for formatting.

## Rule Sources Checked
- No `.cursor/rules/` files were found.
- No `.cursorrules` file was found.
- No `.github/copilot-instructions.md` file was found.
- `.agent/rules/` files
- If any of those are added later, merge them into this guide.

## Core Commands
- Install dependencies: `composer install`
- Full setup: `composer setup`
- Start local dev stack: `composer dev`
- Build frontend assets: `npm run build`
- Start Vite only: `npm run dev`

## PHP Quality Commands
- Format code: `composer lint`
- Check formatting only: `composer lint:check`
- Run app tests with lint check: `composer test`
- CI-style check: `composer ci:check`

## Running Tests
- Run all tests: `php artisan test --compact`
- Run one file: `php artisan test --compact tests/Feature/DashboardTest.php`
- Run one test by name: `php artisan test --compact --filter="login screen can be rendered"`
- Run a specific suite: `php artisan test --compact --testsuite=Feature`
- Prefer the smallest test set that covers the change.

## Single-Test Notes
- Use `--filter` when you know the test name.
- Use a file path when the target file is known.
- Keep `--compact` on for readable output.
- If a test depends on database state, make sure the test uses the existing Pest setup in `tests/Pest.php`.

## Formatting Rules
- Follow `.editorconfig`: UTF-8, LF, 4-space indents, final newline, no trailing whitespace.
- Use 2-space indentation only in YAML files.
- Keep Markdown trailing whitespace off behavior unchanged if editing docs.
- Let Pint handle formatting; do not hand-format to fight Pint.

## Import Style
- Group imports by namespace and keep them tidy.
- Prefer explicit class imports over fully qualified names in code.
- Remove unused imports immediately.
- Keep `use` statements at the top, above class declarations.
- Match the surrounding file if a file already uses a local ordering convention.

## PHP Style
- Prefer typed parameters and return types.
- Use `void`, `array`, and concrete return types when possible.
- Use PHP 8 attributes where the project already does so, such as model attributes.
- Keep methods small and direct; extract business logic into services when it grows.
- Prefer early returns over deep nesting.
- Use `now()`/Carbon helpers for dates instead of manual time formatting.

## Naming Conventions
- Classes: `PascalCase`.
- Methods: `camelCase`.
- Properties: `camelCase`.
- Tests: descriptive Pest names in plain English.
- Livewire components commonly end in `Manager`, `Table`, `Board`, `Wizard`, or `Detail`.
- Models are singular `PascalCase` nouns.
- Routes use readable names like `dashboard`, `*.index`, and `*.show`.

## Laravel Conventions
- Keep controllers thin.
- Put multi-step business logic in services, actions, or observers.
- Use Eloquent relationships instead of raw joins when the codebase already does.
- Prefer transactions for multi-write operations.
- Use policies/roles for authorization rather than inline permission checks when practical.
- Use `auth()->id()` / `auth()->user()` only when the route is already protected.

## Validation And Input
- Validate before writing to the database.
- Prefer `$request->validated()` when Form Requests exist.
- Keep validation near the boundary layer.
- Be strict about file size, MIME type, and allowed values.

## Database And Migrations
- Use migration classes with `up()` and `down()` methods.
- Keep each migration focused on one schema concern.
- Use foreign keys and `constrained()` where appropriate.
- Do not rewrite old migrations unless the task explicitly requires it.
- Add indexes when query patterns need them.

## Models
- Define fillable/guarded behavior explicitly.
- Use `casts()` for attribute casting.
- Prefer relationship return types such as `HasMany`, `BelongsTo`, and `BelongsToMany`.
- Keep model methods about model behavior, not orchestration.

## Livewire And Filament
- Follow the project’s existing Livewire style instead of introducing a second pattern.
- Keep components focused and stateful where needed.
- Use Filament forms/tables/actions consistent with adjacent files.
- Avoid query work in Blade views.

## Testing Style
- Use Pest syntax, not PHPUnit classes, for new tests.
- Prefer small, isolated tests with clear names.
- Use factories rather than manual model construction when possible.
- Use `assertOk()`, `assertRedirect()`, and similar specific assertions.
- Keep auth and feature tests aligned with the existing `tests/Pest.php` setup.

## Error Handling
- Do not swallow exceptions silently.
- Use early failure, transaction rollback, or framework exception handling as appropriate.
- Return or throw meaningful results instead of ambiguous booleans when the codebase already uses richer types.
- Prefer explicit notification or response handling over hidden side effects.

## Comments And Docs
- Write comments only when the code is not self-explanatory.
- Keep comments short and factual.
- Prefer naming and extraction over long comments.

## Practical Agent Rules
- Make the smallest correct change.
- Preserve existing project patterns unless the repo clearly needs cleanup.
- Do not introduce new abstractions unless they reduce real duplication.
- If a change affects tests, run the smallest relevant test set first, then broaden if needed.
- Avoid destructive git operations unless the user explicitly asks.

## Observed Project Patterns
- Many files use English code structure with Portuguese labels and UI strings.
- Livewire routes are registered in `routes/web.php` with role-based middleware.
- Tests already use Pest and the default Laravel test case setup.
- Services such as `FinanceiroService` hold business logic that would be awkward in controllers.

## When In Doubt
- Check sibling files first.
- Follow the closest existing pattern.
- Prefer consistency over theoretical purity.
- If a rule conflicts with an established local pattern, the local pattern wins.
