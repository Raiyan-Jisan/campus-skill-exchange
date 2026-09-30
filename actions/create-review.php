<?php
/**
 * Create a review for a peer teaching listing.
 * Reviews are intended for students who have actually completed a learning session.
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../auth.php');
}

$userId = current_user_id();
$skillId = intval($_POST['skill_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$returnUrl = '../explore.php';

if ($skillId <= 0 || $rating < 1 || $rating > 5 || $comment === '' || strlen($comment) > 500) {
    set_flash('danger', 'Please provide a rating from 1 to 5 and a review of up to 500 characters.');
    redirect($returnUrl);
}

try {
    $stmt = $pdo->prepare("SELECT id, user_id, type, status FROM skills WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $skillId]);
    $skill = $stmt->fetch();

    if (!$skill || $skill['type'] !== 'teach' || $skill['status'] !== 'active') {
        set_flash('danger', 'Reviews are available only for active teaching listings.');
        redirect($returnUrl);
    }

    if ((int)$skill['user_id'] === (int)$userId) {
        set_flash('danger', 'You cannot review your own teaching listing.');
        redirect($returnUrl);
    }

    $reviewStmt = $pdo->prepare("SELECT id FROM reviews WHERE skill_id = :skill_id AND reviewer_id = :reviewer_id LIMIT 1");
    $reviewStmt->execute([':skill_id' => $skillId, ':reviewer_id' => $userId]);
    if ($reviewStmt->fetch()) {
        set_flash('danger', 'You have already reviewed this teaching listing.');
        redirect($returnUrl);
    }

    $insert = $pdo->prepare("INSERT INTO reviews (skill_id, reviewer_id, rating, comment) VALUES (:skill_id, :reviewer_id, :rating, :comment)");
    $insert->execute([
        ':skill_id' => $skillId,
        ':reviewer_id' => $userId,
        ':rating' => $rating,
        ':comment' => $comment
    ]);

    set_flash('success', 'Thanks! Your review has been added.');
} catch (PDOException $e) {
    set_flash('danger', 'Unable to save your review right now.');
}

redirect($returnUrl);
?>
