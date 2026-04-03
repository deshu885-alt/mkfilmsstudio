<?php
require_once 'includes/config.php';

// Must be logged in
if (!isLoggedIn()) {
    $_SESSION['login_return'] = SITE_URL . '/profile.php';
    redirect('auth/google-login.php');
}

$user = currentUser();

// Get user's liked photos
$stmt = $pdo->prepare("
    SELECT p.*, w.couple_names
    FROM photo_likes pl
    JOIN photos p ON pl.photo_id = p.id
    JOIN weddings w ON p.wedding_id = w.id
    WHERE pl.user_id = ?
    ORDER BY pl.created_at DESC
");
$stmt->execute([$user['id']]);
$likedPhotos = $stmt->fetchAll();

// Get user's comments
$stmt = $pdo->prepare("
    SELECT c.comment, c.created_at, p.filename, p.id as photo_id
    FROM comments c JOIN photos p ON c.photo_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC LIMIT 20
");
$stmt->execute([$user['id']]);
$comments = $stmt->fetchAll();

// Split liked photos into 2 cols
$col1 = []; $col2 = [];
foreach ($likedPhotos as $i => $p) {
    if ($i % 2 === 0) $col1[] = $p;
    else              $col2[] = $p;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($user['name']) ?> — MK Films</title>
<link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<nav class="nav">
  <div class="nav-logo">MK Films <span>Mandi, Himachal Pradesh</span></div>
  <div class="nav-right">
    <a href="auth/logout.php" class="btn-google" style="font-size:.75rem">Sign out</a>
    <a href="profile.php" class="avatar-btn">
      <img src="<?= e($user['avatar']) ?>" alt="<?= e($user['name']) ?>" referrerpolicy="no-referrer">
    </a>
  </div>
</nav>

<!-- ── Profile Header ── -->
<div class="profile-header">
  <div class="profile-avatar-wrap">
    <img src="<?= e($user['avatar']) ?>" alt="<?= e($user['name']) ?>" referrerpolicy="no-referrer">
  </div>
  <div class="profile-name"><?= e($user['name']) ?></div>
  <div class="profile-handle"><?= e(strtolower(str_replace(' ', '', $user['name']))) ?></div>
  <div class="profile-location">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
    Himachal Pradesh
  </div>
  <div class="profile-stats">
    <div style="text-align:center">
      <div class="profile-stat-num"><?= count($likedPhotos) ?></div>
      <div class="profile-stat-label">Liked</div>
    </div>
    <div style="text-align:center">
      <div class="profile-stat-num"><?= count($comments) ?></div>
      <div class="profile-stat-label">Comments</div>
    </div>
    <div style="text-align:center">
      <div class="profile-stat-num">1</div>
      <div class="profile-stat-label">Wedding</div>
    </div>
  </div>
</div>

<!-- ── Tabs ── -->
<div class="profile-tabs">
  <div class="profile-tab active" onclick="switchTab('liked', this)">❤️ Liked Photos</div>
  <div class="profile-tab" onclick="switchTab('comments', this)">💬 Comments</div>
</div>

<!-- ── Liked Photos Grid ── -->
<div id="tab-liked">
  <?php if (empty($likedPhotos)): ?>
    <div style="text-align:center;padding:50px 20px;color:var(--text3)">
      <div style="font-size:2.5rem;margin-bottom:10px">💔</div>
      <div style="font-family:var(--font-display);font-size:1.2rem;font-style:italic">No liked photos yet</div>
      <div style="font-size:.82rem;margin-top:6px">Go explore and heart the moments you love!</div>
      <a href="index.php" style="display:inline-block;margin-top:16px;background:var(--accent);color:#111;border-radius:50px;padding:10px 24px;font-size:.84rem">Browse Photos</a>
    </div>
  <?php else: ?>
    <div class="masonry-grid" style="padding-top:12px">
      <div class="masonry-col">
        <?php foreach ($col1 as $photo): ?>
        <div class="photo-card" onclick="openPhotoModal(<?= $photo['id'] ?>)">
          <img src="uploads/photos/<?= e($photo['filename']) ?>" alt="<?= e($photo['caption'] ?? '') ?>" loading="lazy" onerror="this.src='assets/images/placeholder.jpg'">
          <div class="photo-card-overlay">
            <div class="photo-card-caption"><?= e($photo['caption'] ?? '') ?></div>
            <div style="color:#ff6b8a;font-size:.7rem">❤️ <?= $photo['like_count'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="masonry-col">
        <?php foreach ($col2 as $photo): ?>
        <div class="photo-card" onclick="openPhotoModal(<?= $photo['id'] ?>)">
          <img src="uploads/photos/<?= e($photo['filename']) ?>" alt="<?= e($photo['caption'] ?? '') ?>" loading="lazy" onerror="this.src='assets/images/placeholder.jpg'">
          <div class="photo-card-overlay">
            <div class="photo-card-caption"><?= e($photo['caption'] ?? '') ?></div>
            <div style="color:#ff6b8a;font-size:.7rem">❤️ <?= $photo['like_count'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- ── Comments Tab ── -->
<div id="tab-comments" class="hidden" style="padding:12px var(--gap) 80px">
  <?php if (empty($comments)): ?>
    <div style="text-align:center;padding:50px 20px;color:var(--text3)">
      <div style="font-size:2.5rem;margin-bottom:10px">💬</div>
      <div style="font-family:var(--font-display);font-size:1.2rem;font-style:italic">No comments yet</div>
    </div>
  <?php else: ?>
    <?php foreach ($comments as $c): ?>
    <div style="display:flex;gap:12px;padding:14px;background:var(--bg2);border-radius:12px;margin-bottom:10px;align-items:flex-start">
      <img src="uploads/photos/<?= e($c['filename']) ?>" style="width:52px;height:52px;border-radius:8px;object-fit:cover;flex-shrink:0" loading="lazy" onerror="this.style.display='none'">
      <div>
        <div style="font-size:.72rem;color:var(--text3);margin-bottom:4px"><?= date('d M Y', strtotime($c['created_at'])) ?></div>
        <div style="font-size:.86rem;color:var(--text);line-height:1.45"><?= e($c['comment']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Bottom Nav -->
<nav class="bottom-nav">
  <a href="index.php">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
    Home
  </a>
  <a href="#" onclick="openPrebookFromProfile()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
    Book
  </a>
  <a href="profile.php" class="active">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    Profile
  </a>
</nav>

<!-- Pre-booking Popup (same as homepage) -->
<div class="popup-overlay" id="prebookPopup">
  <div class="popup-card">
    <button class="popup-close" onclick="document.getElementById('prebookPopup').classList.remove('open')">✕</button>
    <div class="popup-badge">Limited Offer — 20% OFF</div>
    <div class="popup-title">Book Your Wedding Film</div>
    <div class="popup-subtitle">Pre-book now and save 20% + lifetime photo access on mkfilms.studio 🏔️</div>
    <div class="popup-form" id="prebookForm">
      <input class="popup-input" id="pb_name" type="text" placeholder="Your name *">
      <input class="popup-input" id="pb_phone" type="tel" placeholder="Phone / WhatsApp *">
      <input class="popup-input" id="pb_email" type="email" placeholder="Email (optional)">
      <input class="popup-input" id="pb_date" type="date">
      <button class="btn-book" onclick="submitPrebook()">Book Now — 20% OFF 🎉</button>
    </div>
    <div id="prebookSuccess" class="hidden" style="text-align:center;padding:10px 0">
      <div style="font-size:2rem">🎊</div>
      <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--accent);margin-top:8px">Booking Confirmed!</div>
      <div style="font-size:.82rem;color:var(--text2);margin-top:6px">We'll call you soon!</div>
    </div>
  </div>
</div>

<script>
const BASE = '<?= SITE_URL ?>';

function switchTab(tab, el) {
  document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tab-liked').classList.toggle('hidden', tab !== 'liked');
  document.getElementById('tab-comments').classList.toggle('hidden', tab !== 'comments');
}

function openPhotoModal(id) {
  window.location.href = `${BASE}/index.php#photo-${id}`;
}

function openPrebookFromProfile() {
  document.getElementById('prebookPopup').classList.add('open');
}

function submitPrebook() {
  const name  = document.getElementById('pb_name').value.trim();
  const phone = document.getElementById('pb_phone').value.trim();
  if (!name || !phone) return;
  const data = new URLSearchParams({ action:'prebook', name, phone,
    email: document.getElementById('pb_email').value,
    wedding_date: document.getElementById('pb_date').value });
  fetch(`${BASE}/api/action.php`, { method:'POST', body:data })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'ok') {
        document.getElementById('prebookForm').classList.add('hidden');
        document.getElementById('prebookSuccess').classList.remove('hidden');
      }
    });
}
</script>
</body>
</html>
