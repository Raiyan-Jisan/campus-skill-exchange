-- ==========================================================
-- Campus Skill Exchange - Database Initialization Script
-- Course: CSE 472 Web and Internet Programming Lab
-- Compatible with: MySQL 5.7+ / MariaDB 10.x / phpMyAdmin
-- ==========================================================

-- 1. Create Database
CREATE DATABASE IF NOT EXISTS cse472_skill_exchange
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE cse472_skill_exchange;

-- 2. Drop existing tables if re-running
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS users;

-- 3. Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    department VARCHAR(60) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Create Skills Table
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(120) NOT NULL,
    type ENUM('teach', 'learn') NOT NULL DEFAULT 'teach',
    category VARCHAR(60) NOT NULL,
    description TEXT NOT NULL,
    availability VARCHAR(120) NOT NULL,
    contact_info VARCHAR(120) NULL,
    status ENUM('active', 'closed') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_skills_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4b. Reviews for completed teaching exchanges
CREATE TABLE reviews (
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

-- 5. Indexes for fast searching and filtering
CREATE INDEX idx_skills_type ON skills(type);
CREATE INDEX idx_skills_category ON skills(category);
CREATE INDEX idx_skills_status ON skills(status);
CREATE INDEX idx_reviews_skill ON reviews(skill_id);
CREATE INDEX idx_reviews_reviewer ON reviews(reviewer_id);

-- 6. Insert Demo Users (Pre-hashed passwords for 'password' and 'Password123!')
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi corresponds to 'password'
-- We also provide password 'Password123!' support.
INSERT INTO users (id, student_id, full_name, email, department, password_hash) VALUES
(1, '202312345', 'Ayesha Rahman', 'ayesha.rahman.cse@gmail.com', 'Computer Science & Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, '202312346', 'Tanvir Hasan', 'tanvir.hasan.eee@gmail.com', 'Electrical & Electronic Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, '202312347', 'Nabila Sultana', 'nabila.sultana.cse@gmail.com', 'Computer Science & Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(4, '202312348', 'Rakib Ahmed', 'rakib.ahmed.bba@gmail.com', 'Business Administration (BBA)', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(5, '202312349', 'Sadia Karim', 'sadia.karim.english@gmail.com', 'English', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(6, '202312350', 'Fahim Rahman', 'fahim.rahman.civil@gmail.com', 'Civil Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 7. Insert 8 Realistic Skill Listings (4 'teach', 4 'learn')
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at) VALUES
(1, 'Python for Data Analysis & Pandas', 'teach', 'Programming', 
 'I can help beginners grasp Python syntax, NumPy matrices, and Pandas dataframes for university lab assignments and course projects.', 
 'Sun, Tue after 4:00 PM (CSE Lab 3)', 'ayesha.rahman.cse@gmail.com / WhatsApp: 01700-112233', 'active', '2026-09-20 10:15:00'),

(2, 'Digital Logic & Circuit Troubleshooting', 'teach', 'Engineering', 
 'Can explain Karnaugh maps, Boolean simplification, multiplexers, and breadboard circuit debugging for DLD lab exams.', 
 'Monday & Thursday 2:00 PM - 5:00 PM', 'tanvir.hasan.eee@gmail.com', 'active', '2026-09-21 11:30:00'),

(1, 'UI/UX Design in Figma & Wireframing', 'teach', 'Design', 
 'Offering practical sessions on Figma components, typography scales, spacing tokens, and clean responsive layout architecture.', 
 'Saturdays 3:00 PM - 6:00 PM', 'ayesha.rahman.cse@gmail.com', 'active', '2026-09-22 14:00:00'),

(2, 'Public Speaking & Presentation Slides', 'teach', 'Communication', 
 'Need to present your capstone or lab defense? I can help you structure slide decks and overcome stage anxiety with mock presentations.', 
 'Flexible on Zoom or Central Library lobby', 'tanvir.hasan.eee@gmail.com', 'active', '2026-09-23 09:45:00'),

(1, 'Looking for Guidance in PHP & MySQL (PDO)', 'learn', 'Programming', 
 'Want to partner with a senior or peer to master PHP session management, prepared statements, and normalized SQL schemas.', 
 'Evenings after 6:00 PM', 'ayesha.rahman.cse@gmail.com', 'active', '2026-09-24 16:20:00'),

(2, 'Seeking MATLAB & Signal Processing Help', 'learn', 'Engineering', 
 'Looking for someone experienced in MATLAB Fourier transforms and waveform plotting for Signals and Systems homework.', 
 'Wednesday afternoons', 'tanvir.hasan.eee@gmail.com', 'active', '2026-09-25 13:10:00'),

(1, 'Academic Paper Writing & Harvard Referencing', 'learn', 'Academic', 
 'Seeking review and tips on citation styles (Harvard / IEEE), literature reviews, and avoiding accidental plagiarism in research reports.', 
 'Sundays 11:00 AM - 1:00 PM', 'ayesha.rahman.cse@gmail.com', 'active', '2026-09-26 10:00:00'),

(2, 'Calculus & Discrete Mathematics Practice', 'learn', 'Mathematics', 
 'Looking for a study partner to solve graph theory, combinatorics, and integration problems for the upcoming midterms.', 
 'Daily 5:00 PM - 7:00 PM (Study Room B)', 'tanvir.hasan.eee@gmail.com', 'active', '2026-09-27 15:30:00');

-- 8 additional listings from different student users (total: 16 listings, 8 teach + 8 learn)
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at) VALUES
(3, 'Java OOP & Problem Solving', 'teach', 'Programming',
 'I can help with classes, inheritance, interfaces, exception handling, and beginner-friendly problem solving for Java assignments.',
 'Sat & Mon, 7:00 PM - 9:00 PM (Library)', 'nabila.sultana.cse@gmail.com', 'active', '2026-09-24 18:10:00'),

(4, 'Excel Basics & Data Visualization', 'teach', 'Science',
 'Can guide beginners through Excel formulas, pivot tables, charts, and simple data summaries for coursework and projects.',
 'Friday 3:00 PM - 5:00 PM or Zoom', 'rakib.ahmed.bba@gmail.com', 'active', '2026-09-25 12:20:00'),

(5, 'Canva & Presentation Design', 'teach', 'Design',
 'Offering practical help with Canva layouts, presentation hierarchy, color balance, and clean academic slide design.',
 'Tue & Thu after 6:30 PM', 'sadia.karim.english@gmail.com', 'active', '2026-09-26 17:40:00'),

(6, 'AutoCAD 2D Drawing Basics', 'teach', 'Engineering',
 'I can help beginners with basic AutoCAD commands, layers, dimensions, and simple 2D technical drawings.',
 'Saturday 10:00 AM - 1:00 PM', 'fahim.rahman.civil@gmail.com', 'active', '2026-09-27 10:25:00'),

(3, 'Want to Learn React Fundamentals', 'learn', 'Programming',
 'Looking for a peer who can explain React components, props, state, and how to build a small frontend project from scratch.',
 'Weekdays after 8:00 PM', 'nabila.sultana.cse@gmail.com', 'active', '2026-09-28 20:05:00'),

(4, 'Seeking Help with Digital Marketing', 'learn', 'Communication',
 'Want to learn social media content planning, basic SEO, and campaign analytics for a student entrepreneurship project.',
 'Saturday & Sunday afternoons', 'rakib.ahmed.bba@gmail.com', 'active', '2026-09-28 15:30:00'),

(5, 'Looking for IELTS Speaking Practice', 'learn', 'Languages',
 'Looking for a speaking partner to practice common IELTS topics, pronunciation, fluency, and mock speaking sessions.',
 'Sun, Tue & Thu 8:00 PM - 9:30 PM', 'sadia.karim.english@gmail.com', 'active', '2026-09-29 19:15:00'),

(6, 'Want to Learn Git & GitHub Workflow', 'learn', 'Programming',
 'Need practical guidance on branches, pull requests, resolving merge conflicts, and maintaining a clean student project repository.',
 'Flexible on weekday evenings', 'fahim.rahman.civil@gmail.com', 'active', '2026-09-29 21:00:00');


-- 9. More overlapping listings so the same skill has multiple student choices
INSERT INTO skills (user_id, title, type, category, description, availability, contact_info, status, created_at) VALUES
(3, 'Python for Data Analysis & Pandas', 'teach', 'Programming',
 'I can explain Python basics, Pandas, data cleaning, and small data analysis exercises with beginner-friendly examples.',
 'Mon & Wed 6:30 PM - 8:30 PM', 'nabila.sultana.cse@gmail.com', 'active', '2026-09-27 18:10:00'),
(5, 'UI/UX Design in Figma & Wireframing', 'teach', 'Design',
 'Can guide students through Figma wireframes, layout hierarchy, components, and practical interface design exercises.',
 'Fri & Sat 4:00 PM - 6:00 PM', 'sadia.karim.english@gmail.com', 'active', '2026-09-28 16:10:00'),
(4, 'Public Speaking & Presentation Slides', 'teach', 'Communication',
 'I can help with presentation structure, slide storytelling, speaking practice, and short mock presentation sessions.',
 'Tue & Thu 7:00 PM - 9:00 PM', 'rakib.ahmed.bba@gmail.com', 'active', '2026-09-28 19:20:00'),
(6, 'Java OOP & Problem Solving', 'teach', 'Programming',
 'Can help beginners understand Java OOP concepts and practice common assignment and problem-solving patterns.',
 'Sun & Wed 5:00 PM - 7:00 PM', 'fahim.rahman.civil@gmail.com', 'active', '2026-09-29 17:20:00'),
(4, 'Want to Learn Python for Data Analysis', 'learn', 'Programming',
 'Looking for a peer who can teach Python, Pandas, and basic data analysis through practical examples.',
 'Weekdays after 8:00 PM', 'rakib.ahmed.bba@gmail.com', 'active', '2026-09-29 20:15:00'),
(6, 'Want to Learn UI/UX Design in Figma', 'learn', 'Design',
 'Want to learn Figma wireframing and how to turn a rough idea into a clean responsive interface.',
 'Saturday afternoons', 'fahim.rahman.civil@gmail.com', 'active', '2026-09-29 14:40:00'),
(3, 'Want to Improve Public Speaking', 'learn', 'Communication',
 'Looking for someone to practice presentations with and give feedback on delivery, pacing, and slide structure.',
 'Sun & Tue after 6:00 PM', 'nabila.sultana.cse@gmail.com', 'active', '2026-09-29 18:25:00'),
(5, 'Want to Learn Java OOP', 'learn', 'Programming',
 'Need help understanding Java classes, inheritance, interfaces, and solving beginner programming problems.',
 'Mon & Thu after 7:00 PM', 'sadia.karim.english@gmail.com', 'active', '2026-09-29 21:10:00');

-- 10. Sample reviews from students who previously learned from peer tutors
INSERT INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 3, 5, 'Very clear explanations and good beginner examples. The practice exercises made the topic much easier.'
FROM skills s WHERE s.user_id = 1 AND s.title = 'Python for Data Analysis & Pandas' LIMIT 1;
INSERT INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 4, 4, 'Explained the concepts step by step and was patient when I got stuck. Helpful for project work.'
FROM skills s WHERE s.user_id = 1 AND s.title = 'UI/UX Design in Figma & Wireframing' LIMIT 1;
INSERT INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 5, 5, 'The mock presentation was really useful. I got practical feedback instead of just theory.'
FROM skills s WHERE s.user_id = 2 AND s.title = 'Public Speaking & Presentation Slides' LIMIT 1;
INSERT INTO reviews (skill_id, reviewer_id, rating, comment)
SELECT s.id, 1, 5, 'Good pace and easy-to-follow Java examples. The OOP explanation finally clicked for me.'
FROM skills s WHERE s.user_id = 3 AND s.title = 'Java OOP & Problem Solving' LIMIT 1;
