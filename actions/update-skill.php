<?php
/**
 * Update Skill Listing Action
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../auth.php');
}

$userId       = current_user_id();
$skillId      = intval($_POST['id'] ?? 0);
$title        = trim($_POST['title'] ?? '');
$type         = trim($_POST['type'] ?? 'teach');
$category     = trim($_POST['category'] ?? '');
$description  = trim($_POST['description'] ?? '');
$availability = trim($_POST['availability'] ?? '');
$contactInfo  = trim($_POST['contact_info'] ?? '');
$status       = trim($_POST['status'] ?? 'active');

if ($skillId <= 0) {
    set_flash('danger', 'Invalid listing ID provided.');
    redirect('../dashboard.php');
}

// 1. Validation
if (empty($title) || empty($category) || empty($description) || empty($availability)) {
    set_flash('danger', 'Please complete all required fields.');
    redirect("../skill-form.php?id={$skillId}");
}

if (!in_array($type, ['teach', 'learn'])) {
    $type = 'teach';
}

if (!in_array($status, ['active', 'closed'])) {
    $status = 'active';
}

try {
    // 2. Strict Authorization Verification: Verify that the listing exists and belongs to this user
    $verifyStmt = $pdo->prepare("SELECT id FROM skills WHERE id = :id AND user_id = :user_id LIMIT 1");
    $verifyStmt->execute([
        ':id'      => $skillId,
        ':user_id' => $userId
    ]);

    if (!$verifyStmt->fetch()) {
        set_flash('danger', 'Unauthorized: You do not have permission to edit this listing.');
        redirect('../dashboard.php');
    }

    // 3. Perform update with prepared statement
    $updateStmt = $pdo->prepare("
        UPDATE skills 
        SET title = :title, 
            type = :type, 
            category = :category, 
            description = :description, 
            availability = :availability, 
            contact_info = :contact_info,
            status = :status
        WHERE id = :id AND user_id = :user_id
    ");

    $updateStmt->execute([
        ':title'        => $title,
        ':type'         => $type,
        ':category'     => $category,
        ':description'  => $description,
        ':availability' => $availability,
        ':contact_info' => $contactInfo,
        ':status'       => $status,
        ':id'           => $skillId,
        ':user_id'      => $userId
    ]);

    set_flash('success', 'Your listing was updated successfully.');
    redirect('../dashboard.php');

} catch (PDOException $e) {
    set_flash('danger', 'Error updating listing: ' . htmlspecialchars($e->getMessage()));
    redirect("../skill-form.php?id={$skillId}");
}
?>
