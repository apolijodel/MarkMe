<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Escape for HTML output. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function format_price(float $amount): string
{
    return '₱' . number_format($amount, 2);
}

function flash_set(string $key, mixed $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): mixed
{
    if (!empty($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/** Current request path relative to BASE_URL, e.g. "/customer/product.php?id=3". */
function current_path(): string
{
    $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    if ($basePath !== '' && str_starts_with($requestUri, $basePath)) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
    return $requestUri === '' ? '/' : $requestUri;
}

/** Only allow redirecting back to a local app path — never an absolute/external URL. */
function safe_local_path(?string $path, string $default = '/customer/index.php'): string
{
    if (!$path || $path[0] !== '/' || str_starts_with($path, '//') || str_contains($path, '\\')) {
        return $default;
    }
    return $path;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        die('Invalid or expired form submission. Please go back and try again.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function is_admin(): bool
{
    return is_logged_in() && $_SESSION['user']['role'] === 'admin';
}

function get_or_create_cart_id(int $userId): int
{
    $stmt = db()->prepare('SELECT cart_id FROM carts WHERE user_id = :id');
    $stmt->execute(['id' => $userId]);
    $cartId = $stmt->fetchColumn();

    if ($cartId === false) {
        db()->prepare('INSERT INTO carts (user_id) VALUES (:id)')->execute(['id' => $userId]);
        $cartId = db()->lastInsertId();
    }

    return (int)$cartId;
}
