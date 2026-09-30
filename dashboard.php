<?php
/**
 * Campus Skill Exchange - Student Dashboard
 */

$page_title = "My Dashboard | Campus Skill Exchange";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php'; // Guard: must be logged in

$userId = current_user_id();

try {
    // 1. Fetch User Record
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $userStmt->execute([':id' => $userId]);
    $user = $userStmt->fetch();

    if (!$user) {
        // Session corrupted or user deleted
        redirect('logout.php');
    }

    // 2. Fetch User's Skill Listings
    $skillsStmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = :user_id ORDER BY created_at DESC");
    $skillsStmt->execute([':user_id' => $userId]);
    $mySkills = $skillsStmt->fetchAll();

    // 3. Compute Metrics
    $totalListings = count($mySkills);
    $teachCount    = 0;
    $learnCount    = 0;

    foreach ($mySkills as $s) {
        if ($s['type'] === 'teach') $teachCount++;
        if ($s['type'] === 'learn') $learnCount++;
    }

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading dashboard: " . htmlspecialchars($e->getMessage()) . "</div>";
    $mySkills = [];
    $totalListings = $teachCount = $learnCount = 0;
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Student Profile Banner -->
<div class="profile-banner">
    <div class="profile-main">
        <div class="profile-avatar-large">
            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
        </div>
        <div class="profile-details">
            <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
            <div class="profile-meta-chips">
                <span>🎓 ID: <strong><?php echo htmlspecialchars($user['student_id']); ?></strong></span>
                <span>🏛️ Department: <strong><?php echo htmlspecialchars($user['department']); ?></strong></span>
                <span>✉️ <?php echo htmlspecialchars($user['email']); ?></span>
            </div>
        </div>
    </div>
    <div>
        <a href="skill-form.php" class="btn btn-primary" style="background-color: white; color: var(--primary);">
            + Post New Skill
        </a>
    </div>
</div>

<!-- Quick Statistics Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-box stat-icon-green">📚</div>
        <div class="stat-info">
            <h3><?php echo $totalListings; ?></h3>
            <p>Total Listings</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box stat-icon-green">💡</div>
        <div class="stat-info">
            <h3><?php echo $teachCount; ?></h3>
            <p>Skills Offered (Teaching)</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box stat-icon-blue">🔍</div>
        <div class="stat-info">
            <h3><?php echo $learnCount; ?></h3>
            <p>Skills Requested (Learning)</p>
        </div>
    </div>
</div>

<!-- My Listings Section -->
<div class="section-header" style="margin-top: 10px;">
    <div>
        <h2 class="section-title">Manage My Skill Listings</h2>
        <p class="section-subtitle">Edit your schedule, update availability, or remove completed exchanges</p>
    </div>
    <a href="explore.php" class="btn btn-outline btn-sm">Browse All Campus Listings</a>
</div>

<?php if (empty($mySkills)): ?>
    <div class="empty-state">
        <div class="empty-icon">📝</div>
        <h3 class="empty-title">You haven't posted any skills yet</h3>
        <p class="empty-desc">
            Offer a skill you excel at, or request assistance for a challenging lab assignment or course topic.
        </p>
        <a href="skill-form.php" class="btn btn-primary btn-sm">+ Create Your First Listing</a>
    </div>
<?php else: ?>
    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Skill Title</th>
                    <th>Category</th>
                    <th>Availability</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mySkills as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['type'] === 'teach'): ?>
                                <span class="badge badge-teach">Can Teach</span>
                            <?php else: ?>
                                <span class="badge badge-learn">Want to Learn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-category"><?php echo htmlspecialchars($item['category']); ?></span>
                        </td>
                        <td>
                            <span style="font-size: 0.84rem; color: var(--text-muted);"><?php echo htmlspecialchars($item['availability']); ?></span>
                        </td>
                        <td>
                            <?php if ($item['status'] === 'active'): ?>
                                <span class="badge badge-status-active">Active</span>
                            <?php else: ?>
                                <span class="badge badge-status-closed">Closed</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="font-size: 0.8rem; color: var(--text-light);"><?php echo format_date($item['created_at']); ?></span>
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: flex-end;">
                                <!-- Edit Link -->
                                <a href="skill-form.php?id=<?php echo intval($item['id']); ?>" class="btn btn-outline btn-sm" title="Edit this listing">
                                    Edit
                                </a>

                                <!-- Delete Form via POST for Security -->
                                <form action="actions/delete-skill.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this listing?');">
                                    <input type="hidden" name="id" value="<?php echo intval($item['id']); ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete listing">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
