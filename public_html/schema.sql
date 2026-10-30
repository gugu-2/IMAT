-- =====================================================================
-- IMAT & TestAS Examination Mastery Engine - MySQL Database Schema
-- Multi-Exam Isolated Cloud Persistence (IMAT & TestAS completely separated)
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+, SQLite 3
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- 1. IMAT EXAMINATION CLOUD TABLES (ITALY MD)
-- =====================================================================

-- IMAT Topics Progress (268 topics)
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
  INDEX `idx_imat_subject` (`subject`),
  INDEX `idx_imat_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMAT Study Timetable Tasks
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
  INDEX `idx_imat_day` (`day`),
  INDEX `idx_imat_task_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMAT Subject Specializations
CREATE TABLE IF NOT EXISTS `imat_specializations` (
  `subject` VARCHAR(64) NOT NULL,
  `accuracy` INT(3) NOT NULL DEFAULT 0,
  `level_title` VARCHAR(128) NOT NULL DEFAULT 'Level 1: Unstarted',
  `strengths` TEXT NULL,
  `weak_spots` TEXT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMAT Mock History
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMAT Settings & Snapshots
CREATE TABLE IF NOT EXISTS `imat_settings` (
  `setting_key` VARCHAR(128) NOT NULL,
  `setting_value` LONGTEXT NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- 2. TESTAS EXAMINATION CLOUD TABLES (GERMANY BSC)
-- COMPLETELY ISOLATED - ZERO COLLISION WITH IMAT
-- =====================================================================

-- TestAS Topics Progress (Engineering 11 Chapters + Core Module)
CREATE TABLE IF NOT EXISTS `testas_topics_progress` (
  `topic_id` VARCHAR(64) NOT NULL,
  `module` VARCHAR(64) NOT NULL,
  `chapter` VARCHAR(128) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `priority` VARCHAR(32) NOT NULL DEFAULT 'High',
  `study_pct` DECIMAL(4,1) NOT NULL DEFAULT 10.0,
  `phase` VARCHAR(64) NOT NULL DEFAULT 'Phase 1: Foundations',
  `status` ENUM('not_started', 'in_progress', 'mastered') NOT NULL DEFAULT 'not_started',
  `notes` TEXT NULL,
  `completed_at` DATETIME NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`topic_id`),
  INDEX `idx_testas_module` (`module`),
  INDEX `idx_testas_phase` (`phase`),
  INDEX `idx_testas_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TestAS Timetable Tasks (100h / 4-Phase Schedule)
CREATE TABLE IF NOT EXISTS `testas_tasks` (
  `task_id` VARCHAR(64) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(64) NOT NULL,
  `day` ENUM('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun') NOT NULL,
  `time_slot` VARCHAR(64) NOT NULL,
  `priority` VARCHAR(32) DEFAULT 'High',
  `phase` VARCHAR(64) DEFAULT 'Phase 1: Foundations',
  `status` ENUM('not_started', 'in_progress', 'mastered') NOT NULL DEFAULT 'not_started',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_id`),
  INDEX `idx_testas_task_day` (`day`),
  INDEX `idx_testas_task_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TestAS Engineering Specializations (11 Chapter Allocation Weights)
CREATE TABLE IF NOT EXISTS `testas_specializations` (
  `subject_id` VARCHAR(64) NOT NULL,
  `title` VARCHAR(128) NOT NULL,
  `priority` VARCHAR(32) NOT NULL,
  `weight_pct` DECIMAL(4,1) NOT NULL DEFAULT 10.0,
  `hours_100h` DECIMAL(4,1) NOT NULL DEFAULT 5.0,
  `strengths` TEXT NULL,
  `weak_spots` TEXT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TestAS Mock History & Standard Scores (70-130)
CREATE TABLE IF NOT EXISTS `testas_mock_history` (
  `id` VARCHAR(64) NOT NULL,
  `exam_format` ENUM('digital', 'paper') NOT NULL DEFAULT 'digital',
  `exam_name` VARCHAR(255) NOT NULL,
  `exam_date` DATE NOT NULL,
  `standard_score` INT(3) NOT NULL DEFAULT 100,
  `percentile_rank` INT(3) NOT NULL DEFAULT 50,
  `bavarian_nc` DECIMAL(3,2) NOT NULL DEFAULT 2.00,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TestAS Settings & Isolated Snapshots
CREATE TABLE IF NOT EXISTS `testas_settings` (
  `setting_key` VARCHAR(128) NOT NULL,
  `setting_value` LONGTEXT NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
