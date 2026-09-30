<?php
/**
 * Global Header & Navigation
 * Reference: Semantic HTML Header & Nav
 */
require_once __DIR__ . '/functions.php';

// Determine active page name for highlighting active nav links
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($page_title)) {
    $page_title = 'Campus Skill Exchange';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <!-- Modern academic stylesheet -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Favicon indicator -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
</head>
<body>

<header class="site-header">
    <div class="nav-container">
        <a href="<?php echo is_logged_in() ? 'index.php' : 'auth.php'; ?>" class="brand-logo">
            <span class="logo-icon">🎓</span>
            <div class="brand-text">
                <span class="brand-title">Campus Skill Exchange</span>
                <span class="brand-subtitle">Share what you know. Learn what you need.</span>
            </div>
        </a>

        <?php if (is_logged_in()): ?>
            <!-- Mobile Navigation Toggle -->
            <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle Navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="main-nav" id="mainNav">
                <ul class="nav-links">
                    <li>
                        <a href="index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="explore.php" class="<?php echo ($current_page === 'explore.php') ? 'active' : ''; ?>">
                            Explore Skills
                        </a>
                    </li>
                    <li>
                        <a href="skill-form.php" class="<?php echo ($current_page === 'skill-form.php') ? 'active' : ''; ?>">
                            + Post a Skill
                        </a>
                    </li>
                    <li>
                        <a href="dashboard.php" class="<?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                            My Dashboard
                        </a>
                    </li>
                </ul>

                <div class="nav-auth">
                    <div class="user-chip">
                        <span class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'] ?? 'S', 0, 1)); ?></span>
                        <div class="user-meta">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Student'); ?></span>
                            <span class="user-id">ID: <?php echo htmlspecialchars($_SESSION['student_id'] ?? ''); ?></span>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">Log Out</a>
                </div>
            </nav>
        <?php else: ?>
            <div class="nav-auth">
                <a href="auth.php" class="btn btn-outline btn-sm">Sign In</a>
                <a href="auth.php?tab=register" class="btn btn-primary btn-sm">Create Account</a>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="page-content">
    <div class="content-container">
        <?php display_flash(); ?>
