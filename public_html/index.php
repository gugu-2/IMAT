<?php
/**
 * IMAT Examination Dashboard & Timetable Engine
 * Primary PHP Entrypoint for Hostinger, cPanel, and Web Hosting
 */

// Serve index.html directly
if (file_exists(__DIR__ . '/index.html')) {
    include __DIR__ . '/index.html';
} else {
    header('Location: index.html');
}
exit;
