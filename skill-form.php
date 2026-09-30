<?php
/**
 * Campus Skill Exchange - Skill Form (Create & Edit Mode)
 */

$page_title = "Skill Listing Form | Campus Skill Exchange";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php'; // Guard: must be logged in

$userId     = current_user_id();
$skillId    = isset($_GET['id']) ? intval($_GET['id']) : 0;
$isEditMode = ($skillId > 0);

// Default Form Values
$skill = [
    'id'           => 0,
    'title'        => '',
    'type'         => 'teach',
    'category'     => '',
    'description'  => '',
    'availability' => '',
    'contact_info' => '',
    'status'       => 'active'
];

$categories = [
    'Programming',
    'Engineering',
    'Design',
    'Mathematics',
    'Academic',
    'Communication',
    'Languages',
    'Science'
];

// If Edit Mode, verify ownership and load existing listing
if ($isEditMode) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute([
            ':id'      => $skillId,
            ':user_id' => $userId
        ]);
        $existing = $stmt->fetch();

        if (!$existing) {
            set_flash('danger', 'Unauthorized access: You can only edit your own listings.');
            redirect('dashboard.php');
        }

        $skill = $existing;
        $page_title = "Edit Skill Listing | Campus Skill Exchange";

    } catch (PDOException $e) {
        set_flash('danger', 'Error loading listing: ' . htmlspecialchars($e->getMessage()));
        redirect('dashboard.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-card">
    <div class="form-header">
        <h2><?php echo $isEditMode ? 'Edit Skill Listing' : 'Share or Request a Skill'; ?></h2>
        <p>
            <?php echo $isEditMode 
                ? 'Update your listing details and schedule below.' 
                : 'Connect with peers across campus. Share your knowledge or request assistance.'; 
            ?>
        </p>
    </div>

    <!-- Lab 04 LocalStorage Draft Notice (shown dynamically by validation.js if draft restored) -->
    <div id="draftNotice" class="alert alert-info" style="display: none; padding: 10px 14px; margin-bottom: 20px;">
        <span>📝 <strong>Draft restored:</strong> Loaded unsaved content from your browser local storage.</span>
    </div>

    <form 
        id="skillForm" 
        action="<?php echo $isEditMode ? 'actions/update-skill.php' : 'actions/create-skill.php'; ?>" 
        method="POST"
    >
        <!-- Hidden input for ID in edit mode -->
        <input type="hidden" id="skillId" name="id" value="<?php echo htmlspecialchars($skill['id']); ?>">

        <!-- Listing Type (Radio Group) -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label>What would you like to do? <span style="color: var(--danger);">*</span></label>
            <div class="radio-group">
                <label class="radio-card">
                    <input 
                        type="radio" 
                        name="type" 
                        value="teach" 
                        <?php echo ($skill['type'] === 'teach') ? 'checked' : ''; ?>
                    >
                    <div class="radio-card-content">
                        <span class="radio-card-title">Can Teach (Offer)</span>
                        <span class="radio-card-desc">I want to mentor or teach a skill</span>
                    </div>
                </label>

                <label class="radio-card">
                    <input 
                        type="radio" 
                        name="type" 
                        value="learn" 
                        <?php echo ($skill['type'] === 'learn') ? 'checked' : ''; ?>
                    >
                    <div class="radio-card-content">
                        <span class="radio-card-title">Want to Learn (Request)</span>
                        <span class="radio-card-desc">I am seeking help or study tutoring</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Skill Title -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="title">Skill Title <span style="color: var(--danger);">*</span></label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-control" 
                placeholder="e.g. Python for Data Analysis, Figma UI Design, Circuit Analysis" 
                value="<?php echo htmlspecialchars($skill['title']); ?>" 
                required
            >
            <span class="form-text">Give a clear, concise headline describing the topic or task.</span>
        </div>

        <!-- Category Dropdown -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="category">Academic Category <span style="color: var(--danger);">*</span></label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($skill['category'] === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Description -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="description">Detailed Description <span style="color: var(--danger);">*</span></label>
            <textarea 
                id="description" 
                name="description" 
                class="form-control" 
                rows="5" 
                placeholder="Explain what topics you can cover, what projects you need help with, or your background..." 
                required
            ><?php echo htmlspecialchars($skill['description']); ?></textarea>
            <span class="form-text">Provide sufficient details for other students to understand what you offer or require.</span>
        </div>

        <!-- Availability -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="availability">Availability & Preferred Venue <span style="color: var(--danger);">*</span></label>
            <input 
                type="text" 
                id="availability" 
                name="availability" 
                class="form-control" 
                placeholder="e.g. Sun & Tue after 4 PM (CSE Lab 3) or Zoom" 
                value="<?php echo htmlspecialchars($skill['availability']); ?>" 
                required
            >
        </div>

        <!-- Optional Contact Information -->
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="contact_info">Preferred Contact Info (Optional)</label>
            <input 
                type="text" 
                id="contact_info" 
                name="contact_info" 
                class="form-control" 
                placeholder="e.g. your_email@seu.edu.bd, WhatsApp number, or campus room" 
                value="<?php echo htmlspecialchars($skill['contact_info']); ?>"
            >
            <span class="form-text">If left empty, other students can reach out via your registered campus email.</span>
        </div>

        <!-- Status (in edit mode only) -->
        <?php if ($isEditMode): ?>
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="status">Listing Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active" <?php echo ($skill['status'] === 'active') ? 'selected' : ''; ?>>Active (Visible to all students)</option>
                    <option value="closed" <?php echo ($skill['status'] === 'closed') ? 'selected' : ''; ?>>Closed (Exchange completed / paused)</option>
                </select>
            </div>
        <?php endif; ?>

        <!-- Form Actions -->
        <div style="display: flex; gap: 12px; margin-top: 28px;">
            <button type="submit" class="btn btn-primary" style="flex: 2;">
                <?php echo $isEditMode ? 'Save Changes' : 'Publish Skill Listing'; ?>
            </button>
            <a href="dashboard.php" class="btn btn-outline" style="flex: 1; text-align: center;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
