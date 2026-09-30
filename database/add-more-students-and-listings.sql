-- Campus Skill Exchange: add realistic student accounts + listings
-- Run this ONCE if your existing database already contains the original 2 users / 8 listings.
USE cse472_skill_exchange;

-- Give the original demo users realistic-looking contact emails.
UPDATE users SET email = 'ayesha.rahman.cse@gmail.com' WHERE student_id = '202312345';
UPDATE users SET email = 'tanvir.hasan.eee@gmail.com' WHERE student_id = '202312346';
UPDATE skills SET contact_info = REPLACE(contact_info, 'ayesha@example.com', 'ayesha.rahman.cse@gmail.com') WHERE contact_info LIKE '%ayesha@example.com%';
UPDATE skills SET contact_info = REPLACE(contact_info, 'tanvir@example.com', 'tanvir.hasan.eee@gmail.com') WHERE contact_info LIKE '%tanvir@example.com%';

-- Four additional demo students. Password for all seeded accounts: password
INSERT INTO users (student_id, full_name, email, department, password_hash)
SELECT '202312347', 'Nabila Sultana', 'nabila.sultana.cse@gmail.com', 'Computer Science & Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE student_id = '202312347');

INSERT INTO users (student_id, full_name, email, department, password_hash)
SELECT '202312348', 'Rakib Ahmed', 'rakib.ahmed.bba@gmail.com', 'Business Administration (BBA)', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE student_id = '202312348');

INSERT INTO users (student_id, full_name, email, department, password_hash)
SELECT '202312349', 'Sadia Karim', 'sadia.karim.english@gmail.com', 'English', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE student_id = '202312349');

INSERT INTO users (student_id, full_name, email, department, password_hash)
SELECT '202312350', 'Fahim Rahman', 'fahim.rahman.civil@gmail.com', 'Civil Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE student_id = '202312350');

-- Add 8 more listings, while avoiding duplicates if this patch is accidentally run twice.
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'Java OOP & Problem Solving', 'teach', 'Programming',
       'I can help with classes, inheritance, interfaces, exception handling, and beginner-friendly problem solving for Java assignments.',
       'Sat & Mon, 7:00 PM - 9:00 PM (Library)', u.email, 'active', '2026-09-24 18:10:00'
FROM users u WHERE u.student_id = '202312347'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'Java OOP & Problem Solving');

INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'Excel Basics & Data Visualization', 'teach', 'Science',
       'Can guide beginners through Excel formulas, pivot tables, charts, and simple data summaries for coursework and projects.',
       'Friday 3:00 PM - 5:00 PM or Zoom', u.email, 'active', '2026-09-25 12:20:00'
FROM users u WHERE u.student_id = '202312348'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'Excel Basics & Data Visualization');

INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'Canva & Presentation Design', 'teach', 'Design',
       'Offering practical help with Canva layouts, presentation hierarchy, color balance, and clean academic slide design.',
       'Tue & Thu after 6:30 PM', u.email, 'active', '2026-09-26 17:40:00'
FROM users u WHERE u.student_id = '202312349'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'Canva & Presentation Design');

INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'AutoCAD 2D Drawing Basics', 'teach', 'Engineering',
       'I can help beginners with basic AutoCAD commands, layers, dimensions, and simple 2D technical drawings.',
       'Saturday 10:00 AM - 1:00 PM', u.email, 'active', '2026-09-27 10:25:00'
FROM users u WHERE u.student_id = '202312350'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'AutoCAD 2D Drawing Basics');

INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'Want to Learn React Fundamentals', 'learn', 'Programming',
       'Looking for a peer who can explain React components, props, state, and how to build a small frontend project from scratch.',
       'Weekdays after 8:00 PM', u.email, 'active', '2026-09-28 20:05:00'
FROM users u WHERE u.student_id = '202312347'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'Want to Learn React Fundamentals');

INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'Seeking Help with Digital Marketing', 'learn', 'Communication',
       'Want to learn social media content planning, basic SEO, and campaign analytics for a student entrepreneurship project.',
       'Saturday & Sunday afternoons', u.email, 'active', '2026-09-28 15:30:00'
FROM users u WHERE u.student_id = '202312348'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'Seeking Help with Digital Marketing');

INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'Looking for IELTS Speaking Practice', 'learn', 'Languages',
       'Looking for a speaking partner to practice common IELTS topics, pronunciation, fluency, and mock speaking sessions.',
       'Sun, Tue & Thu 8:00 PM - 9:30 PM', u.email, 'active', '2026-09-29 19:15:00'
FROM users u WHERE u.student_id = '202312349'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'Looking for IELTS Speaking Practice');

INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at)
SELECT u.id, 'Want to Learn Git & GitHub Workflow', 'learn', 'Programming',
       'Need practical guidance on branches, pull requests, resolving merge conflicts, and maintaining a clean student project repository.',
       'Flexible on weekday evenings', u.email, 'active', '2026-09-29 21:00:00'
FROM users u WHERE u.student_id = '202312350'
AND NOT EXISTS (SELECT 1 FROM skills WHERE title = 'Want to Learn Git & GitHub Workflow');


-- Final consistency pass: keep every student's name, account email, and listing contact email aligned.
UPDATE users SET email = 'ayesha.rahman.cse@gmail.com' WHERE student_id = '202312345';
UPDATE users SET email = 'tanvir.hasan.eee@gmail.com' WHERE student_id = '202312346';
UPDATE users SET email = 'nabila.sultana.cse@gmail.com' WHERE student_id = '202312347';
UPDATE users SET email = 'rakib.ahmed.bba@gmail.com' WHERE student_id = '202312348';
UPDATE users SET email = 'sadia.karim.english@gmail.com' WHERE student_id = '202312349';
UPDATE users SET email = 'fahim.rahman.civil@gmail.com' WHERE student_id = '202312350';
UPDATE skills s JOIN users u ON u.id = s.user_id SET s.contact_info = u.email;

-- Create reviews table if this is being applied to an existing database
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT uq_review_per_student_skill UNIQUE (skill_id, reviewer_id),
    CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE INDEX idx_reviews_skill ON reviews(skill_id);
CREATE INDEX idx_reviews_reviewer ON reviews(reviewer_id);

-- Add overlapping offers/requests so students have multiple choices for the same skill.
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 3, 'Python for Data Analysis & Pandas', 'teach', 'Programming', 'I can explain Python basics, Pandas, data cleaning, and small data analysis exercises with beginner-friendly examples.', 'Mon & Wed 6:30 PM - 8:30 PM', 'nabila.sultana.cse@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=3 AND title='Python for Data Analysis & Pandas' AND type='teach');
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 5, 'UI/UX Design in Figma & Wireframing', 'teach', 'Design', 'Can guide students through Figma wireframes, layout hierarchy, components, and practical interface design exercises.', 'Fri & Sat 4:00 PM - 6:00 PM', 'sadia.karim.english@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=5 AND title='UI/UX Design in Figma & Wireframing' AND type='teach');
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 4, 'Public Speaking & Presentation Slides', 'teach', 'Communication', 'I can help with presentation structure, slide storytelling, speaking practice, and short mock presentation sessions.', 'Tue & Thu 7:00 PM - 9:00 PM', 'rakib.ahmed.bba@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=4 AND title='Public Speaking & Presentation Slides' AND type='teach');
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 6, 'Java OOP & Problem Solving', 'teach', 'Programming', 'Can help beginners understand Java OOP concepts and practice common assignment and problem-solving patterns.', 'Sun & Wed 5:00 PM - 7:00 PM', 'fahim.rahman.civil@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=6 AND title='Java OOP & Problem Solving' AND type='teach');
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 4, 'Want to Learn Python for Data Analysis', 'learn', 'Programming', 'Looking for a peer who can teach Python, Pandas, and basic data analysis through practical examples.', 'Weekdays after 8:00 PM', 'rakib.ahmed.bba@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=4 AND title='Want to Learn Python for Data Analysis' AND type='learn');
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 6, 'Want to Learn UI/UX Design in Figma', 'learn', 'Design', 'Want to learn Figma wireframing and how to turn a rough idea into a clean responsive interface.', 'Saturday afternoons', 'fahim.rahman.civil@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=6 AND title='Want to Learn UI/UX Design in Figma' AND type='learn');
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 3, 'Want to Improve Public Speaking', 'learn', 'Communication', 'Looking for someone to practice presentations with and give feedback on delivery, pacing, and slide structure.', 'Sun & Tue after 6:00 PM', 'nabila.sultana.cse@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=3 AND title='Want to Improve Public Speaking' AND type='learn');
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status)
SELECT 5, 'Want to Learn Java OOP', 'learn', 'Programming', 'Need help understanding Java classes, inheritance, interfaces, and solving beginner programming problems.', 'Mon & Thu after 7:00 PM', 'sadia.karim.english@gmail.com', 'active'
WHERE NOT EXISTS (SELECT 1 FROM skills WHERE user_id=5 AND title='Want to Learn Java OOP' AND type='learn');

-- Seed a few realistic past-session reviews. Safe to re-run because of the unique constraint.
INSERT IGNORE INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 3, 5, 'Very clear explanations and good beginner examples. The practice exercises made the topic much easier.' FROM skills s WHERE s.user_id=1 AND s.title='Python for Data Analysis & Pandas' LIMIT 1;
INSERT IGNORE INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 4, 4, 'Explained the concepts step by step and was patient when I got stuck. Helpful for project work.' FROM skills s WHERE s.user_id=1 AND s.title='UI/UX Design in Figma & Wireframing' LIMIT 1;
INSERT IGNORE INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 5, 5, 'The mock presentation was really useful. I got practical feedback instead of just theory.' FROM skills s WHERE s.user_id=2 AND s.title='Public Speaking & Presentation Slides' LIMIT 1;
INSERT IGNORE INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 1, 5, 'Good pace and easy-to-follow Java examples. The OOP explanation finally clicked for me.' FROM skills s WHERE s.user_id=3 AND s.title='Java OOP & Problem Solving' LIMIT 1;
