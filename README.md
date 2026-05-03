# 🏠 Helperly - Marketplace for Reliable Home Services

Helperly is a modern, professional marketplace platform that connects families with verified service providers. Whether you need a babysitter, a cleaner, or a gardener, Helperly provides a secure and easy-to-use interface to find, hire, and pay reliable help.

---

## 🌟 Key Features

- **Triple-Sided Marketplace**: Tailored dashboards for Clients (Parents), Providers (Servants), and Administrators.
- **Verification System**: Rigorous vetting process with identity and selfie verification.
- **Real-time Messaging**: Direct communication between clients and providers.
- **Secure Payments**: Integrated payment tracking and completion confirmation.
- **Professional UI**: Fully responsive, accessible, and clean design system.
- **12-Factor App**: Centralized configuration via environment variables.

---

## 🛠️ Tech Stack

- **Backend**: Vanilla PHP 8.2 (optimized for performance)
- **Database**: MongoDB Atlas (highly scalable NoSQL)
- **Frontend**: Modern CSS3 (Grid/Flexbox), Native JavaScript
- **Infrastructure**: Docker & Docker Compose
- **Web Server**: Nginx (hardened for security)
- **Services**: ImageKit (media storage), Redis (cache), SMTP (transactional email)

---

## 🚀 Getting Started (Docker - Recommended)

The easiest and most consistent way to run Helperly is using Docker.

### 1. Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running.
- A [MongoDB Atlas](https://www.mongodb.com/cloud/atlas) account and connection string.

### 2. Setup Environment
```bash
cp .env.example .env
# Open .env and add your MONGODB_URI and JWT_SECRET
```

### 3. Build and Launch
```bash
docker-compose up -d --build
```

### 4. Install Dependencies
```bash
docker-compose exec app composer install
```

The application is now live at: **`http://localhost:8000`**

---

## 🏗️ Folder Structure

```text
├── assets/             # Static files (CSS, JS, Images)
├── config/             # Bootstrap, 12-factor config, DB wiring
├── controllers/        # Request handling & business logic
├── models/             # Data access layer (MongoDB collections)
├── public/             # Web root & front controller (index.php)
├── scratch/            # Utility scripts, seeders, and debug tools
├── views/              # Presentation layer (PHP Templates)
├── Dockerfile          # Multi-stage production build
├── docker-compose.yml  # Full-stack orchestration
└── nginx.conf          # Hardened Nginx configuration
```

---

## 🔐 Environment Variables

| Variable | Description | Default | Required |
| :--- | :--- | :--- | :---: |
| `APP_ENV` | `development` or `production` | `development` | No |
| `APP_DEBUG` | Show verbose errors | `false` | No |
| `APP_URL` | Base URL of the app | `http://localhost:8000` | Yes |
| `MONGODB_URI` | MongoDB Atlas URI | - | **Yes** |
| `JWT_SECRET` | Secret for API tokens | - | **Yes** |
| `REDIS_HOST` | Redis service hostname | `redis` | No |
| `SMTP_HOST` | Email server host | - | No |

---

## 🤝 Contributing

We welcome contributions! To maintain code quality, please follow these guidelines:

### 🌈 Design Standards
- Use the centralized CSS variable system in `assets/css/styles.css`.
- Prefer Utility Classes (`p-4`, `flex`, `grid-cols-2`) over inline styles.
- Ensure all new components are mobile-first and responsive.

### 📜 Workflow
1. **Branching**: Use `feature/` or `fix/` prefixes (e.g., `feature/add-redis-cache`).
2. **Coding Style**: PSR-12 for PHP, modern ES6+ for JavaScript.
3. **Commits**: Clear, imperative messages (e.g., "Add identity verification model").
4. **Pull Requests**: Explain *what* changed and *why*. Include screenshots for UI changes.

---

## 🐳 Docker Management

- **Stop Containers**: `docker-compose down`
- **View Logs**: `docker-compose logs -f app`
- **Run Seeder**: `docker-compose exec app php scratch/master_seeder.php`
- **Shell Access**: `docker-compose exec app bash`

---

## 🛡️ Security & Best Practices

- **Zero Hardcoded Secrets**: Everything is loaded via `env()` with validation.
- **Fail-Fast Configuration**: App won't start if critical keys are missing.
- **Nginx Hardening**: Blocks access to `.env`, `.git`, and prevents PHP execution in uploads.
- **XSS/CSRF Protection**: Native `escape()` helper and token validation on all POST requests.

---

## 🚧 Future Roadmap

- [ ] Multi-language support (i18n).
- [ ] Push notifications using WebSockets/Socket.io.
- [ ] Stripe/Chapa payment gateway integration.
- [ ] Automated unit and integration testing suite.

---

**Helperly** - Built with ❤️ for families and providers everywhere.
