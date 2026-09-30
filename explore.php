<?php
/**
 * Campus Skill Exchange - Explore Skills Page
 */

$page_title = "Explore Skills | Campus Skill Exchange";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/includes/header.php';

// 1. Read Filter & Search Parameters from GET
$searchQuery  = trim($_GET['q'] ?? '');
$category     = trim($_GET['cat'] ?? '');
$typeFilter   = trim($_GET['type'] ?? 'all'); // 'all', 'teach', 'learn'

// Categories list for filtering
$availableCategories = [
    'Programming',
    'Engineering',
    'Design',
    'Mathematics',
    'Academic',
    'Communication',
    'Languages',
    'Science'
];

try {
    // 2. Build Dynamic SQL Query with Prepared Statement Placeholders
    $conditions = ["s.status = 'active'"];
    $params     = [];

    if (!empty($searchQuery)) {
        // Use separate named parameters to prevent SQLSTATE[HY093] parameter number errors
        $conditions[] = "(s.title LIKE :search_title OR s.description LIKE :search_desc)";
        $params[':search_title'] = '%' . $searchQuery . '%';
        $params[':search_desc']  = '%' . $searchQuery . '%';
    }

    if (!empty($category)) {
        $conditions[] = "s.category = :category";
        $params[':category'] = $category;
    }

    if ($typeFilter === 'teach' || $typeFilter === 'learn') {
        $conditions[] = "s.type = :type";
        $params[':type'] = $typeFilter;
    }

    $sql = "
        SELECT s.*, u.full_name, u.department, u.student_id 
        FROM skills s 
        JOIN users u ON s.user_id = u.id 
        WHERE " . implode(' AND ', $conditions) . " 
        ORDER BY s.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $listings = $stmt->fetchAll();

    // Load review summaries/details for the visible listings
    $reviewsBySkill = [];
    if (!empty($listings)) {
        $skillIds = array_column($listings, 'id');
        $placeholders = implode(',', array_fill(0, count($skillIds), '?'));
        $reviewStmt = $pdo->prepare("
            SELECT r.skill_id, r.rating, r.comment, r.created_at, u.full_name AS reviewer_name
            FROM reviews r
            JOIN users u ON r.reviewer_id = u.id
            WHERE r.skill_id IN ($placeholders)
            ORDER BY r.created_at DESC
        " );
        $reviewStmt->execute($skillIds);
        foreach ($reviewStmt->fetchAll() as $review) {
            $reviewsBySkill[$review['skill_id']][] = $review;
        }
    }

    foreach ($listings as &$listing) {
        $listingReviews = $reviewsBySkill[$listing['id']] ?? [];
        $listing['reviews'] = $listingReviews;
        $listing['review_count'] = count($listingReviews);
        $listing['rating_avg'] = $listing['review_count']
            ? round(array_sum(array_column($listingReviews, 'rating')) / $listing['review_count'], 1)
            : 0;
    }
    unset($listing);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error fetching listings: " . htmlspecialchars($e->getMessage()) . "</div>";
    $listings = [];
}
?>

<div class="section-header" style="margin-bottom: 24px;">
    <div>
        <h1 class="section-title">Explore Campus Skills</h1>
        <p class="section-subtitle">Discover peer tutoring and learning requests across all departments</p>
    </div>
    <?php if (is_logged_in()): ?>
        <a href="skill-form.php" class="btn btn-primary">+ Post a Skill</a>
    <?php endif; ?>
</div>

<!-- Search & Filtering Panel -->
<div class="filter-panel">
    <form action="explore.php" method="GET" class="filter-form-grid" id="exploreFilterForm">
        <!-- Keyword Search -->
        <div class="form-group">
            <label for="searchQuery">Search Keyword</label>
            <input 
                type="text" 
                id="searchQuery" 
                name="q" 
                class="form-control" 
                placeholder="Search title or description..." 
                value="<?php echo htmlspecialchars($searchQuery); ?>"
            >
        </div>

        <!-- Category Dropdown -->
        <div class="form-group">
            <label for="catFilter">Category</label>
            <select id="catFilter" name="cat" class="form-control">
                <option value="">All Categories</option>
                <?php foreach ($availableCategories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Type Segmented Control -->
        <div class="form-group">
            <label>Listing Type</label>
            <div class="segmented-control">
                <a href="explore.php?<?php echo http_build_query(array_merge($_GET, ['type' => 'all'])); ?>" 
                   class="<?php echo ($typeFilter === 'all' || empty($typeFilter)) ? 'active' : ''; ?>">
                    All
                </a>
                <a href="explore.php?<?php echo http_build_query(array_merge($_GET, ['type' => 'teach'])); ?>" 
                   class="<?php echo ($typeFilter === 'teach') ? 'active' : ''; ?>">
                    Can Teach
                </a>
                <a href="explore.php?<?php echo http_build_query(array_merge($_GET, ['type' => 'learn'])); ?>" 
                   class="<?php echo ($typeFilter === 'learn') ? 'active' : ''; ?>">
                    Want to Learn
                </a>
            </div>
            <!-- Hidden input to maintain type if submitting form via button -->
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($typeFilter); ?>">
        </div>

        <!-- Filter Actions -->
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
            <?php if (!empty($searchQuery) || !empty($category) || $typeFilter !== 'all'): ?>
                <a href="explore.php" class="btn btn-outline" style="height: 42px;" title="Reset all filters">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Results Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <p style="color: var(--text-muted); font-size: 0.9rem;">
        Showing <strong><?php echo count($listings); ?></strong> listing<?php echo count($listings) === 1 ? '' : 's'; ?>
        <?php if (!empty($searchQuery)): ?>
            for query "<em><?php echo htmlspecialchars($searchQuery); ?></em>"
        <?php endif; ?>
        <?php if (!empty($category)): ?>
            in <strong><?php echo htmlspecialchars($category); ?></strong>
        <?php endif; ?>
    </p>

    <?php if ($typeFilter === 'teach'): ?>
        <span class="badge badge-teach">Showing: Can Teach (Offers)</span>
    <?php elseif ($typeFilter === 'learn'): ?>
        <span class="badge badge-learn">Showing: Want to Learn (Requests)</span>
    <?php endif; ?>
</div>

<!-- Listings Grid -->
<?php if (empty($listings)): ?>
    <div class="empty-state">
        <div class="empty-icon">🔎</div>
        <h3 class="empty-title">No listings found</h3>
        <p class="empty-desc">
            We couldn't find any skill exchanges matching your current filters. Try changing your search query or reset the filters.
        </p>
        <a href="explore.php" class="btn btn-primary btn-sm">Clear All Filters</a>
    </div>
<?php else: ?>
    <div class="skills-grid">
        <?php foreach ($listings as $item): ?>
            <div class="skill-card">
                <div class="card-top">
                    <div class="card-badges">
                        <?php if ($item['type'] === 'teach'): ?>
                            <span class="badge badge-teach">Can Teach</span>
                        <?php else: ?>
                            <span class="badge badge-learn">Want to Learn</span>
                        <?php endif; ?>
                        <span class="badge badge-category"><?php echo htmlspecialchars($item['category']); ?></span>
                    </div>

                    <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>

                    <?php if ($item['type'] === 'teach'): ?>
                        <div class="review-summary">
                            <?php if ($item['review_count'] > 0): ?>
                                <span class="review-stars">★★★★★</span>
                                <strong><?php echo number_format($item['rating_avg'], 1); ?></strong>
                                <span>(<?php echo $item['review_count']; ?> review<?php echo $item['review_count'] === 1 ? '' : 's'; ?>)</span>
                            <?php else: ?>
                                <span class="review-empty">No reviews yet</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

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
                            "id"          => (int)$item["id"],
                            "owner_id"    => (int)$item["user_id"],
                            "title"       => $item["title"],
                            "type"        => $item["type"],
                            "category"    => $item["category"],
                            "description" => $item["description"],
                            "availability"=> $item["availability"],
                            "contact"     => $item["contact_info"],
                            "author"      => $item["full_name"],
                            "department"  => $item["department"],
                            "date"        => format_date($item["created_at"]),
                            "reviews"     => $item["reviews"],
                            "reviewCount" => (int)$item["review_count"],
                            "ratingAvg"   => (float)$item["rating_avg"],
                            "currentUserId" => (int)current_user_id()
                        ]); ?>)'>
                        View Details
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Detail Modal -->
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

        <div id="modalReviews" class="reviews-section"></div>

        <button type="button" class="btn btn-outline btn-block" onclick="closeSkillModal()">Close Details</button>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
