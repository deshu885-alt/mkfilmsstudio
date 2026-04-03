<?php
require_once 'includes/config.php';
$user = currentUser();

// Fetch top photos from DB (sorted by like_count, 2 per wedding minimum shown)
$stmt = $pdo->query("
    SELECT p.*, w.couple_names, w.title as wedding_title
    FROM photos p
    JOIN weddings w ON p.wedding_id = w.id
    WHERE w.is_published = 1
    ORDER BY p.like_count DESC, p.id ASC
    LIMIT 50
");
$photos = $stmt->fetchAll();

// Fetch reels
$reels = $pdo->query("
    SELECT r.*, w.couple_names FROM reels r
    JOIN weddings w ON r.wedding_id = w.id
    LIMIT 3
")->fetchAll();

// Split photos into 2 columns (alternate)
$col1 = []; $col2 = [];
foreach ($photos as $i => $photo) {
    if ($i % 2 === 0) $col1[] = $photo;
    else              $col2[] = $photo;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MK Films — Wedding Cinematography · Mandi, Himachal Pradesh</title>
<meta name="description" content="Award-winning wedding films from the mountains of Himachal Pradesh. Every story told with heart.">
<link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<!-- ═══ NAV ═══ -->
<nav class="nav">
  <div class="nav-logo">
    MK Films
    <span>Mandi, Himachal Pradesh</span>
  </div>
  <div class="nav-right">
    <?php if ($user): ?>
      <a href="profile.php" class="avatar-btn" title="<?= e($user['name']) ?>">
        <img src="<?= e($user['avatar']) ?>" alt="<?= e($user['name']) ?>" referrerpolicy="no-referrer">
      </a>
    <?php else: ?>
      <button class="btn-google" onclick="openAuthModal()">
        <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
        Sign in
      </button>
    <?php endif; ?>
  </div>
</nav>

<!-- ═══ REELS ROW ═══ -->
<?php if ($reels): ?>
<section class="reels-section">
  <h3>🎬 Wedding Films</h3>
  <div class="reels-scroll">
    <?php foreach ($reels as $reel): ?>
    <div class="reel-card" onclick="openReel('<?= e($reel['filename']) ?>', '<?= e($reel['title']) ?>')">
      <div class="reel-card-thumb">
        <?php if ($reel['thumbnail']): ?>
          <img src="uploads/reels/<?= e($reel['thumbnail']) ?>" alt="<?= e($reel['title']) ?>" loading="lazy">
        <?php else: ?>
          <video src="uploads/reels/<?= e($reel['filename']) ?>" muted preload="none"></video>
        <?php endif; ?>
        <div class="reel-play-icon">
          <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
      </div>
      <div class="reel-title"><?= e($reel['title']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ═══ FEED HEADER ═══ -->
<div class="feed-header">
  <div>
    <div class="feed-header h2" style="font-family:var(--font-display);font-style:italic;font-size:.85rem;color:var(--text3);margin-bottom:2px;">✨ Top moments from</div>
    <div class="wedding-title">Arjun &amp; Priya</div>
  </div>
</div>

<!-- ═══ MASONRY FEED ═══ -->
<div class="masonry-grid">
  <!-- Column 1 -->
  <div class="masonry-col">
    <?php foreach ($col1 as $photo): ?>
    <div class="photo-card" onclick="openPhoto(<?= $photo['id'] ?>)"
         data-id="<?= $photo['id'] ?>"
         data-caption="<?= e($photo['caption']) ?>"
         data-likes="<?= (int)$photo['like_count'] ?>">
      <img src="uploads/photos/<?= e($photo['filename']) ?>"
           alt="<?= e($photo['caption']) ?>"
           loading="lazy"
           onerror="this.src='assets/images/placeholder.jpg'">
      <div class="photo-card-overlay">
        <div class="photo-card-caption"><?= e($photo['caption']) ?></div>
        <button class="like-btn <?= likedClass($photo['id'], $user, $pdo) ?>"
                onclick="handleLike(event, <?= $photo['id'] ?>)">
          <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" fill="none">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
          <span class="like-count"><?= $photo['like_count'] ?></span>
        </button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Column 2 -->
  <div class="masonry-col">
    <?php foreach ($col2 as $photo): ?>
    <div class="photo-card" onclick="openPhoto(<?= $photo['id'] ?>)"
         data-id="<?= $photo['id'] ?>"
         data-caption="<?= e($photo['caption']) ?>"
         data-likes="<?= (int)$photo['like_count'] ?>">
      <img src="uploads/photos/<?= e($photo['filename']) ?>"
           alt="<?= e($photo['caption']) ?>"
           loading="lazy"
           onerror="this.src='assets/images/placeholder.jpg'">
      <div class="photo-card-overlay">
        <div class="photo-card-caption"><?= e($photo['caption']) ?></div>
        <button class="like-btn <?= likedClass($photo['id'], $user, $pdo) ?>"
                onclick="handleLike(event, <?= $photo['id'] ?>)">
          <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" fill="none">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
          <span class="like-count"><?= $photo['like_count'] ?></span>
        </button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ═══ PHOTO DETAIL MODAL ═══ -->
<div class="modal-overlay" id="photoModal" onclick="closeModalOnBg(event)">
  <div class="photo-modal" id="photoModalContent">
    <div class="modal-drag-handle"></div>
    <div class="modal-photo" id="modalPhoto"></div>
    <div class="modal-actions">
      <button class="modal-like-btn" id="modalLikeBtn" onclick="handleModalLike()">
        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" fill="none">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span id="modalLikeCount">0</span>
      </button>
      <button class="modal-like-btn" onclick="sharePhoto()" style="gap:7px">
        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" fill="none" width="17" height="17">
          <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8M16 6l-4-4-4 4M12 2v13"/>
        </svg>
        Share
      </button>
    </div>
    <div class="modal-caption" id="modalCaption"></div>
    <div class="comments-section">
      <h4>Comments</h4>
      <div id="commentsList"></div>
      <div class="comment-input-row" id="commentInputRow">
        <?php if ($user): ?>
          <input class="comment-input" id="commentInput" type="text" placeholder="Add a comment..." maxlength="300">
          <button class="comment-submit" onclick="submitComment()">Post</button>
        <?php else: ?>
          <button class="btn-google" style="width:100%;justify-content:center" onclick="openAuthModal()">
            <svg viewBox="0 0 24 24" width="16" height="16"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84z"/></svg>
            Sign in to comment
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ═══ VIDEO REEL MODAL ═══ -->
<div class="modal-overlay" id="reelModal" onclick="closeReelModal(event)">
  <div class="photo-modal" style="max-height:96vh">
    <div class="modal-drag-handle"></div>
    <div style="padding:8px">
      <video id="reelVideo" controls style="border-radius:12px;width:100%;background:#000;max-height:75vh"></video>
      <div style="padding:12px 8px 20px;font-size:.88rem;color:var(--text2)" id="reelTitle"></div>
    </div>
  </div>
</div>

<!-- ═══ AUTH MODAL ═══ -->
<div class="auth-modal-overlay" id="authModal">
  <div class="auth-modal">
    <div class="auth-modal-logo">MK Films</div>
    <h3>Sign in to continue</h3>
    <p>Like & comment on wedding photos.<br>It's free and takes 2 seconds.</p>
    <a href="auth/google-login.php" class="btn-google-big">
      <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84z"/></svg>
      Continue with Google
    </a>
    <button class="auth-modal-close" onclick="closeAuthModal()">Maybe later</button>
  </div>
</div>

<!-- ═══ PRE-BOOKING POPUP ═══ -->
<div class="popup-overlay" id="prebookPopup">
  <div class="popup-card">
    <button class="popup-close" onclick="closePopup()">✕</button>
    <div class="popup-badge">Limited Offer — 20% OFF</div>
    <div class="popup-title">Book Your Wedding Film</div>
    <div class="popup-subtitle">Pre-book now and get 20% off + lifetime access to all your photos & reels on mkfilms.studio 🏔️</div>
    <div class="popup-perks">
      <div class="popup-perk"><div class="popup-perk-icon">🎬</div> Cinematic wedding films</div>
      <div class="popup-perk"><div class="popup-perk-icon">📸</div> 50+ edited photos</div>
      <div class="popup-perk"><div class="popup-perk-icon">♾️</div> Lifetime online access</div>
      <div class="popup-perk"><div class="popup-perk-icon">🏔️</div> Mandi, Himachal Pradesh</div>
    </div>
    <div class="popup-form" id="prebookForm">
      <input class="popup-input" id="pb_name" type="text" placeholder="Your name *" required>
      <input class="popup-input" id="pb_phone" type="tel" placeholder="Phone / WhatsApp *" required>
      <input class="popup-input" id="pb_email" type="email" placeholder="Email (optional)">
      <input class="popup-input" id="pb_date" type="date" placeholder="Wedding date">
      <button class="btn-book" onclick="submitPrebook()">Book Now — 20% OFF 🎉</button>
    </div>
    <div id="prebookSuccess" class="hidden" style="text-align:center;padding:10px 0">
      <div style="font-size:2rem;margin-bottom:8px">🎊</div>
      <div style="font-family:var(--font-display);font-size:1.3rem;color:var(--accent)">You're on the list!</div>
      <div style="font-size:.82rem;color:var(--text2);margin-top:6px">We'll call you soon to discuss your special day.</div>
    </div>
    <div class="popup-footer">No spam. We'll only call to confirm your booking.</div>
  </div>
</div>

<!-- ═══ BOTTOM NAV ═══ -->
<nav class="bottom-nav">
  <a href="index.php" class="active">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
    Home
  </a>
  <a href="#" onclick="openPopup();return false">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
    Book
  </a>
  <?php if ($user): ?>
  <a href="profile.php">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    Profile
  </a>
  <?php else: ?>
  <a href="#" onclick="openAuthModal();return false">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    Sign In
  </a>
  <?php endif; ?>
</nav>

<script>
const BASE = '<?= SITE_URL ?>';
const IS_LOGGED_IN = <?= $user ? 'true' : 'false' ?>;
let currentPhotoId = null;

// ── Photo Modal ──────────────────────────────────────────────
function openPhoto(photoId) {
  currentPhotoId = photoId;
  const card = document.querySelector(`[data-id="${photoId}"]`);
  const img = card.querySelector('img').cloneNode();
  img.style.cssText = 'width:100%;max-height:55vh;object-fit:contain;background:#000;';

  document.getElementById('modalPhoto').innerHTML = '';
  document.getElementById('modalPhoto').appendChild(img);
  document.getElementById('modalCaption').textContent = card.dataset.caption;
  document.getElementById('modalLikeCount').textContent = card.dataset.likes;

  // Like state
  const cardBtn = card.querySelector('.like-btn');
  const modalBtn = document.getElementById('modalLikeBtn');
  if (cardBtn.classList.contains('liked')) modalBtn.classList.add('liked');
  else modalBtn.classList.remove('liked');

  // Load comments
  loadComments(photoId);
  document.getElementById('photoModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModalOnBg(e) {
  if (e.target === document.getElementById('photoModal')) closePhotoModal();
}
function closePhotoModal() {
  document.getElementById('photoModal').classList.remove('open');
  document.body.style.overflow = '';
}

// ── Likes ────────────────────────────────────────────────────
function handleLike(e, photoId) {
  e.stopPropagation();
  if (!IS_LOGGED_IN) { openAuthModal(); return; }
  toggleLike(photoId);
}

function handleModalLike() {
  if (!IS_LOGGED_IN) { openAuthModal(); return; }
  toggleLike(currentPhotoId);
}

function toggleLike(photoId) {
  fetch(`${BASE}/api/action.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=like&photo_id=${photoId}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.status === 'auth_required') { openAuthModal(); return; }
    // Update card
    const card = document.querySelector(`[data-id="${photoId}"]`);
    const btn  = card?.querySelector('.like-btn');
    if (btn) {
      btn.classList.toggle('liked', data.liked);
      btn.querySelector('.like-count').textContent = data.count;
      card.dataset.likes = data.count;
    }
    // Update modal
    if (currentPhotoId == photoId) {
      document.getElementById('modalLikeBtn').classList.toggle('liked', data.liked);
      document.getElementById('modalLikeCount').textContent = data.count;
    }
    showToast(data.liked ? '❤️ Liked!' : 'Unliked');
  });
}

// ── Comments ─────────────────────────────────────────────────
function loadComments(photoId) {
  fetch(`${BASE}/api/action.php?action=get_comments&photo_id=${photoId}`)
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('commentsList');
      if (!data.comments?.length) {
        list.innerHTML = '<div style="font-size:.8rem;color:var(--text3);margin-bottom:8px">No comments yet. Be first! 💬</div>';
        return;
      }
      list.innerHTML = data.comments.map(c => `
        <div class="comment-item">
          <div class="comment-avatar"><img src="${c.avatar}" referrerpolicy="no-referrer" onerror="this.style.display='none'"></div>
          <div class="comment-body">
            <div class="comment-name">${c.name}</div>
            <div class="comment-text">${c.comment}</div>
          </div>
        </div>
      `).join('');
    });
}

function submitComment() {
  const input = document.getElementById('commentInput');
  const text = input.value.trim();
  if (!text) return;

  fetch(`${BASE}/api/action.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=comment&photo_id=${currentPhotoId}&comment=${encodeURIComponent(text)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.status === 'ok') {
      input.value = '';
      loadComments(currentPhotoId);
      showToast('Comment posted ✓');
    }
  });
}

// ── Reels ────────────────────────────────────────────────────
function openReel(filename, title) {
  document.getElementById('reelVideo').src = `${BASE}/uploads/reels/${filename}`;
  document.getElementById('reelTitle').textContent = title;
  document.getElementById('reelModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeReelModal(e) {
  if (e.target === document.getElementById('reelModal')) {
    document.getElementById('reelVideo').pause();
    document.getElementById('reelModal').classList.remove('open');
    document.body.style.overflow = '';
  }
}

// ── Auth Modal ───────────────────────────────────────────────
function openAuthModal() {
  document.getElementById('authModal').classList.add('open');
}
function closeAuthModal() {
  document.getElementById('authModal').classList.remove('open');
}

// ── Pre-booking Popup ────────────────────────────────────────
let popupShown = false;
function openPopup() {
  document.getElementById('prebookPopup').classList.add('open');
}
function closePopup() {
  document.getElementById('prebookPopup').classList.remove('open');
  popupShown = true;
  // Reset timer for next 5 min
  setTimeout(() => { popupShown = false; }, 5 * 60 * 1000);
}

// Show every 5 minutes
function schedulePopup() {
  setTimeout(() => {
    if (!popupShown) { openPopup(); popupShown = true; }
    setTimeout(function loop() {
      popupShown = false;
      openPopup();
      setTimeout(loop, 5 * 60 * 1000);
    }, 5 * 60 * 1000);
  }, 5 * 60 * 1000); // first show after 5 min
}
schedulePopup();

function submitPrebook() {
  const name  = document.getElementById('pb_name').value.trim();
  const phone = document.getElementById('pb_phone').value.trim();
  if (!name || !phone) { showToast('Please enter name & phone'); return; }

  const data = new URLSearchParams({
    action: 'prebook',
    name, phone,
    email: document.getElementById('pb_email').value,
    wedding_date: document.getElementById('pb_date').value,
  });

  fetch(`${BASE}/api/action.php`, { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'ok') {
        document.getElementById('prebookForm').classList.add('hidden');
        document.getElementById('prebookSuccess').classList.remove('hidden');
      }
    });
}

// ── Share ────────────────────────────────────────────────────
function sharePhoto() {
  if (navigator.share) {
    navigator.share({ title: 'MK Films', text: 'Check out this moment!', url: window.location.href });
  } else {
    navigator.clipboard?.writeText(window.location.href);
    showToast('Link copied!');
  }
}

// ── Toast ────────────────────────────────────────────────────
function showToast(msg) {
  const old = document.querySelector('.toast');
  if (old) old.remove();
  const t = document.createElement('div');
  t.className = 'toast'; t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2200);
}

// Swipe down to close modal
let startY = 0;
document.getElementById('photoModalContent').addEventListener('touchstart', e => { startY = e.touches[0].clientY; });
document.getElementById('photoModalContent').addEventListener('touchmove', e => {
  if (e.touches[0].clientY - startY > 70) closePhotoModal();
});
</script>

<?php
// Helper: check if current user liked this photo
function likedClass($photoId, $user, $pdo) {
    if (!$user) return '';
    static $likedIds = null;
    if ($likedIds === null) {
        $stmt = $pdo->prepare("SELECT photo_id FROM photo_likes WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $likedIds = array_column($stmt->fetchAll(), 'photo_id');
    }
    return in_array($photoId, $likedIds) ? 'liked' : '';
}
?>
</body>
</html>
