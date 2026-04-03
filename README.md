# MK Films — Setup Guide
## mkfilms.studio · Wedding Cinematography · Mandi, HP

---

## 📁 Project Structure

```
mkfilms/
├── index.php              ← Homepage (Pinterest feed)
├── profile.php            ← User profile
├── database.sql           ← Run this first
├── .htaccess
├── includes/
│   └── config.php         ← DB + Google OAuth config
├── auth/
│   ├── google-login.php   ← Redirect to Google
│   ├── google-callback.php← OAuth return handler
│   └── logout.php
├── api/
│   └── action.php         ← Like/Comment/Prebook AJAX
├── assets/
│   └── css/main.css
└── uploads/
    ├── photos/            ← Drop your 50 wedding photos here
    └── reels/             ← Drop your 3 reels here
```

---

## ⚡ Quick Setup (5 steps)

### 1. Database
```sql
-- In phpMyAdmin or MySQL terminal:
source /path/to/database.sql
```

### 2. Config — Edit `includes/config.php`
```php
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('SITE_URL', 'http://localhost/mkfilms');  // change for prod
```

### 3. Google OAuth Setup
1. Go to https://console.cloud.google.com
2. Create a project → APIs & Services → Credentials
3. Create OAuth 2.0 Client ID → Web Application
4. Authorized redirect URI: `http://localhost/mkfilms/auth/google-callback.php`
5. Copy Client ID + Secret into `includes/config.php`

### 4. Upload Media
- Put your 50 photos in `uploads/photos/`
  - Name them: `photo_01.jpg` to `photo_50.jpg`
  - OR update filenames in DB: `UPDATE photos SET filename='yourfile.jpg' WHERE id=1;`
- Put 3 reels in `uploads/reels/`
  - Name: `reel_highlight.mp4`, `reel_haldi.mp4`, `reel_fullday.mp4`
  - Thumbnails (optional): `reel_01_thumb.jpg` etc.

### 5. Place in Web Root
```bash
# Copy to your Apache/PHP web root
cp -r mkfilms/ /var/www/html/
# OR for XAMPP/WAMP: htdocs/mkfilms/
```
Then visit: http://localhost/mkfilms

---

## 🌐 Production (mkfilms.studio)

1. Upload all files via FTP/SFTP to public_html/
2. Update `SITE_URL` in config.php → `https://mkfilms.studio`
3. Update Google OAuth redirect URI in Google Console
4. Enable HTTPS (SSL certificate)

---

## 🔧 Customization

### Update couple name on homepage
Edit `index.php` line with `Arjun &amp; Priya` → your couple's names

### Add more weddings later
```sql
INSERT INTO weddings (title, couple_names, wedding_date, location) 
VALUES ('New Wedding', 'Bride & Groom', '2026-03-15', 'Shimla, HP');
```

### Change popup timing (default: 5 min)
In `index.php`, find `5 * 60 * 1000` and change to desired milliseconds.

---

## 📱 Features Checklist
- [x] Pinterest-style 2-column photo feed
- [x] Top photos by likes shown first
- [x] Heart/Like with Google login gate
- [x] Comments with Google login gate
- [x] Google OAuth only (no passwords)
- [x] Pinterest-style user profile
- [x] Liked photos grid on profile
- [x] 3 Reels horizontal scroll
- [x] Pre-booking popup every 5 min
- [x] 20% discount offer in popup
- [x] Swipe down to close modals
- [x] Dark mode (dark gray, not black)
- [x] Mobile-first design
- [x] Bottom navigation bar

---

## 💡 Tips
- Recommended photo sizes: 800×1200px (portrait) for feed
- Reel format: MP4, H.264, max 50MB each
- Add thumbnails for reels (JPG) for faster loading
