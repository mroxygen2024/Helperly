# Helperly — Premium Home Service Marketplace

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/php-%5E8.1-777BB4.svg)
![MongoDB](https://img.shields.io/badge/mongodb-%234ea94b.svg?logo=mongodb&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Helperly is a high-performance marketplace platform connecting families with verified home service providers. Built entirely with vanilla PHP, it features role-based dashboards, real-time messaging, and a premium glassmorphic UI.

---

## Features

### Parent (Employer) Dashboard
- Post jobs with real-time cost estimation
- Search and filter verified service providers
- Track active jobs and manage contracts

### Provider Dashboard
- Build professional profiles with skills and certifications
- Identity verification (ID + Live Selfie)
- Apply to jobs and track earnings

### Admin Control Center
- Manage users (Block / Unblock / Delete)
- Review and approve provider verification documents
- Marketplace analytics and system health overview

---

## Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Backend** | PHP 8.2 (Vanilla — No Frameworks) |
| **Database** | MongoDB |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Auth** | JWT & CSRF-Protected Sessions |
| **Media** | ImageKit.io |
| **Web Server** | Nginx (Docker) / PHP Built-in Server (Local) |
| **Containerization** | Docker & Docker Compose |

---

## Project Structure

```text
├── assets/             # CSS Design System & JS Modules
├── bin/                # CLI utilities (setup.php)
├── config/             # App bootstrap, helpers, database config
├── controllers/        # Business logic controllers
├── core/               # Router and framework primitives
├── models/             # MongoDB data access layer
├── public/             # Web root & front controller (index.php)
├── views/              # PHP template views
│   ├── admin/
│   ├── marketplace/
│   ├── profile/
│   └── layouts/
├── Dockerfile          # PHP-FPM multi-stage build
├── docker-compose.yml  # Full-stack local orchestration
└── nginx.conf          # Nginx reverse proxy config
```

---

## Quick Start

### Option A: Docker (Recommended)

```bash
# 1. Clone and enter the project
git clone https://github.com/yourusername/helperly.git
cd helperly

# 2. Configure environment
cp .env.example .env
# Edit .env — set MONGODB_URI=mongodb://mongodb:27017

# 3. Build and start all services
docker compose up --build -d

# 4. Initialize database indexes (run once)
docker compose exec app php bin/setup.php
```

The app will be live at **http://localhost:8000**.

### Option B: Without Docker

```bash
# 1. Install PHP dependencies
composer install

# 2. Configure environment
cp .env.example .env
# Edit .env — set your MongoDB connection string

# 3. Initialize database indexes (run once)
php bin/setup.php

# 4. Start the development server
php -S localhost:8000 -t public public/index.php
```

---

## Environment Variables

| Variable | Description | Example |
| :--- | :--- | :--- |
| `APP_NAME` | Platform name | `Helperly` |
| `APP_URL` | Base URL | `http://localhost:8000` |
| `MONGODB_URI` | MongoDB connection string | `mongodb://mongodb:27017` |
| `MONGODB_DB` | Database name | `helperly_db` |
| `JWT_SECRET` | Secret for JWT signing (32+ chars) | `your-secret-key` |
| `IMAGEKIT_PUBLIC_KEY` | ImageKit public key | `public_...` |

---

## Security

- All inputs are sanitized via `escape()` and `sanitizeInput()` helpers.
- All POST forms require a CSRF token (`<?= csrfToken() ?>`).
- Never commit `.env` or hardcoded credentials.
- Set `APP_DEBUG=false` in production.

---

## Troubleshooting

**`Class 'MongoDB\Client' not found`**
→ Run `composer install` and ensure `ext-mongodb` is enabled in `php.ini`.

**Styles not loading / 404 on assets**
→ Ensure your web server document root points to the `public/` directory.

---

## License

MIT — See [LICENSE](LICENSE) for details.

---

## GitHub Contribution

- Contributions are welcome via GitHub issues and pull requests.
- Use descriptive branch names like `feature/your-feature` or `fix/your-fix`, and include a clear PR summary.

---

## Credits

- **Lead Developer**: [Fuad Sano](https://github.com/fuadsano)
- **Design Inspiration**: Vercel, Stripe, Linear
