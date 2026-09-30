<?php
/**
 * Global Footer - Clean Minimal Design
 * Reference: Semantic HTML Footer
 */
require_once __DIR__ . '/functions.php';
?>
    </div><!-- /.content-container -->
</main><!-- /.page-content -->

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-minimal">
            <div class="footer-brand">
                <span class="logo-icon">🎓</span>
                <h4>Campus Skill Exchange</h4>
            </div>
            <p class="footer-tagline">Share what you know. Learn what you need.</p>
            
            <?php if (is_logged_in()): ?>
                <div class="footer-nav">
                    <a href="index.php">Home</a>
                    <span class="footer-sep">|</span>
                    <a href="explore.php">Explore Skills</a>
                    <span class="footer-sep">|</span>
                    <a href="skill-form.php">Post a Skill</a>
                    <span class="footer-sep">|</span>
                    <a href="dashboard.php">My Dashboard</a>
                </div>
            <?php else: ?>
                <div class="footer-nav">
                    <a href="auth.php">Sign In</a>
                    <span class="footer-sep">|</span>
                    <a href="auth.php?tab=register">Create Account</a>
                </div>
            <?php endif; ?>

            <div class="footer-bottom-minimal">
                <p>&copy; <?php echo date('Y'); ?> Campus Skill Exchange. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Vanilla JavaScript -->
<script src="js/validation.js"></script>
<script src="js/main.js"></script>
</body>
</html>
