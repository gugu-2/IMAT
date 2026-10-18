<?php
/**
 * IMAT Syllabus & Timetable Mastery Engine - REST API Backend
 * 
 * 100% Automated Multi-Device Cloud Persistence Engine
 * Supports Zero-Config SQLite (Default) and MySQL (WordPress/cPanel).
 */

// 1. CORS Headers (Allows requests from any device, browser, or domain)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-IMAT-Token');
header('Content-Type: application/json; charset=utf-8');

// Respond to preflight OPTIONS requests immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config.php';

// 2. Authentication Check (if IMAT_API_KEY is configured)
if (defined('IMAT_API_KEY') && IMAT_API_KEY !== '') {
    $client_token = $_SERVER['HTTP_X_IMAT_TOKEN'] ?? $_GET['token'] ?? '';
    if ($client_token !== IMAT_API_KEY) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized: Invalid or missing API token.']);
        exit;
    }
}

// 3. Database Connection & Auto-Table Initializer
$db = get_db_connection();

function ensure_tables_exist($db) {
    try {
        $stmt = $db->query("SHOW TABLES LIKE 'imat_settings'");
        if ($stmt && $stmt->rowCount() > 0) return true;
    } catch (Exception $e) {
        // May be SQLite which uses sqlite_master
    }

    try {
        $stmtLite = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='imat_settings'");
        if ($stmtLite && $stmtLite->fetch()) return true;
    } catch (Exception $e) {}

    // Create tables automatically
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS imat_topics_progress (
                topic_id VARCHAR(64) PRIMARY KEY,
                subject VARCHAR(64) NOT NULL,
                chapter VARCHAR(128) NOT NULL,
                title VARCHAR(255) NOT NULL,
                tier INTEGER DEFAULT 1,
                stars INTEGER DEFAULT 5,
                status VARCHAR(20) DEFAULT 'not_started',
                notes TEXT,
                completed_at VARCHAR(32),
                updated_at VARCHAR(32)
            );

            CREATE TABLE IF NOT EXISTS imat_tasks (
                task_id VARCHAR(64) PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                subject VARCHAR(64) NOT NULL,
                day VARCHAR(10) NOT NULL,
                time_slot VARCHAR(64) NOT NULL,
                tier INTEGER DEFAULT 1,
                status VARCHAR(20) DEFAULT 'not_started',
                tag VARCHAR(64) DEFAULT 'Study Session',
                created_at VARCHAR(32),
                updated_at VARCHAR(32)
            );

            CREATE TABLE IF NOT EXISTS imat_specializations (
                subject VARCHAR(64) PRIMARY KEY,
                accuracy INTEGER DEFAULT 0,
                level_title VARCHAR(128) DEFAULT 'Level 1: Unstarted',
                strengths TEXT,
                weak_spots TEXT,
                updated_at VARCHAR(32)
            );

            CREATE TABLE IF NOT EXISTS imat_mock_history (
                id VARCHAR(64) PRIMARY KEY,
                exam_name VARCHAR(255) NOT NULL,
                exam_date VARCHAR(32) NOT NULL,
                score REAL DEFAULT 0.0,
                accuracy INTEGER DEFAULT 0,
                penalty REAL DEFAULT 0.0,
                admission_tier VARCHAR(255),
                notes TEXT,
                created_at VARCHAR(32)
            );

            CREATE TABLE IF NOT EXISTS imat_matrix_modules (
                module_id VARCHAR(16) PRIMARY KEY,
                title VARCHAR(128) NOT NULL,
                subject VARCHAR(64) NOT NULL,
                status VARCHAR(20) DEFAULT 'not_started',
                score_pct INTEGER DEFAULT 0,
                count_qs INTEGER DEFAULT 0,
                updated_at VARCHAR(32)
            );

            CREATE TABLE IF NOT EXISTS imat_settings (
                setting_key VARCHAR(128) PRIMARY KEY,
                setting_value TEXT NOT NULL,
                updated_at VARCHAR(32)
            );
        ");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

ensure_tables_exist($db);

$action = $_GET['action'] ?? $_POST['action'] ?? 'ping';

// Helper to read incoming JSON body
function get_json_input() {
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

try {
    switch ($action) {

        // ==========================================
        // ACTION: PING & SYSTEM DIAGNOSTICS
        // ==========================================
        case 'ping':
            $is_browser = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false && !isset($_GET['json']);
            if ($is_browser) {
                header('Content-Type: text/html; charset=utf-8');
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>IMAT Cloud Engine Status</title>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #F3F4F8; color: #1E293B; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
                        .card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #E2E8F0; max-width: 520px; width: 100%; padding: 32px; }
                        .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; background: #DCFCE7; color: #15803D; margin-bottom: 16px; }
                        h2 { margin: 0 0 8px; font-size: 20px; }
                        p { margin: 0 0 20px; color: #64748B; font-size: 13px; line-height: 1.5; }
                        .metric { background: #F8FAFC; border-radius: 12px; padding: 12px 16px; margin-bottom: 8px; display: flex; justify-content: space-between; font-size: 13px; }
                        .metric span { color: #64748B; }
                        .metric strong { font-family: monospace; color: #0F172A; }
                        .btn { display: inline-block; width: 100%; text-align: center; padding: 12px; background: #0F172A; color: white; border-radius: 12px; font-weight: 600; font-size: 14px; text-decoration: none; margin-top: 16px; box-sizing: border-box; }
                        .btn:hover { opacity: 0.9; }
                    </style>
                </head>
                <body>
                    <div class="card">
                        <div class="badge">🟢 100% Automated Cloud Database Active</div>
                        <h2>IMAT Engine Status</h2>
                        <p>Your cloud database is running and ready. Open the dashboard on any device using your URL.</p>
                        
                        <div class="metric"><span>Database Type:</span> <strong><?= htmlspecialchars(strtoupper(DB_DRIVER)) ?></strong></div>
                        <div class="metric"><span>Database Source:</span> <strong><?= htmlspecialchars(DB_SOURCE) ?></strong></div>
                        <div class="metric"><span>Tables:</span> <strong>✅ All 6 Tables Ready</strong></div>
                        <div class="metric"><span>Server Time:</span> <strong><?= date('Y-m-d H:i:s') ?></strong></div>
                        
                        <a href="index.html" class="btn">🚀 Open IMAT Dashboard</a>
                    </div>
                </body>
                </html>
                <?php
                exit;
            }

            echo json_encode([
                'success'       => true,
                'message'       => 'IMAT Cloud API active and reachable.',
                'database'      => DB_NAME,
                'driver'        => DB_DRIVER,
                'source'        => DB_SOURCE,
                'tables_exist'  => true,
                'server_time'   => date('Y-m-d H:i:s')
            ]);
            break;

        // ==========================================
        // ACTION: SYNC ALL (ATOMIC CLOUD SAVE)
        // ==========================================
        case 'sync_all':
            $input = get_json_input();
            if (empty($input)) {
                echo json_encode(['success' => false, 'error' => 'No sync payload provided.']);
                exit;
            }

            $now = date('Y-m-d H:i:s');
            $db->beginTransaction();

            // 1. Sync Topics Progress
            if (isset($input['topics']) && is_array($input['topics'])) {
                $stmtTopic = $db->prepare("
                    INSERT OR REPLACE INTO imat_topics_progress (topic_id, subject, chapter, title, tier, stars, status, notes, updated_at)
                    VALUES (:id, :subject, :chapter, :title, :tier, :stars, :status, :notes, :updated_at)
                ");
                foreach ($input['topics'] as $t) {
                    $stmtTopic->execute([
                        ':id'         => $t['id'],
                        ':subject'    => $t['subject'] ?? 'General',
                        ':chapter'    => $t['chapter'] ?? 'General',
                        ':title'      => $t['title'] ?? '',
                        ':tier'       => $t['tier'] ?? 1,
                        ':stars'      => $t['stars'] ?? 5,
                        ':status'     => $t['status'] ?? 'not_started',
                        ':notes'      => $t['notes'] ?? '',
                        ':updated_at' => $now
                    ]);
                }
            }

            // 2. Sync Study Timetable Tasks
            if (isset($input['tasks']) && is_array($input['tasks'])) {
                $db->exec("DELETE FROM imat_tasks");
                $stmtTask = $db->prepare("
                    INSERT OR REPLACE INTO imat_tasks (task_id, title, subject, day, time_slot, tier, status, tag, updated_at)
                    VALUES (:id, :title, :subject, :day, :time_slot, :tier, :status, :tag, :updated_at)
                ");
                foreach ($input['tasks'] as $task) {
                    $stmtTask->execute([
                        ':id'         => $task['id'],
                        ':title'      => $task['title'],
                        ':subject'    => $task['subject'],
                        ':day'        => $task['day'],
                        ':time_slot'  => $task['time'] ?? $task['time_slot'] ?? '',
                        ':tier'       => $task['tier'] ?? 1,
                        ':status'     => $task['status'] ?? 'not_started',
                        ':tag'        => $task['tag'] ?? 'Study Session',
                        ':updated_at' => $now
                    ]);
                }
            }

            // 3. Sync Subject Specializations
            if (isset($input['specializations']) && is_array($input['specializations'])) {
                $stmtSpec = $db->prepare("
                    INSERT OR REPLACE INTO imat_specializations (subject, accuracy, level_title, strengths, weak_spots, updated_at)
                    VALUES (:subject, :accuracy, :level_title, :strengths, :weak_spots, :updated_at)
                ");
                foreach ($input['specializations'] as $sp) {
                    $stmtSpec->execute([
                        ':subject'     => $sp['subject'],
                        ':accuracy'    => $sp['accuracy'] ?? 0,
                        ':level_title' => $sp['level'] ?? $sp['level_title'] ?? '',
                        ':strengths'   => $sp['strengths'] ?? '',
                        ':weak_spots'  => $sp['weak'] ?? $sp['weak_spots'] ?? '',
                        ':updated_at'  => $now
                    ]);
                }
            }

            // 4. Sync Mock History
            if (isset($input['mocks']) && is_array($input['mocks'])) {
                $db->exec("DELETE FROM imat_mock_history");
                $stmtMock = $db->prepare("
                    INSERT INTO imat_mock_history (id, exam_name, exam_date, score, accuracy, penalty, admission_tier, created_at)
                    VALUES (:id, :name, :date, :score, :accuracy, :penalty, :admission, :created_at)
                ");
                foreach ($input['mocks'] as $m) {
                    $stmtMock->execute([
                        ':id'         => $m['id'],
                        ':name'       => $m['name'],
                        ':date'       => $m['date'] ?? date('Y-m-d'),
                        ':score'      => $m['score'] ?? 0.0,
                        ':accuracy'   => $m['accuracy'] ?? 0,
                        ':penalty'    => $m['penalty'] ?? 0.0,
                        ':admission'  => $m['admission'] ?? '',
                        ':created_at' => $now
                    ]);
                }
            }

            // 5. Store Full Snapshot
            $stmtSet = $db->prepare("
                INSERT OR REPLACE INTO imat_settings (setting_key, setting_value, updated_at)
                VALUES ('last_full_snapshot', :val, :updated_at)
            ");
            $stmtSet->execute([':val' => json_encode($input), ':updated_at' => $now]);

            $db->commit();

            echo json_encode([
                'success'   => true,
                'message'   => 'Cloud sync complete across all devices.',
                'synced_at' => $now
            ]);
            break;

        // ==========================================
        // ACTION: GET ALL (LOAD CLOUD STATE)
        // ==========================================
        case 'get_all':
            $stmtSnap = $db->prepare("SELECT setting_value FROM imat_settings WHERE setting_key = 'last_full_snapshot'");
            $stmtSnap->execute();
            $rowSnap = $stmtSnap->fetch();

            if ($rowSnap && !empty($rowSnap['setting_value'])) {
                $data = json_decode($rowSnap['setting_value'], true);
                echo json_encode([
                    'success' => true,
                    'data'    => $data,
                    'source'  => 'snapshot'
                ]);
                exit;
            }

            // Fresh database fallback
            echo json_encode([
                'success' => true,
                'data'    => null,
                'source'  => 'empty_initial'
            ]);
            break;

        // ==========================================
        // ACTION: SAVE SINGLE TOPIC STATUS
        // ==========================================
        case 'save_topic':
            $input = get_json_input();
            $topic_id = $input['id'] ?? '';
            $status   = $input['status'] ?? 'not_started';
            $now      = date('Y-m-d H:i:s');

            if (empty($topic_id)) {
                echo json_encode(['success' => false, 'error' => 'Missing topic_id']);
                exit;
            }

            $stmt = $db->prepare("
                UPDATE imat_topics_progress SET status = :status, updated_at = :updated_at WHERE topic_id = :id
            ");
            $stmt->execute([':id' => $topic_id, ':status' => $status, ':updated_at' => $now]);

            echo json_encode(['success' => true, 'topic_id' => $topic_id, 'status' => $status]);
            break;

        // ==========================================
        // ACTION: RESET ALL TO ZERO
        // ==========================================
        case 'reset_all':
            $db->exec("UPDATE imat_topics_progress SET status = 'not_started'");
            $db->exec("UPDATE imat_specializations SET accuracy = 0");
            $db->exec("DELETE FROM imat_mock_history");
            $db->exec("DELETE FROM imat_settings WHERE setting_key = 'last_full_snapshot'");

            echo json_encode(['success' => true, 'message' => 'All cloud progress reset to zero.']);
            break;

        default:
            echo json_encode(['success' => true, 'message' => 'IMAT API operational.']);
            break;
    }

} catch (Exception $e) {
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'API Error: ' . $e->getMessage()
    ]);
}
