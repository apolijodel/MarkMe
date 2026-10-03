<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Validate and move an uploaded image into $targetDir.
 * Returns the stored filename on success, or throws RuntimeException with a
 * user-facing message on failure.
 */
function handle_image_upload(array $file, string $targetDir): string
{
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid upload parameters.');
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('No file was uploaded.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed. Please try again.');
    }

    if ($file['size'] > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('Image is too large. Maximum size is 2MB.');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Invalid upload.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXT, true)) {
        throw new RuntimeException('Unsupported file type. Allowed: jpg, jpeg, png, webp.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, ALLOWED_IMAGE_MIME, true)) {
        throw new RuntimeException('File does not appear to be a valid image.');
    }

    if (@getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('File does not appear to be a valid image.');
    }

    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
        throw new RuntimeException('Server error: unable to prepare upload directory.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Server error: unable to save the uploaded file.');
    }

    return $filename;
}

/**
 * Duplicate an already-stored upload under a fresh random filename.
 *
 * Used when a saved design is added to the cart. The cart/order line takes its
 * own copy of the photo rather than pointing at the saved design's file, so the
 * two have independent lifetimes: editing or deleting a saved design can never
 * pull the image out from under an order that already referenced it. This is
 * the same snapshot rule order_items already applies to names and prices.
 *
 * Returns the new filename, or null when the source is missing.
 */
function copy_stored_upload(string $dir, ?string $filename): ?string
{
    if (!$filename) {
        return null;
    }

    $source = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . basename($filename);
    if (!is_file($source)) {
        return null;
    }

    $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXT, true)) {
        return null;
    }

    $copy = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $copy;

    return copy($source, $destination) ? $copy : null;
}
