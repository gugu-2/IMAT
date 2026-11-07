# ⚡ 100% Automated Zero-Config Setup Guide for `public_html`

Steps 2 and 3 have been **completely automated**. 

You do **NOT** need to create a database in cPanel. You do **NOT** need to create database passwords. You do **NOT** need to edit `config.php`.

---

## 🚀 The Only Step: Upload to `public_html`

### Method 1: Using the ZIP File (10 Seconds — Recommended)
1. Open your hosting **cPanel** > open **File Manager**.
2. Click to open your server's **`public_html`** folder.
3. Click **Upload** > select **`public_html_files.zip`**.
4. Right-click `public_html_files.zip` inside `public_html` > click **Extract**.

*(Or Method 2: Drag and drop all files from the `public_html` folder into your server's `public_html`).*

---

## 🌐 Done! Open Your Website URL

Visit your website URL in any browser:
```text
https://yourdomain.com/
(or https://yourdomain.com/imat/ if placed in an imat subfolder)
```

### What Happens Automatically Behind the Scenes:
1. **Self-Initializing Database:** On the first page load, PHP automatically creates the server database file (`imat_database.sqlite`) and sets up all 6 tables.
2. **Instant Multi-Device Sync:** The database lives on your server. When you open the website on your phone, tablet, or laptop, it connects to this same server database.
3. **Real-Time Auto-Save:** Every time you check off a topic (from all 268 topics), slide an accuracy score, or add a timetable task, it saves online instantly.
4. **Security Protection:** The included `.htaccess` file prevents any unauthorized downloads of the database file.

---

*(Optional Power Feature: If you already have WordPress running on your domain, `config.php` automatically detects it and can use your WordPress database with zero configuration as well).*
