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

## 🚀 Quick Start (Docker Compose)

The easiest way to run the entire stack (App, MongoDB, Redis) locally.

### 1. Setup Environment
```bash
cp .env.example .env
```
Open `.env` and set `MONGODB_URI` to `mongodb://mongodb:27017` (for local Docker use) or your Atlas URI.

### 2. Launch Stack
```bash
docker-compose up -d --build
```
This starts:
- **PHP App**: `http://localhost:8000`
- **MongoDB**: Port `27017`
- **Mongo Express (GUI)**: `http://localhost:8081` (Admin UI for your DB)
- **Redis**: Port `6379`

### 3. Initialize Data
```bash
# Install PHP dependencies
docker-compose exec app composer install

# Seed dummy data (Parent, Provider, Admin)
docker-compose exec app php scratch/seed_dummy_data.php
```

---

## ☁️ Cloud Hosting (Render)

Deploy Helperly to Render in minutes using our optimized Docker setup.

### 1. Prerequisites
- A GitHub repository with your code.
- A [MongoDB Atlas](https://www.mongodb.com/cloud/atlas) cluster (Free tier works great).

### 2. Deploy to Render
1. Create a new **Web Service** on Render.
2. Connect your GitHub repository.
3. In the **Environment** settings:
   - **Runtime**: `Docker`
   - **Docker Command**: Leave default (it will use `Dockerfile`) or specify `Dockerfile.render` if you want the simplified Apache version.
4. Add the following **Environment Variables** in the Render dashboard:
   - `MONGODB_URI`: Your MongoDB Atlas connection string.
   - `MONGODB_DB`: `helperly_prod`
   - `JWT_SECRET`: A long random string.
   - `APP_URL`: Your Render service URL (e.g., `https://helperly.onrender.com`).
   - `IMAGEKIT_PUBLIC_KEY` / `IMAGEKIT_PRIVATE_KEY` / `IMAGEKIT_URL_ENDPOINT`: From your ImageKit dashboard.
5. Click **Deploy Web Service**.

---

## 🏗️ Folder Structure

```text
├── assets/             # Global CSS, JS, and Design System
├── config/             # App bootstrap and DB wiring
├── controllers/        # Business logic & Route handlers
├── models/             # MongoDB Collection Models
├── public/             # Web root (index.php)
├── scratch/            # Database seeders & utility scripts
├── views/              # PHP Templates (layouts, admin, marketplace)
├── Dockerfile          # Multi-stage production build (PHP-FPM)
├── Dockerfile.render   # Simplified Apache build (Recommended for Render)
├── docker-compose.yml  # Local development orchestration
└── nginx.conf          # Nginx config for Docker local setup
```

---

## 🔐 Key Environment Variables

| Variable | Description | Recommended for Dev |
| :--- | :--- | :--- |
| `MONGODB_URI` | Database Connection String | `mongodb://mongodb:27017` |
| `MONGODB_DB` | Database Name | `servant_marketplace` |
| `JWT_SECRET` | Secret for auth tokens | `any-random-string` |
| `APP_URL` | Base URL of the site | `http://localhost:8000` |
| `IMAGEKIT_...` | Media Hosting Credentials | [Get at ImageKit.io](https://imagekit.io) |

---

## 🐳 Useful Docker Commands

- **Logs**: `docker-compose logs -f app`
- **Stop**: `docker-compose down`
- **Reset DB**: `docker-compose exec app php scratch/seed_dummy_data.php`
- **Shell**: `docker-compose exec app bash`

---

## 🤝 Contributing
... (rest of the content)

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
