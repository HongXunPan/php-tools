[简体中文](CONTRIBUTING.md) | **English**

# Contributing

Thank you for helping improve `hongxunpan/php-tools`. Stability, reuse, and long-term compatibility are project priorities. Every change must preserve formal support for PHP `>=5.6`.

## Before You Start

- Read the [support policy](SUPPORT.en.md) for usage questions and general assistance;
- Report security issues privately according to the [security policy](SECURITY.en.md); do not create a public Issue;
- Use the repository's Issue templates for bugs and feature requests;
- Open an Issue first for substantial public API, dependency, or compatibility changes, and explain the motivation, design, and migration impact.

## Development Environment

1. Fork the repository and create a short-lived branch from the latest `main`;
2. Install a Composer version compatible with your PHP runtime; PHP 5.6 must use Composer 2.2 LTS;
3. Install dependencies:

```bash
composer install
```

Composer's `config.platform.php=5.6.0` preserves the minimum dependency-resolution boundary. Do not raise it merely to accommodate a local development environment.

## Compatibility Requirements

- The package supports PHP `>=5.6`; do not introduce syntax available only in PHP 7 or PHP 8;
- Do not use scalar type declarations, return types, nullable types, the null coalescing operator, or other syntax unsupported by PHP 5.6;
- New dependencies must satisfy PHP 5.6, license compatibility, maintenance, and supply-chain requirements;
- Public API changes must explain backward-compatibility impact and migration steps;
- Code comments, maintenance documentation, and commit messages should explain why a change exists rather than merely restating its implementation.

## Code and Tests

- Follow the repository's existing naming, directory, and test organization;
- For bug fixes, first add a test that reproduces the problem, then implement the fix;
- New capabilities should cover the normal path, boundary conditions, and failure behavior;
- Do not commit `vendor/`, IDE workspaces, secrets, logs, or local temporary files;
- Do not mix unrelated refactoring or formatting into the same Pull Request.

Run at least the following commands before submitting a change:

```bash
composer validate --strict
composer lint
composer test
```

GitHub Actions verifies PHP 5.6, 7.4, 8.0, and 8.5. Passing on one newer runtime does not replace the full compatibility matrix.

## Documentation and Changelog

- User-visible behavior, configuration, and public API changes must update the README or the relevant document under `readme/`;
- When a public entry has both Chinese and English versions, semantic changes must update both; explain in the Pull Request when one side does not require a change;
- Record user-visible changes under the “Unreleased” section of the [changelog](CHANGELOG.md);
- Breaking changes must include migration guidance rather than relying on code or commit messages alone;
- Documentation examples must be executable and must not contain real credentials or sensitive business data.

## Commits and Pull Requests

Prefer single-purpose commits that can be reviewed independently. Suggested commit types include:

- `feat`: new capability;
- `fix`: bug fix;
- `docs`: documentation-only change;
- `test`: test change;
- `refactor`: internal change without external behavior changes;
- `chore`: maintenance work.

Complete the Pull Request template and describe at least:

1. the problem and objective;
2. the implementation and affected scope;
3. PHP 5.6 and public API compatibility;
4. verification performed;
5. documentation, changelog, and security impact.

Maintainers may request a smaller scope, additional tests, or design changes. A change is merged only after all required checks and reviews are complete.

## License

By contributing, you confirm that you have the right to submit the contribution and agree that it will be released under the repository's [MIT License](LICENSE).
