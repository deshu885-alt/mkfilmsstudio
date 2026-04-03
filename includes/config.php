<?php
// ============================================================
//  config.php — mkfilms.studio
//  Edit DB credentials and Google OAuth keys here
// ============================================================

// --- Database ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'mkfilms');
define('DB_USER', 'root');        // change this
define('DB_PASS', 'Panda@123');            // change this
define('DB_CHARSET', 'utf8mb4');

// --- Site ---
define('SITE_URL', 'http://localhost');   // change to https://mkfilms.studio in prod
define('SITE_NAME', 'MK Films');
define('SITE_TAGLINE', 'Wedding Cinematography · Mandi, Himachal Pradesh');

// --- Google OAuth ---
// Get from: https://console.cloud.google.com → Credentials → OAuth 2.0
define('GOOGLE_CLIENT_ID',     'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  SITE_URL . '/auth/google-callback.php');

// --- Session ---
session_start();

// --- DB Connection (PDO) ---
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]));
}

// --- Helper: current logged-in user ---
function currentUser() {
    return $_SESSION['user'] ?? null;
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

// --- Helper: redirect ---
function redirect($url) {
    header("Location: $url");
    exit;
}

// --- Helper: sanitize output ---
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
