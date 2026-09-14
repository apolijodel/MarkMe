<?php
require_once __DIR__ . '/functions.php';

function require_login(): void
{
    if (!is_logged_in()) {
        flash_set('error', 'Please log in to continue.');
        redirect('/customer/login.php');
    }
}

function require_admin(): void
{
    if (!is_logged_in() || !is_admin()) {
        http_response_code(403);
        die('Access denied. Administrator login required.');
    }
}

function require_guest(): void
{
    if (is_logged_in()) {
        redirect(is_admin() ? '/admin/dashboard.php' : '/customer/index.php');
    }
}

/**
 * Attempt to authenticate a user. Returns the user row on success, null on failure.
 * Regenerates the session id on success to prevent session fixation.
 */
function attempt_login(string $identifier, string $password): ?array
{
    $stmt = db()->prepare(
        'SELECT * FROM users WHERE (username = :username OR email = :email) AND is_active = 1 LIMIT 1'
    );
    $stmt->execute(['username' => $identifier, 'email' => $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return null;
    }

    session_regenerate_id(true);
    unset($user['password_hash']);
    $_SESSION['user'] = $user;

    return $user;
}

function logout_user(): void
{
    $_SESSION = [];
    session_destroy();
}
