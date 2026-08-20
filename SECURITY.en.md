[简体中文](SECURITY.md) | **English**

# Security Policy

We take the security of `hongxunpan/php-tools` and its users seriously. Please disclose potential vulnerabilities responsibly and avoid publishing exploitable details before a fix is available.

## Supported Versions

| Package version | Security maintenance | PHP requirement |
| --- | --- | --- |
| `3.x` | Actively maintained | PHP `>=5.6` |
| `2.x` | Legacy line; handled according to impact | See the constraint of the relevant release |
| `<2.0` | No longer maintained | Not applicable |

PHP `>=5.6` is the formal compatibility boundary of the current major version. The lifecycle and security maintenance of the PHP runtime itself are outside this project's control. Production users should still choose an upstream-supported PHP runtime whenever their application constraints allow it.

## Reporting a Vulnerability

Please use one of the following private channels:

1. GitHub Private Vulnerability Reporting, if it is enabled for the repository;
2. Email `me@kangxuanpeng.com` with a subject such as `[php-tools security] Short summary`.

Do not disclose an unpatched vulnerability through a public Issue, Pull Request, discussion, or social media post.

## What to Include

To help us reproduce and assess the issue, please include as much of the following information as possible:

- affected package, PHP, and runtime versions;
- vulnerability class, impact, and required preconditions;
- minimal reproduction steps or a proof of concept;
- potential attack paths and practical consequences;
- known mitigations or a suggested fix;
- your preferred attribution and disclosure terms.

Remove real secrets, personal data, and production credentials before submitting a report.

## Response Process

The maintainer will process a valid report as follows:

1. reproduce the issue and identify affected versions;
2. assess severity, exploitability, and compatibility impact;
3. prepare a fix and regression coverage in private;
4. coordinate the release date, advisory scope, and upgrade guidance;
5. disclose the necessary information after a fix is available and disclosure no longer creates avoidable risk.

Resolution time depends on complexity, compatibility, and release risk. Material progress will be communicated through the original reporting channel.

## Security Research Scope

Reports are welcome when they concern security risks directly caused by this package, including path handling, encryption semantics, locking and concurrency, input handling, or dependency-chain risks. The following cases are generally outside the project's vulnerability scope:

- configurations or usages that explicitly contradict the security documentation;
- issues limited to unsupported versions and not reproducible on a supported release;
- authorization, deployment, or infrastructure problems in a consuming application;
- automated scanner output without a verifiable impact.

If you are unsure whether a finding belongs to this project, contact the maintainer privately first.

## Acknowledgements

With the reporter's consent, the project may acknowledge responsible disclosure in a security advisory or changelog. Reporter identity and report details will not be published without permission.
