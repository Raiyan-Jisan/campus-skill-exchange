/**
 * Main Client-Side Interactivity
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Menu Toggle
    const navToggle = document.getElementById('navToggle');
    const mainNav   = document.getElementById('mainNav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            mainNav.classList.toggle('active');
        });
    }

    // 2. Auth Tab Switching (Sign In vs Register)
    const tabBtns = document.querySelectorAll('.auth-tab-btn');
    if (tabBtns.length > 0) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-tab');
                switchAuthTab(targetTab);
            });
        });

        // Check URL parameters for active tab
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'register') {
            switchAuthTab('register');
        }
    }

    // 3. Close modal when clicking outside box or pressing ESC
    const modal = document.getElementById('skillDetailModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeSkillModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeSkillModal();
            }
        });
    }
});

/**
 * Switches between Login and Register tabs on auth.php
 */
function switchAuthTab(tabName) {
    const loginSection = document.getElementById('loginSection');
    const registerSection = document.getElementById('registerSection');
    const tabLogin = document.getElementById('tabLogin');
    const tabRegister = document.getElementById('tabRegister');

    if (tabName === 'register') {
        if (loginSection) loginSection.style.display = 'none';
        if (registerSection) registerSection.style.display = 'block';
        if (tabLogin) tabLogin.classList.remove('active');
        if (tabRegister) tabRegister.classList.add('active');
    } else {
        if (registerSection) registerSection.style.display = 'none';
        if (loginSection) loginSection.style.display = 'block';
        if (tabRegister) tabRegister.classList.remove('active');
        if (tabLogin) tabLogin.classList.add('active');
    }
}

/**
 * Opens and populates the Skill Details Modal on explore.php / index.php
 */
function openSkillModal(skill) {
    const modal = document.getElementById('skillDetailModal');
    if (!modal) return;

    document.getElementById('modalTitle').textContent = skill.title || 'Skill Details';
    
    // Type badge
    const badgeEl = document.getElementById('modalTypeBadge');
    if (badgeEl) {
        badgeEl.textContent = skill.type === 'teach' ? 'Can Teach' : 'Want to Learn';
        badgeEl.className = 'badge ' + (skill.type === 'teach' ? 'badge-teach' : 'badge-learn');
    }

    document.getElementById('modalCategory').textContent = skill.category || 'General';
    document.getElementById('modalDescription').textContent = skill.description || 'No description provided.';
    document.getElementById('modalAvailability').textContent = skill.availability || 'Not specified';
    document.getElementById('modalAuthor').textContent = skill.author || 'University Student';
    document.getElementById('modalDepartment').textContent = skill.department || 'Department Student';
    document.getElementById('modalDate').textContent = skill.date || 'Recent';

    // Reviews for teaching listings
    const reviewsBox = document.getElementById('modalReviews');
    if (reviewsBox) {
        const reviews = Array.isArray(skill.reviews) ? skill.reviews : [];
        const avg = Number(skill.ratingAvg || 0);
        const count = Number(skill.reviewCount || reviews.length || 0);
        let reviewsHtml = `<div class="reviews-heading"><strong>Student Reviews</strong>`;
        if (skill.type === 'teach' && count > 0) {
            reviewsHtml += `<span class="review-summary" style="margin:0"><span class="review-stars">★★★★★</span> ${avg.toFixed(1)} (${count})</span>`;
        }
        reviewsHtml += `</div>`;

        if (reviews.length) {
            reviews.forEach(review => {
                const stars = '★'.repeat(Number(review.rating || 0)) + '☆'.repeat(5 - Number(review.rating || 0));
                reviewsHtml += `<div class="review-item"><div class="review-item-top"><span class="review-author">${escapeHtml(review.reviewer_name)}</span><span class="review-date">${escapeHtml(review.created_at ? review.created_at.substring(0, 10) : '')}</span></div><div class="review-stars">${stars}</div><p class="review-comment">${escapeHtml(review.comment)}</p></div>`;
            });
        } else if (skill.type === 'teach') {
            reviewsHtml += `<p class="review-comment">No reviews yet. Reviews appear after students complete a learning session.</p>`;
        } else {
            reviewsHtml += `<p class="review-comment">Reviews are shown on student teaching offers.</p>`;
        }

        // Only other students can review a teaching listing. The form intentionally asks users to review after an actual session.
        if (skill.type === 'teach' && Number(skill.owner_id) !== Number(skill.currentUserId || 0) && Number(skill.id) > 0) {
            reviewsHtml += `<div class="review-form"><form action="actions/create-review.php" method="POST"><input type="hidden" name="skill_id" value="${Number(skill.id)}"><label for="reviewRating">Your rating</label><select id="reviewRating" name="rating" class="form-control" required><option value="">Select rating</option><option value="5">★★★★★ — Excellent</option><option value="4">★★★★☆ — Very good</option><option value="3">★★★☆☆ — Good</option><option value="2">★★☆☆☆ — Needs improvement</option><option value="1">★☆☆☆☆ — Poor</option></select><label for="reviewComment">Your experience</label><textarea id="reviewComment" name="comment" class="form-control" rows="3" maxlength="500" placeholder="How was the teaching style?" required></textarea><button type="submit" class="btn btn-primary btn-sm">Submit Review</button></form></div>`;
        }
        reviewsBox.innerHTML = reviewsHtml;
    }

    // Contact information handling
    const contactBox = document.getElementById('modalContactBox');
    if (contactBox) {
        if (skill.contact) {
            const safeContact = escapeHtml(skill.contact);
            const emailMatch = String(skill.contact).match(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i);
            const emailButton = emailMatch
                ? ` <a href="mailto:${encodeURIComponent(emailMatch[0])}" class="btn btn-primary btn-sm" style="margin-left:8px;">Email Student</a>`
                : '';
            contactBox.innerHTML = `<strong>Direct Contact:</strong> <code>${safeContact}</code>${emailButton}`;
            contactBox.style.display = 'block';
        } else {
            contactBox.innerHTML = `<strong>Contact:</strong> <em>Author did not specify extra contact info. Connect via campus email.</em>`;
            contactBox.style.display = 'block';
        }
    }

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Closes the Skill Details Modal
 */
function closeSkillModal() {
    const modal = document.getElementById('skillDetailModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Utility to escape HTML strings inside JavaScript
 */
function escapeHtml(string) {
    if (!string) return '';
    return String(string)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
