<?php
/**
 * Campus Skill Exchange - Authentication Page (Login & Register)
 * Reference: Client & Server Validation, Safe Auth
 */

$page_title = "Authentication | Campus Skill Exchange";
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'register' ? 'register' : 'login';

$departments = [
    'Computer Science & Engineering',
    'Electrical & Electronic Engineering',
    'Civil Engineering',
    'Textile Engineering',
    'Business Administration (BBA)',
    'English',
    'Law & Human Rights',
    'Pharmacy'
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-card" style="max-width: 520px;">
    <!-- Brand Headline & Tagline -->
    <div class="form-header" style="margin-bottom: 24px;">
        <div style="font-size: 2.2rem; margin-bottom: 6px;">🎓</div>
        <h2>Campus Skill Exchange</h2>
        <p style="font-size: 0.95rem; font-weight: 600; color: var(--primary); margin-top: 4px;">
            Share what you know. Learn what you need.
        </p>
    </div>

    <!-- Tab Navigation: Sign In & Create Account -->
    <div class="segmented-control" style="margin-bottom: 24px;">
        <a
            href="auth.php?tab=login"
            id="tabLogin"
            class="auth-tab-btn <?php echo ($activeTab === 'login') ? 'active' : ''; ?>"
            data-tab="login"
        >
            Sign In
        </a>
        <a
            href="auth.php?tab=register"
            id="tabRegister"
            class="auth-tab-btn <?php echo ($activeTab === 'register') ? 'active' : ''; ?>"
            data-tab="register"
        >
            Create Account
        </a>
    </div>

    <!-- 1. LOGIN FORM SECTION -->
    <div id="loginSection" style="<?php echo ($activeTab === 'login') ? 'display: block;' : 'display: none;'; ?>">
        <div class="form-header" style="margin-bottom: 18px;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Sign In to Your Account</h3>
            <p style="font-size: 0.85rem; color: var(--text-light);">Enter your Student ID or Email to continue</p>
        </div>

        <form id="loginForm" action="actions/login.php" method="POST">
            <div class="form-group" style="margin-bottom: 18px;">
                <label for="loginId">Student ID or Email <span style="color: var(--danger);">*</span></label>
                <input 
                    type="text" 
                    id="loginId" 
                    name="login_id" 
                    class="form-control" 
                    placeholder="e.g. 202312345 or ayesha@example.com" 
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="loginPassword">Password <span style="color: var(--danger);">*</span></label>
                <input 
                    type="password" 
                    id="loginPassword" 
                    name="password" 
                    class="form-control" 
                    placeholder="Enter your password" 
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px;">Sign In</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 0.86rem; color: var(--text-muted);">
            Don't have an account yet? 
            <a href="auth.php?tab=register">Create one here</a>
        </p>
    </div>

    <!-- 2. REGISTRATION FORM SECTION -->
    <div id="registerSection" style="<?php echo ($activeTab === 'register') ? 'display: block;' : 'display: none;'; ?>">
        <div class="form-header" style="margin-bottom: 18px;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Create Student Account</h3>
            <p style="font-size: 0.85rem; color: var(--text-light);">Join the student skill sharing network</p>
        </div>

        <form id="registerForm" action="actions/register.php" method="POST">
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="regFullName">Full Name <span style="color: var(--danger);">*</span></label>
                <input 
                    type="text" 
                    id="regFullName" 
                    name="full_name" 
                    class="form-control" 
                    placeholder="e.g. Ayesha Rahman" 
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="regStudentId">Student ID Number <span style="color: var(--danger);">*</span></label>
                <input 
                    type="text" 
                    id="regStudentId" 
                    name="student_id" 
                    class="form-control" 
                    placeholder="e.g. 202312345" 
                    required
                >
                <span class="form-text">Your unique university identifier.</span>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="regEmail">Campus Email Address <span style="color: var(--danger);">*</span></label>
                <input 
                    type="email" 
                    id="regEmail" 
                    name="email" 
                    class="form-control" 
                    placeholder="student@example.com" 
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="regDepartment">Academic Department <span style="color: var(--danger);">*</span></label>
                <select id="regDepartment" name="department" class="form-control" required>
                    <option value="">Select Your Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept); ?>">
                            <?php echo htmlspecialchars($dept); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="regPassword">Create Password <span style="color: var(--danger);">*</span></label>
                <input 
                    type="password" 
                    id="regPassword" 
                    name="password" 
                    class="form-control" 
                    placeholder="Minimum 6 characters" 
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="regConfirmPassword">Confirm Password <span style="color: var(--danger);">*</span></label>
                <input 
                    type="password" 
                    id="regConfirmPassword" 
                    name="confirm_password" 
                    class="form-control" 
                    placeholder="Repeat password" 
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px;">Create Account</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 0.86rem; color: var(--text-muted);">
            Already have an account? 
            <a href="auth.php?tab=login">Sign In</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
