<?php
/**
 * Delete Skill Listing Action
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../auth.php');
}

$userId  = current_user_id();
$skillId = intval($_POST['id'] ?? 0);

if ($skillId <= 0) {
    set_flash('danger', 'Invalid listing ID specified.');
    redirect('../dashboard.php');
}

try {
    // Strict Authorization: User can ONLY delete listings where user_id matches their session ID
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id'      => $skillId,
        ':user_id' => $userId
    ]);

    if ($stmt->rowCount() > 0) {
        set_flash('success', 'Your listing has been successfully deleted.');
    } else {
        set_flash('danger', 'Action failed: You do not have permission to delete this listing or it does not exist.');
    }

} catch (PDOException $e) {
    set_flash('danger', 'Error deleting listing: ' . htmlspecialchars($e->getMessage()));
}

redirect('../dashboard.php');
?>
