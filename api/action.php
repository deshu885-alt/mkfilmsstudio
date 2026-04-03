<?php
// api/action.php — Like & Comment AJAX handler
require_once '../includes/config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Auth check for write actions
if (in_array($action, ['like', 'comment']) && !isLoggedIn()) {
    echo json_encode(['status' => 'auth_required']);
    exit;
}

switch ($action) {

    // --- Toggle Like ---
    case 'like':
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $userId  = (int)$_SESSION['user']['id'];

        // Check if already liked
        $stmt = $pdo->prepare("SELECT id FROM photo_likes WHERE photo_id=? AND user_id=?");
        $stmt->execute([$photoId, $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Unlike
            $pdo->prepare("DELETE FROM photo_likes WHERE photo_id=? AND user_id=?")->execute([$photoId, $userId]);
            $pdo->prepare("UPDATE photos SET like_count = like_count - 1 WHERE id=?")->execute([$photoId]);
            $liked = false;
        } else {
            // Like
            $pdo->prepare("INSERT INTO photo_likes (photo_id, user_id) VALUES (?,?)")->execute([$photoId, $userId]);
            $pdo->prepare("UPDATE photos SET like_count = like_count + 1 WHERE id=?")->execute([$photoId]);
            $liked = true;
        }

        $count = $pdo->prepare("SELECT like_count FROM photos WHERE id=?");
        $count->execute([$photoId]);
        $row = $count->fetch();

        echo json_encode(['status' => 'ok', 'liked' => $liked, 'count' => $row['like_count']]);
        break;

    // --- Add Comment ---
    case 'comment':
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $userId  = (int)$_SESSION['user']['id'];
        $text    = trim($_POST['comment'] ?? '');

        if (empty($text) || strlen($text) > 500) {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid comment']);
            exit;
        }

        $pdo->prepare("INSERT INTO comments (photo_id, user_id, comment) VALUES (?,?,?)")
            ->execute([$photoId, $userId, $text]);

        echo json_encode([
            'status'  => 'ok',
            'name'    => e($_SESSION['user']['name']),
            'avatar'  => e($_SESSION['user']['avatar']),
            'comment' => e($text),
            'time'    => 'Just now',
        ]);
        break;

    // --- Get comments for a photo ---
    case 'get_comments':
        $photoId = (int)($_GET['photo_id'] ?? 0);
        $stmt = $pdo->prepare("
            SELECT c.comment, c.created_at, u.name, u.avatar
            FROM comments c JOIN users u ON c.user_id = u.id
            WHERE c.photo_id = ?
            ORDER BY c.created_at DESC LIMIT 20
        ");
        $stmt->execute([$photoId]);
        $comments = $stmt->fetchAll();
        echo json_encode(['status' => 'ok', 'comments' => $comments]);
        break;

    // --- Pre-booking form ---
    case 'prebook':
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $date  = $_POST['wedding_date'] ?? null;
        $msg   = trim($_POST['message'] ?? '');

        if (empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'msg' => 'Name and phone required']);
            exit;
        }

        $pdo->prepare("INSERT INTO prebookings (name, phone, email, wedding_date, message) VALUES (?,?,?,?,?)")
            ->execute([$name, $phone, $email ?: null, $date ?: null, $msg]);

        echo json_encode(['status' => 'ok', 'msg' => 'Booking request received! We\'ll call you soon 🎬']);
        break;

    // --- Check like status ---
    case 'like_status':
        if (!isLoggedIn()) {
            echo json_encode(['liked' => false]);
            exit;
        }
        $photoId = (int)($_GET['photo_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT id FROM photo_likes WHERE photo_id=? AND user_id=?");
        $stmt->execute([$photoId, $_SESSION['user']['id']]);
        echo json_encode(['liked' => (bool)$stmt->fetch()]);
        break;

    default:
        echo json_encode(['status' => 'error', 'msg' => 'Unknown action']);
}
