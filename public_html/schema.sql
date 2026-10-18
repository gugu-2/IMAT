-- =====================================================================
-- IMAT Syllabus & Timetable Mastery Engine - MySQL Database Schema
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+
-- Perfect for WordPress Hosting, cPanel, Plesk, LAMP/LEMP stacks.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Topics Progress Table (Stores completion status & notes for all 266 IMAT topics)
CREATE TABLE IF NOT EXISTS `imat_topics_progress` (
  `topic_id` VARCHAR(64) NOT NULL,
  `subject` VARCHAR(64) NOT NULL,
  `chapter` VARCHAR(128) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `tier` TINYINT(1) NOT NULL DEFAULT 1,
  `stars` TINYINT(1) NOT NULL DEFAULT 5,
  `status` ENUM('not_started', 'in_progress', 'mastered') NOT NULL DEFAULT 'not_started',
  `notes` TEXT NULL,
  `completed_at` DATETIME NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`topic_id`),
  INDEX `idx_subject` (`subject`),
  INDEX `idx_tier` (`tier`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Study Timetable Tasks Table (Stores manual & scheduled tasks)
CREATE TABLE IF NOT EXISTS `imat_tasks` (
  `task_id` VARCHAR(64) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(64) NOT NULL,
  `day` ENUM('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun') NOT NULL,
  `time_slot` VARCHAR(64) NOT NULL,
  `tier` TINYINT(1) NOT NULL DEFAULT 1,
  `status` ENUM('not_started', 'in_progress', 'mastered') NOT NULL DEFAULT 'not_started',
  `tag` VARCHAR(64) DEFAULT 'Study Session',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_id`),
  INDEX `idx_day` (`day`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Subject Specialization & Self-Improvement Table
CREATE TABLE IF NOT EXISTS `imat_specializations` (
  `subject` VARCHAR(64) NOT NULL,
  `accuracy` INT(3) NOT NULL DEFAULT 0,
  `level_title` VARCHAR(128) NOT NULL DEFAULT 'Level 1: Unstarted',
  `strengths` TEXT NULL,
  `weak_spots` TEXT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Mock Exam History Table
CREATE TABLE IF NOT EXISTS `imat_mock_history` (
  `id` VARCHAR(64) NOT NULL,
  `exam_name` VARCHAR(255) NOT NULL,
  `exam_date` DATE NOT NULL,
  `score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `accuracy` INT(3) NOT NULL DEFAULT 0,
  `penalty` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `admission_tier` VARCHAR(255) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_exam_date` (`exam_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Solar Insights Matrix Modules Table (A1 to C4 modules)
CREATE TABLE IF NOT EXISTS `imat_matrix_modules` (
  `module_id` VARCHAR(16) NOT NULL,
  `title` VARCHAR(128) NOT NULL,
  `subject` VARCHAR(64) NOT NULL,
  `status` ENUM('optimal', 'in_progress', 'fault', 'not_started') NOT NULL DEFAULT 'not_started',
  `score_pct` INT(3) NOT NULL DEFAULT 0,
  `count_qs` INT(5) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Live Routine Timer Study Sessions Table
CREATE TABLE IF NOT EXISTS `imat_timer_sessions` (
  `session_id` VARCHAR(64) NOT NULL,
  `subject` VARCHAR(64) NULL,
  `topic` VARCHAR(255) NULL,
  `duration_seconds` INT(11) NOT NULL DEFAULT 0,
  `session_date` DATE NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  INDEX `idx_session_date` (`session_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. App State & Settings Key-Value Store (For atomic backup/restore and custom configurations)
CREATE TABLE IF NOT EXISTS `imat_settings` (
  `setting_key` VARCHAR(128) NOT NULL,
  `setting_value` LONGTEXT NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
