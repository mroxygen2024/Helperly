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

## MongoDB Atlas notes

- Reuse a single MongoDB client instance in app runtime.
- Keep default pool settings unless workload requirements are known.
- Set connection options primarily through the Atlas connection URI.
