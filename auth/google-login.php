<?php
// auth/google-login.php — Redirect user to Google OAuth
require_once '../includes/config.php';

$params = http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account',
    'state'         => bin2hex(random_bytes(16)),
]);

$_SESSION['oauth_state'] = explode('state=', $params)[1] ?? '';

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
exit;
