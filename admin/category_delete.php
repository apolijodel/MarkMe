<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/categories.php');
}
csrf_check();

$categoryId = (int)($_POST['category_id'] ?? 0);

try {
    db()->prepare('DELETE FROM categories WHERE category_id = :id')->execute(['id' => $categoryId]);
    flash_set('success', 'Category deleted.');
} catch (PDOException $e) {
    // Foreign key constraint (products still reference this category).
    flash_set('error', 'Cannot delete this category while products are still assigned to it.');
}

redirect('/admin/categories.php');
