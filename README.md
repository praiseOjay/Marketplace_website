# 🛍️ Marketplace — Premium Classifieds Web Application

A modern, full-featured **Marketplace & Classifieds Web Application** built with **PHP 8.2+**, **Symfony 7**, **Doctrine ORM**, **Twig**, and **Bootstrap 5**. Designed with a sleek dark glassmorphism aesthetic, instant buyer-seller messaging, responsive grid layouts, category filtering, password visibility toggles, Google reCAPTCHA v3 bot protection, and robust security defenses.

---

## 📸 Screenshots & Showcase

### 1. Modern Dark Glassmorphic Homepage & Hero Section
<img src="./docs/screenshots/homepage_hero.png" alt="Homepage Showcase" width="100%" />

### 2. Verified Advert Listings & Seller Dashboard
<img src="./docs/screenshots/listings_grid.png" alt="Listings Showcase" width="100%" />

### 3. Direct Buyer-Seller Messaging Thread
<img src="./docs/screenshots/messaging_chat.png" alt="Messaging Thread" width="100%" />

### 4. Authentication Security: Password Visibility Toggle & Google reCAPTCHA v3
<img src="./docs/screenshots/auth_security_recaptcha.png" alt="Auth Security & Password Visibility" width="100%" />

---

## ✨ Core Features

- 🎨 **Modern Dark Glassmorphic UI**: Vibrant gradient accents, glassmorphic cards, and micro-interactions.
- 👁️ **Interactive Password Visibility Toggle**: One-click show/hide password buttons on login, registration, and profile password updates.
- 🤖 **Google reCAPTCHA v3 Enterprise Bot Protection**: Frictionless spam mitigation and automated bot defense across authentication and user forms.
- 🚀 **Dynamic Category Quick Filter**: Instant category filtering with active state highlighting.
- 🔍 **Integrated Search Capsule**: Search by keyword, category, or location seamlessly across all viewports.
- 📱 **100% Fully Responsive Layout**: Tailored breakpoints for desktop, tablet, and mobile screens.
- 💬 **Direct Messaging System**: Instant buyer-seller chat threads linked to specific listings.
- ❤️ **Saved Adverts & Bookmarks**: User favorites management.
- 📊 **Seller Dashboard**: Real-time listing metrics, portfolio valuation, and quick action management (`View`, `Edit`, `Delete`).
- 🛡️ **Role-Based Access Control (RBAC)**: Fine-grained permissions for Users, Moderators, and Admins via EasyAdmin 4.
- 🖼️ **Automated WebP Image Optimization**: Image upload processing, thumbnail generation, and lazy loading.
- 📦 **Dummy Fixtures Included**: Pre-configured with realistic users, categories, and high-resolution item photography.

---

## 🔒 Security Architecture

- **Google reCAPTCHA v3 Automated Defense**: Invisible client-side token generation paired with backend verification (`App\Service\RecaptchaService`) and `LoginRecaptchaListener` to prevent brute-force attacks, credential stuffing, and bot submissions.
- **SQL Injection Defense**: Strict prepared statement parameter binding (`addcslashes($title, '%_')` and Doctrine Query Builder parameterization).
- **XSS & Injection Protection**: HTML auto-escaping across all Twig templates and input sanitization.
- **HTTP Security Headers**: Automated listener injecting `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection`, `Referrer-Policy`, and `Permissions-Policy`.
- **Malicious Upload Defense**: MIME header verification (`getimagesize()`) restricting uploads strictly to JPEG, PNG, and WebP, re-encoding files via GD.
- **CSRF & Voter Authorization**: Token validation and Symfony Voters for ownership checks.

---

## ⚡ Performance Optimizations

- **N+1 Query Elimination**: Eager `leftJoin` and `addSelect` queries across repositories to fetch listings, users, and categories in single database calls.
- **Automatic WebP Conversion**: Proportional image resizing to 1200px max width and WebP encoding for fast page loading.
- **Browser Resource Efficiency**: Native `loading="lazy"` and `decoding="async"` applied across all media templates.
- **Even Pagination Grid**: 6 items per page ensuring clean 2-row alignment across 3-column layouts.

---

## 🛠️ Stack & Dependencies

- **PHP**: 8.2+
- **Framework**: Symfony 7
- **Database / ORM**: SQLite / Doctrine ORM
- **Security & Anti-Bot**: Google reCAPTCHA v3
- **Templating**: Twig Engine
- **Admin Panel**: EasyAdmin 4
- **Pagination**: KnpPaginatorBundle
- **Frontend**: Vanilla CSS + Bootstrap 5 + FontAwesome 6

---

## 🚀 Quickstart & Local Setup

### 1. Clone Repository
```bash
git clone https://github.com/praiseOjay/Marketplace_website.git
cd Marketplace_website
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Configure Google reCAPTCHA (.env)
```env
RECAPTCHA_SITE_KEY=6LeSZootAAAAAMryTY_Y9m5W4yA0MGsQ6wLA23kn
RECAPTCHA_SECRET_KEY=6LeSZootAAAAAC1hPX6VQFOPiM5ux9wMRrrGh60V
```

### 4. Setup Database & Load Fixtures
```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Run Development Server
Using built-in PHP web server:
```bash
php -S 127.0.0.1:8000 -t public public/index.php
```
Open **[http://127.0.0.1:8000](http://127.0.0.1:8000)** in your browser.

---

## 👤 Test Demo Credentials

| Role | Username | Email | Password |
|---|---|---|---|
| **Admin** | `admin` | `admin@marketplace.com` | `admin123` |
| **User 1** | `johndoe` | `john@example.com` | `password123` |
| **User 2** | `sarah_smith` | `sarah@example.com` | `password123` |

---

## 📝 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## 📧 Contact

**Praise Ojerinola** — Ojerinolapraise@gmail.com  
GitHub: [praiseOjay](https://github.com/praiseOjay)  
Project Repository: [Marketplace_website](https://github.com/praiseOjay/Marketplace_website.git)
