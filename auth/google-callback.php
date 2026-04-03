<?php
// auth/google-callback.php — Handle Google OAuth response
require_once '../includes/config.php';

$code  = $_GET['code']  ?? '';
$error = $_GET['error'] ?? '';

if ($error || !$code) {
    redirect(SITE_URL . '/?auth_error=1');
}

// 1. Exchange code for access token
$tokenResponse = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query([
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]),
    ],
]));

$tokenData = json_decode($tokenResponse, true);
if (empty($tokenData['access_token'])) {
    redirect(SITE_URL . '/?auth_error=1');
}

// 2. Get user info from Google
$userInfoResponse = file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, stream_context_create([
    'http' => [
        'header' => "Authorization: Bearer " . $tokenData['access_token'] . "\r\n",
    ],
]));

$googleUser = json_decode($userInfoResponse, true);
if (empty($googleUser['id'])) {
    redirect(SITE_URL . '/?auth_error=1');
}

// 3. Upsert user in our DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
$stmt->execute([$googleUser['id']]);
$user = $stmt->fetch();

if (!$user) {
    // New user — insert
    $stmt = $pdo->prepare("INSERT INTO users (google_id, name, email, avatar) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $googleUser['id'],
        $googleUser['name'],
        $googleUser['email'],
        $googleUser['picture'] ?? '',
    ]);
    $userId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
} else {
    // Existing — update avatar/name
    $stmt = $pdo->prepare("UPDATE users SET name=?, avatar=? WHERE id=?");
    $stmt->execute([$googleUser['name'], $googleUser['picture'] ?? '', $user['id']]);
}

// 4. Set session
$_SESSION['user'] = [
    'id'     => $user['id'],
    'name'   => $user['name'],
    'email'  => $user['email'],
    'avatar' => $user['avatar'],
];

// 5. Redirect back
$returnTo = $_SESSION['login_return'] ?? SITE_URL . '/';
unset($_SESSION['login_return']);
redirect($returnTo);
