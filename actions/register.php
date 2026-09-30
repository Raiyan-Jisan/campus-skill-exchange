<?php
/**
 * Registration Handler
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../auth.php');
}

$fullName        = trim($_POST['full_name'] ?? '');
$studentId       = trim($_POST['student_id'] ?? '');
$email           = trim($_POST['email'] ?? '');
$department      = trim($_POST['department'] ?? '');
$password        = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// 1. Server-side validation
if (empty($fullName) || empty($studentId) || empty($email) || empty($department) || empty($password)) {
    set_flash('danger', 'Please complete all required fields.');
    redirect('../auth.php?tab=register');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Please provide a valid university email address.');
    redirect('../auth.php?tab=register');
}

if (strlen($password) < 6) {
    set_flash('danger', 'Password must be at least 6 characters in length.');
    redirect('../auth.php?tab=register');
}

if ($password !== $confirmPassword) {
    set_flash('danger', 'Password and confirmation password do not match.');
    redirect('../auth.php?tab=register');
}

try {
    // 2. Check for duplicate student ID or email
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE student_id = :student_id OR email = :email LIMIT 1");
    $checkStmt->execute([
        ':student_id' => $studentId,
        ':email'      => $email
    ]);

    if ($checkStmt->fetch()) {
        set_flash('danger', 'An account with this Student ID or Email already exists.');
        redirect('../auth.php?tab=register');
    }

    // 3. Hash password securely
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert user using prepared statement
    $insertStmt = $pdo->prepare("
        INSERT INTO users (student_id, full_name, email, department, password_hash)
        VALUES (:student_id, :full_name, :email, :department, :password_hash)
    ");

    $insertStmt->execute([
        ':student_id'    => $studentId,
        ':full_name'     => $fullName,
        ':email'         => $email,
        ':department'    => $department,
        ':password_hash' => $passwordHash
    ]);

    $newUserId = $pdo->lastInsertId();

    // 5. Establish secure session
    session_regenerate_id(true);
    $_SESSION['user_id']    = $newUserId;
    $_SESSION['student_id'] = $studentId;
    $_SESSION['full_name']  = $fullName;
    $_SESSION['email']      = $email;
    $_SESSION['department'] = $department;

    set_flash('success', "Welcome to Campus Skill Exchange, <strong>" . htmlspecialchars($fullName) . "</strong>! Your account has been registered.");
    redirect('../dashboard.php');

} catch (PDOException $e) {
    set_flash('danger', 'Database error during registration: ' . htmlspecialchars($e->getMessage()));
    redirect('../auth.php?tab=register');
}
?>
