# 🇮🇹 IMAT & 🇩🇪 TestAS Examination Mastery & Cloud Cockpit

[![License: MIT](https://img.shields.io/badge/License-MIT-amber.svg)](LICENSE.md)
[![Platform](https://img.shields.io/badge/Platform-Web%20%7C%20PHP%20%7C%20MySQL-blue.svg)](https://github.com/gugu-2/IMAT)
[![Architecture](https://img.shields.io/badge/Architecture-Vanilla%20SPA%20%7C%20Zero%20Dependencies-emerald.svg)](https://github.com/gugu-2/IMAT)
[![Question Bank](https://img.shields.io/badge/IMAT%20Archive-840%20Questions%20(2011--2024)-purple.svg)](https://github.com/gugu-2/IMAT)
[![TestAS Module](https://img.shields.io/badge/TestAS-Core%20%2B%20MIN%20%2B%20Engineering-cyan.svg)](https://github.com/gugu-2/IMAT)

An interactive, ambient-engineered **Examination Mastery Cockpit and Cloud Synchronizer** designed for high-stakes university medical and scientific admissions:
1. **🇮🇹 IMAT (International Medical Admissions Test)**: Italian Single-Cycle MD/Medicine & Surgery (MUR Ministry Decree).
2. **🇩🇪 TestAS (Test for Academic Studies)**: German University Bachelor Admissions (Core Test, MIN, and Engineering modules).

Built with an ultra-lightweight, high-performance vanilla architecture: zero build step required, offline-capable via `file:///`, and seamlessly backed by a lightweight PHP/MySQL REST API with real-time cloud synchronization.

---

## 📸 Cockpit Showcase

* **Dual Exam Ecosystem Switcher:** One-click seamless transition between IMAT and TestAS modes in the sticky header.
* **Ambient UI Design System:** Clean `#F3F4F8` canvas, rounded-22px reference cards, micro-borders (`#EAECEF`), pastel tags, and high-legibility typography.
* **Past Papers Question Bank:** All **840 questions** from 14 official IMAT exam years (2011–2024) organized across 30 curriculum sub-chapters with 25-question pagination, instant scoring, step-by-step logic, and examiner trap alerts.
* **TestAS German Admission Simulator:** Dynamic calculation using the official **Modified Bavarian Formula** combined with standard score bonuses (70–130) for top German universities (TUM, RWTH Aachen, Heidelberg, KIT, LMU, TU Berlin).

---

## ⚡ Key Features

### 1. 🇮🇹 IMAT Examination Cockpit
* **MUR Decree Curriculum:** Complete breakdown of official question distribution (Biology 38.3%, Chemistry 25.0%, Physics & Math 21.7%, Logic 8.3%, Reading 6.7%).
* **268-Topic Syllabus Tracker:** 30 categorized chapters with status filters (*Mastered*, *Reviewing*, *Pending*), priority ratings, and real-time progress calculations.
* **14-Day Study Scheduler:** Draggable timetable routine with Pomodoro focus blocks and syllabus topic linking.
* **840 Past Questions Archive (2011–2024):**
  * Official past questions with exact question counts per syllabus chapter.
  * Instant scoring HUD applying MUR negative marking (+1.5 correct, −0.4 incorrect, 0 blank).
  * Filter by chapter, exam paper year, and attempt status.
  * Comprehensive explanations with high-yield active recall points.
* **Italian Medical Universities Cutoff Simulator:** Score projection and benchmarks for Milan, Pavia, Bologna, Sapienza, Padua, and Federico II Naples.
* **High-Yield Formula Vault:** Rapid-lookup memory sheets for Physics, Chemistry stoichiometry, and Mental Math shortcuts.

### 2. 🇩🇪 TestAS Germany Bachelor Cockpit
* **Official Core Test (Kerntest):**
  * *Quantitative Problems* (Algebra, Work-Rate, Mixture, Speed)
  * *Inferring Relationships* (Verbal analogies and semantic pairs)
  * *Completing Patterns* (3×3 matrix rules and rotation logic)
  * *Continuing Number Series* (Two-tier interleaved, delta-of-deltas, alternating operations)
* **Subject Modules:** Mathematics, Computer Science & Natural Sciences (MIN) and Engineering.
* **Modified Bavarian Formula & NC Calculator:**
  $$\text{German Grade} = 1 + 3 \times \frac{N_{\max} - N_d}{N_{\max} - N_{\min}}$$
  Projects foreign high school percentage into the 1.0–4.0 German NC scale and applies standard score bonuses (70–130).
* **Number Series Recognition Catalog:** Formula algorithms for two-tier sequences, fibonacci summations, and speed arithmetic.

### 3. ☁️ Real-Time Cloud Synchronization
* **Automatic Bidirectional Sync:** Progress updates in real time to your online MySQL database.
* **Offline Fallback:** Automatically caches all progress to browser `localStorage` if connection is interrupted.
* **Cross-Device Continuity:** Study on desktop, tablet, or phone with state preserved everywhere.
* **Backup & Restore:** One-click JSON backup export and restore modal.

---

## 🏗️ System Architecture

```text
┌────────────────────────────────────────────────────────────────────────┐
│                          CLIENT BROWSER (SPA)                          │
│                                                                        │
│   ┌───────────────────────────┐      ┌─────────────────────────────┐   │
│   │   index.html (IMAT Mode)  │ ◄──► │  testas.html (TestAS Mode)  │   │
│   │   840 Qs Past Paper Bank  │      │  German NC & Bavarian Calc  │   │
│   │   268 Syllabus Checklist  │      │  Number Series Vault        │   │
│   │   14-Day Scheduler Grid   │      │  Practice Drills HUD        │   │
│   └─────────────┬─────────────┘      └──────────────┬──────────────┘   │
│                 │                                   │                  │
│                 └─────────────────┬─────────────────┘                  │
│                                   │                                    │
│                         localStorage Cache                             │
└───────────────────────────────────┼────────────────────────────────────┘
                                    │
                         HTTPS JSON REST API (Fetch)
                                    │
┌───────────────────────────────────▼────────────────────────────────────┐
│                    HOSTING SERVER (Apache / PHP)                       │
│                                                                        │
│   .htaccess (Security & CORS)  ──►  index.php (Auto Router)            │
│                                     api.php (REST Sync Controller)     │
│                                     config.php (PDO Connection & Auth) │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                 PDO MySQL
                                    │
┌───────────────────────────────────▼────────────────────────────────────┐
│                       REMOTE MYSQL DATABASE                            │
│                                                                        │
│   ├── imat_syllabus_topics   (Topic completion states & review notes)  │
│   ├── imat_timetable_tasks   (Scheduled sessions & completion flags)   │
│   ├── imat_specializations   (Score trends & subject weightings)       │
│   └── imat_mock_history      (Full test scores, accuracy & dates)      │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 📁 Repository Directory Structure

```text
.
├── index.html                           # Main SPA Cockpit (IMAT 840 Qs + Mode Switcher)
├── testas.html                          # Dedicated TestAS German Bachelor Cockpit
├── api.php                              # RESTful backend API for cloud synchronization
├── config.php                           # Database credentials & PDO connection handler
├── schema.sql                           # MySQL database schema (4 relational tables)
├── index.php                            # Server entry-point router
├── .htaccess                            # Apache rewrite rules & security headers
├── IMAT_Master_Strategy_and_Timetable.md# Official strategy & 24-week study curriculum
├── DATABASE_SETUP_GUIDE.md              # Cloud setup manual (Hostinger, cPanel, MySQL)
├── README.txt                           # Web hosting deployment cheat-sheet
├── LICENSE.md                           # MIT License
├── ui topic/                            # Reference design assets & UI wireframes
└── public_html/                         # Ready-to-deploy standalone web hosting bundle
```

---

## 🚀 Quick Start Guide

### Option A: 1-Click Server Deployment (Hostinger / cPanel / Apache)
1. In your hosting control panel, open **File Manager** and navigate to your `public_html` directory.
2. Upload the contents of the `public_html/` folder directly into `public_html`.
3. Open **phpMyAdmin**, create a database (e.g. `u123456_imat`), and import `schema.sql`.
4. Open `config.php` in your server's file editor and update your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_database_user');
   define('DB_PASS', 'your_database_password');
   ```
5. Navigate to `https://yourdomain.com` in your web browser. The system will automatically connect, sync, and display live cloud status!

### Option B: Local / Offline Usage
Simply open `index.html` or `testas.html` directly in any modern browser (Chrome, Edge, Firefox, Safari). The app will operate seamlessly with local persistence without requiring a web server or database.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE.md) — feel free to use, modify, and distribute for personal and academic study.
