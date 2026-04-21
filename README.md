# Servant Marketplace (Vanilla PHP + MongoDB Atlas)

This is a small, production-ready starter structure for a marketplace that connects servants and employers.

## Folder structure

- `config/` -> App bootstrap, shared helpers, and MongoDB connection setup.
- `controllers/` -> Request handling and application flow logic.
- `models/` -> Data access layer for MongoDB collections.
- `views/` -> Presentation templates (HTML + minimal PHP).
- `public/` -> Web entry point and routing.
- `assets/` -> Static files such as CSS and JavaScript.

## Quick start

1. Install PHP dependencies:
   composer install
2. Copy environment template and set your Atlas values:
   cp .env.example .env
3. Export env vars (or configure them in your web server):
   export $(grep -v '^#' .env | xargs)
4. Start local PHP server:
   php -S localhost:8000 -t public public/index.php

   Note: using `public/index.php` as the router is required so `/assets/*`
   requests are handled correctly in this project structure.

## Security notes

- Passwords are hashed with `password_hash` and checked with `password_verify`.
- CSRF tokens are generated and validated on POST forms.
- Session ID is regenerated on login.
- User input is validated and escaped before rendering.

## SMTP email setup (verification + reset emails)

Email verification and password reset now use SMTP (PHPMailer).

1. Copy `.env.example` to `.env` if needed.
2. Set SMTP values in `.env`:
   - `SMTP_HOST`
   - `SMTP_PORT`
   - `SMTP_USERNAME`
   - `SMTP_PASSWORD`
   - `SMTP_ENCRYPTION` (`tls` or `ssl`)
   - `SMTP_FROM_EMAIL`
   - `SMTP_FROM_NAME`
3. Restart PHP/FPM or restart your local PHP server.

Example (Mailtrap sandbox):

```
SMTP_HOST=sandbox.smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USERNAME=your_mailtrap_username
SMTP_PASSWORD=your_mailtrap_password
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=no-reply@your-domain.test
SMTP_FROM_NAME="Servant Marketplace"
```

If SMTP is not configured, the app logs a warning and email delivery is skipped.

## MongoDB Atlas notes

- Reuse a single MongoDB client instance in app runtime.
- Keep default pool settings unless workload requirements are known.
- Set connection options primarily through the Atlas connection URI.
