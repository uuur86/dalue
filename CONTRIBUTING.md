# Contributing to Dalue

Thank you for helping improve Dalue.

## Reporting bugs and requesting features

Open an [issue](https://github.com/uuur86/dalue/issues) with:

- the PHP and `uuur86/strobj` versions you use,
- a minimal source data sample and schema,
- the result you expected and the result you got.

Report security problems privately as described in [SECURITY.md](SECURITY.md).

## Development setup

```sh
git clone https://github.com/uuur86/dalue.git
cd dalue
composer install
```

## Checks

Run every check before opening a pull request:

```sh
composer check
```

This runs:

| Command | Purpose |
| --- | --- |
| `composer cs` | PSR-12 coding standard (`composer cs:fix` fixes most issues) |
| `composer phpstan` | Static analysis at level 8, PHP 7.4 compatible |
| `composer test` | PHPUnit test suite |

The same checks run on GitHub Actions for PHP 7.4 – 8.5.

## Pull requests

- Create a branch from `main` and keep each pull request focused on one change.
- Add or update tests for every behavior change. A bug fix should include a test
  that fails without the fix.
- Update `README.md`, `docs/wiki/` and the `Unreleased` section of `CHANGELOG.md`
  when the public behavior changes.
- Write code, comments and commit messages in English.

## Documentation

The wiki is generated from `docs/wiki/`. Edit the Markdown files there; changes
merged into `main` are published automatically by the `Publish wiki` workflow.
Pages edited directly on the wiki are overwritten by the next publish.
