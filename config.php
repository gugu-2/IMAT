<?php
/**
 * IMAT Syllabus & Timetable Mastery Engine
 * 100% Automated Zero-Configuration Database Engine
 * 
 * AUTOMATED LOGIC:
 * 1. Default: Automatically uses SQLite database on the server (Zero setup, no passwords, no cPanel steps!)
 * 2. If WordPress is installed in public_html, it automatically detects and connects to WordPress MySQL!
 * 3. Optional: If you ever want to use custom MySQL, you can fill in lines 15-18.
 */

// Optional: Manual MySQL credentials (ONLY needed if you want custom MySQL instead of auto-SQLite)
$custom_db_host = 'localhost';             // Leave blank for 100% automated zero-config SQLite
$custom_db_name = 'u713031512_sofia44martin';             // Leave blank for 100% automated zero-config SQLite
$custom_db_user = 'u713031512_hypasia45space';             // Leave blank for 100% automated zero-config SQLite
$custom_db_pass = 'YOUR_HOSTINGER_DB_PASSWORD_HERE';             // Leave blank for 100% automated zero-config SQLite

// Optional API Secret Key (Leave blank for open multi-device access)
define('IMAT_API_KEY', '');

// =========================================================================
// AUTOMATED ZERO-CONFIG RESOLUTION
// =========================================================================

// Priority A: If manual MySQL credentials were provided
if (!empty($custom_db_name)) {
    define('DB_DRIVER', 'mysql');
    define('DB_HOST', $custom_db_host ?: 'localhost');
    define('DB_NAME', $custom_db_name);
    define('DB_USER', $custom_db_user);
    define('DB_PASS', $custom_db_pass);
    define('DB_SOURCE', 'manual_mysql');
} else {
    // Priority B: Check for WordPress wp-config.php in current or parent folders
    $wp_detected = false;
    $search_paths = [
        __DIR__ . '/wp-config.php',
        __DIR__ . '/../wp-config.php',
        __DIR__ . '/../../wp-config.php'
    ];

    foreach ($search_paths as $path) {
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $m_name) &&
                preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $m_user) &&
                preg_match("/define\s*\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"]([^'\"]*)['\"]\s*\)/", $content, $m_pass) &&
                preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $m_host)) {
                
                define('DB_DRIVER', 'mysql');
                define('DB_NAME', $m_name[1]);
                define('DB_USER', $m_user[1]);
                define('DB_PASS', $m_pass[1]);
                define('DB_HOST', $m_host[1]);
                define('DB_SOURCE', 'wordpress_auto');
                $wp_detected = true;
                break;
            }
        }
    }

    // Priority C: 100% AUTOMATED ZERO-SETUP SQLITE (Default)
    // No cPanel database creation, no passwords, no config editing needed!
    if (!$wp_detected) {
        define('DB_DRIVER', 'sqlite');
        define('DB_FILE', __DIR__ . '/imat_database.sqlite');
        define('DB_NAME', 'imat_database.sqlite');
        define('DB_HOST', 'localhost (Zero-Config SQLite)');
        define('DB_SOURCE', 'auto_sqlite_zero_setup');
    }
}

/**
 * Returns a PDO database connection (SQLite or MySQL)
 */
function get_db_connection() {
    try {
        if (defined('DB_DRIVER') && DB_DRIVER === 'sqlite') {
            $pdo = new PDO("sqlite:" . DB_FILE);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Enable WAL mode for high concurrency across multi-device visits
            @$pdo->exec("PRAGMA journal_mode = WAL;");
            return $pdo;
        } else {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error'   => 'Database connection failed: ' . $e->getMessage(),
            'source'  => defined('DB_SOURCE') ? DB_SOURCE : 'unknown'
        ]);
        exit;
    }
}
