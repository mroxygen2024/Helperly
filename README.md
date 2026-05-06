# 🏠 Helperly: Premium Home Service Marketplace

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/php-%5E8.1-777BB4.svg)
![MongoDB](https://img.shields.io/badge/mongodb-%234ea94b.svg?logo=mongodb&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Helperly is a high-performance, production-ready marketplace platform connecting families with verified home service providers. Built with a focus on trust, premium aesthetics, and technical efficiency, it streamlines the process of finding, vetting, and hiring reliable help for domestic needs.

---

## 🌟 Platform Features

### 👤 Parent (Employer) Dashboard
*   **Job Management**: Post detailed service requirements with real-time cost estimation.
*   **Provider Discovery**: Search and filter through a verified network of service professionals.
*   **Active Tracking**: Monitor job progress and manage multiple active service contracts.
*   **Premium UI**: Glassmorphic dashboard with intuitive navigation and status tracking.

### 🛡️ Provider (Servant) Dashboard
*   **Professional Profiles**: Build a comprehensive resume with skills, experience, and certifications.
*   **Verification System**: Integrated multi-step vetting (Identity ID + Live Selfie check).
*   **Application Tracking**: Apply to open jobs and manage ongoing assignments.
*   **Earnings Overview**: Real-time tracking of completed work and projected income.

### 👑 Administrator Control Center
*   **User Oversight**: Comprehensive management of all registered users (Block/Unblock/Delete).
*   **Verification Queue**: Professional document review interface for approving new providers.
*   **Marketplace Analytics**: High-level overview of system health, active jobs, and growth.
*   **Audit Logs**: Monitor platform activity and maintain marketplace integrity.

### 📱 General Features
*   **Responsive Design**: Pixel-perfect experience across mobile, tablet, and desktop.
*   **Secure Auth**: Role-based access control (RBAC) powered by JWT and secure sessions.
*   **Micro-interactions**: Smooth transitions and interactive states for a premium feel.

---

## 🛠️ Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Backend** | Vanilla PHP 8.2 (High Performance / Low Overhead) |
| **Database** | MongoDB (Flexible NoSQL Schema) |
| **Frontend** | HTML5, Modern CSS3 (Custom Design System), Native JavaScript |
| **Authentication** | JWT (JSON Web Tokens) & CSRF-Protected Sessions |
| **Media Handling** | ImageKit.io (Real-time image optimization & storage) |
| **Caching** | Redis (Optional session and data caching) |
| **Containerization** | Docker & Docker Compose |
| **Web Server** | Apache (Apache/2.4.54) |

---



---

## 🏗️ Project Structure

```text
├── assets/             # Global Design System (CSS, JS, Fonts)
│   ├── css/            # Custom utility-first CSS system
│   └── js/             # Native JS modules (Chat, Validation, Modals)
├── config/             # Bootstrap, Helpers, and 12-Factor Wiring
├── controllers/        # Application Controllers (Business Logic)
├── models/             # Data Access Layer (MongoDB Integration)
├── public/             # Web root & Entry point (index.php)
├── scratch/            # Utility scripts, Seeders, and Debug tools
├── views/              # Presentation layer (PHP Template engine)
│   ├── admin/          # Administrator views
│   ├── marketplace/    # User & Provider dashboards
│   └── layouts/        # Global header/footer fragments
├── Dockerfile          # Production PHP-FPM build
├── Dockerfile.render   # Optimized Apache build for Cloud hosting
└── docker-compose.yml  # Local full-stack orchestration
```

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
*   **Git**: For version control.
*   **Docker Desktop**: Recommended for the fastest setup.
*   **PHP 8.1+ & Composer**: Required only for non-Docker local development.
*   **MongoDB**: (Atlas account or local instance).

---

## 🚀 Local Development Setup

### Option A: Using Docker (Recommended)

1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/yourusername/helperly.git
    cd helperly
    ```

2.  **Configure Environment**:
    ```bash
    cp .env.example .env
    ```
    *Update `MONGODB_URI` in `.env` to `mongodb://mongodb:27017` to connect to the Docker container.*

3.  **Start the Stack**:
    ```bash
    docker compose up --build -d
    ```

4.  **Initialize Database Indexes (Crucial)**:
    Because we removed automatic indexing for maximum performance, you must manually build the indexes inside the Docker container:
    ```bash
    docker compose exec app php bin/setup.php
    ```
    
    *The app will be available at `http://localhost:8000`. Nginx serves the frontend and proxies dynamic requests to the PHP-FPM container automatically.*

---

### Option B: Without Docker

1.  **Install Dependencies**:
    ```bash
    composer install
    ```

2.  **Configure .env**:
    ```bash
    cp .env.example .env
    ```
    *Fill in your MongoDB Atlas connection string and ImageKit keys.*

3.  **Run with PHP Built-in Server**:
    ```bash
    php -S localhost:8000 -t public public/index.php
    ```

---

## 🔐 Environment Variables

| Variable | Description | Example Value |
| :--- | :--- | :--- |
| `APP_NAME` | Name of the platform | `Helperly Marketplace` |
| `APP_URL` | Base URL for the application | `http://localhost:8000` |
| `MONGODB_URI` | MongoDB Connection String | `mongodb+srv://user:pass@cluster...` |
| `MONGODB_DB` | Primary database name | `helperly_db` |
| `JWT_SECRET` | 32+ char secret for tokens | `df83...a821` |
| `IMAGEKIT_PUBLIC_KEY`| Media storage public key | `public_...` |

---

## ☁️ Deployment Guide

### Deploying to Render (Docker)
1.  Create a **New Web Service** on Render.
2.  Connect your repository and set **Runtime** to `Docker`.
3.  Set **Dockerfile Path** to `Dockerfile.render`.
4.  Add your environment variables in the Render dashboard.

### Deploying to VPS (Traditional)
1.  Clone repo to `/var/www/html`.
2.  Configure Apache/Nginx to point to the `public/` directory.
3.  Ensure `ext-mongodb` is enabled in your `php.ini`.

---

## 🤝 Contributing Guide

We follow a strict **Clean Code** and **Mobile-First** workflow.

1.  **Branching**: `feature/your-feature` or `fix/your-fix`.
2.  **Styling**: Use the utility classes in `assets/css/styles.css`. Do not add inline styles.
3.  **Commits**: Use conventional commits (e.g., `feat: add real-time messaging`).
4.  **Pull Requests**: Always include a description of UI changes and test results.

---

## 🛡️ Security Notes
*   **Secrets**: Never commit `.env` or any hardcoded credentials.
*   **Validation**: All inputs are sanitized using the `escape()` and `sanitizeInput()` helpers.
*   **CSRF**: All POST forms must include the `<?= csrfToken() ?>` hidden input.
*   **Production**: Always set `APP_DEBUG=false` in live environments.

---

## 🛠️ Troubleshooting

**Issue**: `Fatal error: Class 'MongoDB\Client' not found`
*   **Solution**: Ensure `composer install` was run and `vendor/autoload.php` is required. If using non-Docker, ensure the `mongodb` extension is enabled in `php.ini`.

**Issue**: `Styles not loading / 404 on assets`
*   **Solution**: Ensure your web server is configured to serve the `public/` folder as the document root.

---

## 📜 License
This project is licensed under the **MIT License**. See [LICENSE](LICENSE) for details.

---

## 📧 Contact & Credits
*   **Lead Developer**: [Fuad Sano](https://github.com/fuadsano)
*   **Design Inspiration**: Modern SaaS Dashboards (Vercel, Stripe)
*   **Support**: For issues, please open a GitHub Issue.

---
Built with ❤️ for a safer, more reliable marketplace.
