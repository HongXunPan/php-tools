[简体中文](SUPPORT.md) | **English**

# Support

This document explains how to get help with `hongxunpan/php-tools` and which channel to use for each type of request.

## Usage Questions

Before asking for help:

1. read the [README](README.en.md) and the relevant document under `readme/`;
2. confirm that you are using a supported package version;
3. search existing Issues to avoid duplicates;
4. reduce the problem to a minimal example without sensitive business data.

If the problem remains unresolved, use the usage-question Issue template. Include the package version, PHP version, relevant dependency versions, minimal code, and the complete error message.

## Bug Reports

Use the bug-report Issue template for reproducible problems caused by this package. A useful report includes:

- actual and expected behavior;
- minimal reproduction steps;
- package, PHP, and dependency versions;
- troubleshooting and verification already performed;
- sanitized logs, stack traces, or screenshots.

The maintainer cannot guarantee a fix for reports without reproduction details, reports limited to business symptoms, or issues affecting only unsupported versions.

## Feature Requests

Use the feature-request Issue template and explain:

- the general-purpose problem to be solved;
- why it belongs in a shared library rather than an application project;
- the proposed public API and alternatives considered;
- PHP 5.6 compatibility, dependency weight, and maintenance cost;
- whether the change may be backward incompatible.

The project prioritizes capabilities with clear responsibilities, cross-project reuse, testability, and sustainable maintenance cost. Not every feature request will be accepted.

## Security Issues

Potential vulnerabilities must be reported privately according to the [security policy](SECURITY.en.md). Do not create a public Issue or include exploitable details in a public Pull Request.

## Support Boundaries

- The current `3.x` line is actively maintained and formally supports PHP `>=5.6`;
- `2.x` is a legacy line, and reports are evaluated according to security impact and migration cost;
- application integration, deployment architecture, private Redis services, and third-party framework integration are outside the default support scope;
- the maintainer will make a reasonable effort to respond but does not provide a fixed response time or commercial service-level agreement.

Read the [contribution guide](CONTRIBUTING.en.md) before submitting code changes.
