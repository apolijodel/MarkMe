<?php
// Copy this file to config.php and fill in real values for your environment.
// config.php is gitignored so real credentials never get committed.

define('DB_HOST', 'localhost');
define('DB_NAME', 'markme_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Base URL of the app as served by XAMPP, no trailing slash.
define('BASE_URL', 'http://localhost/markme');

// Upload constraints
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_IMAGE_MIME', ['image/jpeg', 'image/png', 'image/webp']);
