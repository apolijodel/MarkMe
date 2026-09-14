<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/shapes.php');
}
csrf_check();

$shapeId = !empty($_POST['shape_id']) ? (int)$_POST['shape_id'] : null;
$name = trim($_POST['name'] ?? '');

if ($name === '') {
    flash_set('error', 'Shape name is required.');
    redirect('/admin/shapes.php');
}

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    try {
        $imagePath = handle_image_upload($_FILES['image'], __DIR__ . '/../uploads/shapes');
    } catch (RuntimeException $e) {
        flash_set('error', $e->getMessage());
        redirect('/admin/shapes.php');
    }
} elseif (!$shapeId) {
    flash_set('error', 'A shape image is required.');
    redirect('/admin/shapes.php');
}

if ($shapeId) {
    if ($imagePath) {
        db()->prepare('UPDATE shapes SET name = :name, image_path = :img WHERE shape_id = :id')
            ->execute(['name' => $name, 'img' => $imagePath, 'id' => $shapeId]);
    } else {
        db()->prepare('UPDATE shapes SET name = :name WHERE shape_id = :id')
            ->execute(['name' => $name, 'id' => $shapeId]);
    }
    flash_set('success', 'Shape updated.');
} else {
    db()->prepare('INSERT INTO shapes (name, image_path) VALUES (:name, :img)')
        ->execute(['name' => $name, 'img' => $imagePath]);
    flash_set('success', 'Shape added.');
}

redirect('/admin/shapes.php');
