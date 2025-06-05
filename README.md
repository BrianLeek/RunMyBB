# CreateMyBB

This script is not production ready and should not be used on a public server. This script will need you to setup your v-host files to support subdomains like forumname.yourwebsite.com

RunMyBB is a self-hosted multi-forum management platform built with PHP and MySQL. It allows users to quickly create their own MyBB forums with no coding experience required.

This project was designed with simplicity, automation, and scalability in mind — perfect for hobbyist forum hosting or launching a custom MyBB-as-a-service platform.

---

## Features
- **No Coding Required** — Users just enter a few fields and click to launch their forum.
- **Fully Automated Setup** — File duplication, database creation, configuration, and MyBB settings are all handled behind the scenes.
- **Custom User System** — Registers and authenticates users outside of the MyBB system while linking them to their forums.
- **Forum Dashboard** — Logged-in users can manage their forums (suspend, delete, edit name, description, or URL).
- **Subdomain Support** — Supports forums on subdomains like `forumname.example.com` or `forumname.runmybb.com`.
- **Security-Minded** — Sanitization, validation, CSRF protection, and secure password hashing included.
- **Stylable** — Clean Bootstrap-based frontend ready for customization.
- **Forum Updater** — Easily apply MyBB core updates to all hosted forums without affecting user-specific configurations.

---

## How It Works
1. **User Signs Up & Creates Forum**
   - Enters username, email, password, forum name, description, and URL.
   - Forum subfolder is created and MyBB source files are copied.
   - A database is generated and filled with a clean MyBB SQL schema.
   - MyBB settings (`bburl`, `cookiedomain`, `adminemail`, etc.) are automatically set.
   - A matching MyBB admin account is created.
2. **Forum Goes Live**
   - The new forum becomes immediately accessible via subdomain or URL path.
   - The user can log in using their credentials and manage their forum via MyBB's Admin CP.
3. **User Manages Their Forums**
   - Dashboard allows users to delete, suspend, edit, or rename their forums.
4. **Admin Updates All Forums**
   - Easily apply MyBB updates via `update_forums.php`
   - Skips overwriting `inc/config.php` and `inc/settings.php`
   - Adds enhancements like:
     - Suspension check in `global.php`
     - Hidden settings in `admin/modules/config/settings.php`

---

## Installation & Setup Guide

### Prerequisites
- PHP 7.4 or newer
- MySQL
- Apache (with mod_rewrite enabled)
- MyBB clean source (`mybb_source/` folder with base install)

### Setup Steps
1. **Clone or download this repo:**
   ```bash
   git clone https://github.com/yourusername/RunMyBB.git
   ```
2. **Import your createforum database:**
   This contains your `users` and `forums` tables to manage hosted forums.
3. **Update config.php:**
   Set your database credentials.
   ```php
   $host = "localhost";
   $db_user = "";
   $db_pass = "";
   $db_name = "";
   $website_domain = "http://example.com/";
   ```

4. **Start your server and open in browser:**
   ```http://example.com/```

---

## Folder Structure

```
mybb_source/          → Clean MyBB install folder (used as a template)
mybb_latest/          → Folder with latest MyBB version (used by updater)
forum/                → All user-created forums are placed here
assets/               → CSS and static assets
update_forums.php     → Script to apply MyBB updates to all forums
create_forum.php      → Main forum creation logic
edit_forum.php        → Forum name, description, URL editor
delete_forum.php      → Deletes a forum
suspend_forum.php     → Suspends a forum
config.php            → Core config + DB connection
dashboard.php         → User control panel
profile.php           → Public user profile + privacy
index.php             → Homepage + forum creation form
login.php             → Login form
```

---

## How to Update All Forums

1. **Download the latest MyBB version** and place the extracted files in `mybb_latest/`.
2. **Access this URL in your browser:**
   ```http://example.com/update_forums.php```
3. This will:
   - Copy new MyBB files to every user forum
   - Keep user data and settings safe
   - Auto-patch:
     - `/global.php` to check suspension status
     - `/admin/modules/config/settings.php` to hide settings

---

## Contributions

Have a cool idea, UI improvement, or bug fix?  
Open a pull request or start a discussion. Contributions are welcome!

