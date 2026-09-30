<?php
/**
 * Create Skill Listing Action
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../auth.php');
}

$userId       = current_user_id();
$title        = trim($_POST['title'] ?? '');
$type         = trim($_POST['type'] ?? 'teach');
$category     = trim($_POST['category'] ?? '');
$description  = trim($_POST['description'] ?? '');
$availability = trim($_POST['availability'] ?? '');
$contactInfo  = trim($_POST['contact_info'] ?? '');

// 1. Validation
if (empty($title) || empty($category) || empty($description) || empty($availability)) {
    set_flash('danger', 'Please fill in all mandatory listing fields (Title, Category, Description, Availability).');
    redirect('../skill-form.php');
}

if (!in_array($type, ['teach', 'learn'])) {
    $type = 'teach';
}

try {
    // 2. Insert into database using prepared statement
    $stmt = $pdo->prepare("
        INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
        VALUES (:user_id, :title, :type, :category, :description, :availability, :contact_info, 'active')
    ");

    $stmt->execute([
        ':user_id'      => $userId,
        ':title'        => $title,
        ':type'         => $type,
        ':category'     => $category,
        ':description'  => $description,
        ':availability' => $availability,
        ':contact_info' => $contactInfo
    ]);

    set_flash('success', 'Your skill listing has been posted successfully!');
    redirect('../dashboard.php');

} catch (PDOException $e) {
    set_flash('danger', 'Error creating listing: ' . htmlspecialchars($e->getMessage()));
    redirect('../skill-form.php');
}
?>
