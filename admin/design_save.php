<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/designs.php');
}
csrf_check();

$designId = !empty($_POST['design_id']) ? (int)$_POST['design_id'] : null;
$name = trim($_POST['name'] ?? '');

if ($name === '') {
    flash_set('error', 'Design name is required.');
    redirect('/admin/designs.php');
}

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    try {
        $imagePath = handle_image_upload($_FILES['image'], __DIR__ . '/../uploads/designs');
    } catch (RuntimeException $e) {
        flash_set('error', $e->getMessage());
        redirect('/admin/designs.php');
    }
} elseif (!$designId) {
    flash_set('error', 'A design image is required.');
    redirect('/admin/designs.php');
}

if ($designId) {
    if ($imagePath) {
        db()->prepare('UPDATE designs SET name = :name, image_path = :img WHERE design_id = :id')
            ->execute(['name' => $name, 'img' => $imagePath, 'id' => $designId]);
    } else {
        db()->prepare('UPDATE designs SET name = :name WHERE design_id = :id')
            ->execute(['name' => $name, 'id' => $designId]);
    }
    flash_set('success', 'Design updated.');
} else {
    db()->prepare('INSERT INTO designs (name, image_path) VALUES (:name, :img)')
        ->execute(['name' => $name, 'img' => $imagePath]);
    flash_set('success', 'Design added.');
}

redirect('/admin/designs.php');
