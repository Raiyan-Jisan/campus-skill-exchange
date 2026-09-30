<?php
/**
 * Campus Skill Exchange - Home Page
 */

$page_title = "Campus Skill Exchange | Home";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/includes/header.php';

try {
    // 1. Fetch Platform Statistics
    $totalSkillsCount = $pdo->query("SELECT COUNT(*) FROM skills WHERE status = 'active'")->fetchColumn();
    $teachSkillsCount = $pdo->query("SELECT COUNT(*) FROM skills WHERE status = 'active' AND type = 'teach'")->fetchColumn();
    $learnSkillsCount = $pdo->query("SELECT COUNT(*) FROM skills WHERE status = 'active' AND type = 'learn'")->fetchColumn();
    $studentCount     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // 2. Fetch Recent "Can Teach" listings (Limit 3)
    $teachStmt = $pdo->query("
        SELECT s.*, u.full_name, u.department 
        FROM skills s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.status = 'active' AND s.type = 'teach' 
        ORDER BY s.created_at DESC 
        LIMIT 3
    ");
    $recentTeach = $teachStmt->fetchAll();

    // 3. Fetch Recent "Want to Learn" listings (Limit 3)
    $learnStmt = $pdo->query("
        SELECT s.*, u.full_name, u.department 
        FROM skills s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.status = 'active' AND s.type = 'learn' 
        ORDER BY s.created_at DESC 
        LIMIT 3
    ");
    $recentLearn = $learnStmt->fetchAll();

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Unable to load platform data: " . htmlspecialchars($e->getMessage()) . "</div>";
    $totalSkillsCount = $teachSkillsCount = $learnSkillsCount = $studentCount = 0;
    $recentTeach = $recentLearn = [];
}
?>

<!-- Hero Section -->
<section class="hero-section">
    <span class="hero-tag">Student Skill Sharing Network</span>
    <h1 class="hero-title">Exchange Skills. Teach What You Know. <span>Master What You Need.</span></h1>
    <p class="hero-subtitle">
        Connect directly with fellow university students for peer-to-peer tutoring, collaborative project help, and practical skill sharing.
    </p>

    <!-- Quick Search Form (Redirects to explore.php) -->
    <form action="explore.php" method="GET" class="hero-search-form">
        <input 
            type="text" 
            name="q" 
            class="hero-search-input" 
            placeholder="Search skills (e.g. Python, Figma, Circuits, Calculus)..." 
            required
        >
        <button type="submit" class="btn btn-primary">Find Skills</button>
    </form>

    <!-- Platform Live Metrics -->
    <div class="hero-stats">
        <div class="hero-stat-item">
            <span class="stat-number"><?php echo intval($totalSkillsCount); ?></span>
            <span class="stat-label">Active Listings</span>
        </div>
        <div class="hero-stat-item">
            <span class="stat-number"><?php echo intval($teachSkillsCount); ?></span>
            <span class="stat-label">Skills Offered (Teach)</span>
        </div>
        <div class="hero-stat-item">
            <span class="stat-number"><?php echo intval($learnSkillsCount); ?></span>
            <span class="stat-label">Skills Needed (Learn)</span>
        </div>
        <div class="hero-stat-item">
            <span class="stat-number"><?php echo intval($studentCount); ?></span>
            <span class="stat-label">Registered Students</span>
        </div>
    </div>
</section>

<!-- Recent "Can Teach" Skills -->
<section style="margin-bottom: 40px;">
    <div class="section-header">
        <div>
            <h2 class="section-title">Latest Skills Offered (Can Teach)</h2>
            <p class="section-subtitle">Students offering peer mentorship and practical knowledge</p>
        </div>
        <a href="explore.php?type=teach" class="btn btn-outline btn-sm">View All Offers &rarr;</a>
    </div>

    <?php if (empty($recentTeach)): ?>
        <div class="empty-state">
            <div class="empty-icon">💡</div>
            <h3 class="empty-title">No teaching offers yet</h3>
            <p class="empty-desc">Be the first student to offer a skill to your peers!</p>
            <a href="skill-form.php" class="btn btn-primary btn-sm">Offer a Skill</a>
        </div>
    <?php else: ?>
        <div class="skills-grid">
            <?php foreach ($recentTeach as $item): ?>
                <div class="skill-card">
                    <div class="card-top">
                        <div class="card-badges">
                            <span class="badge badge-teach">Can Teach</span>
                            <span class="badge badge-category"><?php echo htmlspecialchars($item['category']); ?></span>
                        </div>
                        <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                        
                        <div class="card-meta-list">
                            <div class="card-meta-item">
                                <strong>Availability:</strong>
                                <span><?php echo htmlspecialchars($item['availability']); ?></span>
                            </div>
                            <div class="card-meta-item">
                                <strong>Posted:</strong>
                                <span><?php echo format_date($item['created_at']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="card-author">
                            <span class="author-avatar"><?php echo strtoupper(substr($item['full_name'], 0, 1)); ?></span>
                            <div class="author-info">
                                <div class="author-name"><?php echo htmlspecialchars($item['full_name']); ?></div>
                                <div class="author-dept"><?php echo htmlspecialchars($item['department']); ?></div>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            class="btn btn-outline btn-sm"
                            onclick='openSkillModal(<?php echo json_encode([
                                "title"       => $item["title"],
                                "type"        => $item["type"],
                                "category"    => $item["category"],
                                "description" => $item["description"],
                                "availability"=> $item["availability"],
                                "contact"     => $item["contact_info"],
                                "author"      => $item["full_name"],
                                "department"  => $item["department"],
                                "date"        => format_date($item["created_at"]),
                                "id"          => (int)$item["id"],
                                "owner_id"    => (int)$item["user_id"],
                                "reviews"     => $item["reviews"] ?? [],
                                "reviewCount" => (int)($item["review_count"] ?? 0),
                                "ratingAvg"   => (float)($item["rating_avg"] ?? 0),
                                "currentUserId" => (int)current_user_id()
                            ]); ?>)'>
                            Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Recent "Want to Learn" Requests -->
<section style="margin-bottom: 40px;">
    <div class="section-header">
        <div>
            <h2 class="section-title">Latest Skills Requested (Want to Learn)</h2>
            <p class="section-subtitle">Students seeking study partners and peer guidance</p>
        </div>
        <a href="explore.php?type=learn" class="btn btn-outline btn-sm">View All Requests &rarr;</a>
    </div>

    <?php if (empty($recentLearn)): ?>
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <h3 class="empty-title">No learning requests yet</h3>
            <p class="empty-desc">Need help with a topic? Post a request to find a peer tutor.</p>
            <a href="skill-form.php" class="btn btn-primary btn-sm">Request Help</a>
        </div>
    <?php else: ?>
        <div class="skills-grid">
            <?php foreach ($recentLearn as $item): ?>
                <div class="skill-card">
                    <div class="card-top">
                        <div class="card-badges">
                            <span class="badge badge-learn">Want to Learn</span>
                            <span class="badge badge-category"><?php echo htmlspecialchars($item['category']); ?></span>
                        </div>
                        <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                        
                        <div class="card-meta-list">
                            <div class="card-meta-item">
                                <strong>Availability:</strong>
                                <span><?php echo htmlspecialchars($item['availability']); ?></span>
                            </div>
                            <div class="card-meta-item">
                                <strong>Posted:</strong>
                                <span><?php echo format_date($item['created_at']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="card-author">
                            <span class="author-avatar"><?php echo strtoupper(substr($item['full_name'], 0, 1)); ?></span>
                            <div class="author-info">
                                <div class="author-name"><?php echo htmlspecialchars($item['full_name']); ?></div>
                                <div class="author-dept"><?php echo htmlspecialchars($item['department']); ?></div>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            class="btn btn-outline btn-sm"
                            onclick='openSkillModal(<?php echo json_encode([
                                "title"       => $item["title"],
                                "type"        => $item["type"],
                                "category"    => $item["category"],
                                "description" => $item["description"],
                                "availability"=> $item["availability"],
                                "contact"     => $item["contact_info"],
                                "author"      => $item["full_name"],
                                "department"  => $item["department"],
                                "date"        => format_date($item["created_at"]),
                                "id"          => (int)$item["id"],
                                "owner_id"    => (int)$item["user_id"],
                                "reviews"     => $item["reviews"] ?? [],
                                "reviewCount" => (int)($item["review_count"] ?? 0),
                                "ratingAvg"   => (float)($item["rating_avg"] ?? 0),
                                "currentUserId" => (int)current_user_id()
                            ]); ?>)'>
                            Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Reusable Skill Detail Modal -->
<div class="modal-overlay" id="skillDetailModal">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <span class="badge" id="modalTypeBadge">Type</span>
                <span class="badge badge-category" id="modalCategory" style="margin-left: 6px;">Category</span>
            </div>
            <button type="button" class="modal-close" onclick="closeSkillModal()">&times;</button>
        </div>
        <h2 class="modal-title" id="modalTitle">Title</h2>
        
        <p id="modalDescription" style="margin: 16px 0; color: #374151; line-height: 1.6; white-space: pre-line;"></p>
        
        <div class="card-meta-list" style="margin-bottom: 20px;">
            <div class="card-meta-item">
                <strong>Student:</strong>
                <span id="modalAuthor">Author</span> (<span id="modalDepartment">Department</span>)
            </div>
            <div class="card-meta-item">
                <strong>Schedule / Availability:</strong>
                <span id="modalAvailability">Time</span>
            </div>
            <div class="card-meta-item">
                <strong>Listing Date:</strong>
                <span id="modalDate">Date</span>
            </div>
        </div>

        <div id="modalContactBox" class="alert alert-info" style="margin-bottom: 20px;"></div>

        <button type="button" class="btn btn-outline btn-block" onclick="closeSkillModal()">Close Details</button>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
