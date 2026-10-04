# Trauma Therapy Centre — PHP Website

A fully functional, database-driven website built with **raw PHP**, **MySQL** and **vanilla JavaScript**, converted from the original static HTML design. All content is editable from an admin dashboard, and both the public site and the admin area are fully responsive.

## Structure

```
├── index.php                 # Public homepage (all content pulled from MySQL)
├── config/
│   ├── config.php            # DB credentials (env-var aware) + session setup
│   └── database.php          # PDO connection (singleton)
├── includes/
│   ├── functions.php         # Helpers: settings, escaping, CSRF, rendering
│   ├── header.php            # Site <head>, top bar, nav
│   └── footer.php            # Footer + script include
├── assets/
│   ├── css/style.css         # Public site styles (responsive)
│   ├── css/admin.css         # Admin dashboard styles (responsive)
│   ├── js/main.js            # Public site JS (nav, reveal, accordion)
│   └── js/admin.js           # Admin JS (sidebar, flash messages)
├── admin/
│   ├── login.php / logout.php
│   ├── auth.php              # Session auth guard
│   ├── index.php             # Dashboard
│   ├── settings.php          # Edit all site text/settings (grouped)
│   ├── manage.php            # Generic CRUD for all content tables
│   ├── password.php          # Change admin password
│   └── includes/             # Admin layout + table definitions
└── database/
    └── schema.sql            # Schema + full seed data
```

## Setup

1. **Requirements:** PHP 8.1+, MySQL 5.7+/MariaDB, Apache or any PHP-capable web server.
2. **Create the database** (schema + seed content in one step):
   ```bash
   mysql -u root -p < database/schema.sql
   ```
3. **Configure credentials** in `config/config.php`, or set environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
4. **Serve the site** (document root = project root). For a quick local run:
   ```bash
   php -S localhost:8000
   ```

## Admin dashboard

- URL: `/admin/`
- Default login: `admin` / `admin123` — **change it immediately** via *Change Password*.

From the dashboard you can control:

| Area | What it edits |
|---|---|
| Site Settings | Every heading, paragraph, price, URL, footer & crisis text (grouped by section) |
| Therapy Types | Accordion items (title, subtitle, body) |
| Gold Standards | EMDR / TF-CBT rows |
| Fee / Appointment Cards | The four info cards incl. reminder timeline & warning flags |
| Cancellation Tiers | Notice/fee scale |
| Qualifications | Registrations and training lists |
| Availability | Session days & hours |
| Marquee Items | Scrolling registration banner |
| Hero Stats | The three stat counters |

Every item supports **sort order** and a **visible/hidden** toggle.

### Content formatting

- Allowed inline tags in text fields: `<em> <strong> <b> <i> <u> <br>` — everything else is escaped.
- In multi-line *body* fields: a blank line starts a new paragraph, lines starting with `- ` become list items.

## Security

- PDO **prepared statements** everywhere (no string-interpolated user input).
- Passwords stored with `password_hash()` / verified with `password_verify()`.
- **CSRF tokens** on every form (login, settings, CRUD, password).
- All output escaped with `htmlspecialchars()`; stored rich text limited to a small tag whitelist.
- Session cookie hardening (`httponly`, `samesite`), session ID regeneration on login, basic login throttling.
- `.htaccess` denies direct web access to `config/`, `includes/` and `database/`.
