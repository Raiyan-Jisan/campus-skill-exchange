# Campus Skill Exchange
**CSE 472 — Web and Internet Programming Lab**  
**Individual Full-Stack Mini Web Application Project**  
*Southeast University | Department of Computer Science & Engineering*

---

## 1. Project Overview

**Campus Skill Exchange** is a peer-to-peer university platform where students can exchange skills they can teach or find skills they want to learn. For example, a student can post *"I can teach Python for Data Analysis"* or *"I want to learn Figma UI/UX Design"*. Fellow classmates can browse, search, and filter these offerings across departments, view details, and connect directly.

The project is built strictly using the core technologies taught in the **CSE 472 Lab Manuals (Labs 01 to 07)**:
- **Structure:** Semantic HTML5 (`header`, `nav`, `main`, `section`, `footer`)
- **Styling:** Pure CSS3 (University theme, deep academic green `#14532d`, responsive layout)
- **Client Interactivity & Validation:** Vanilla JavaScript (DOM manipulation, event listeners, localStorage draft saving)
- **Backend:** PHP 8.x (Session management, POST form processing, server-side validation)
- **Database:** MySQL / MariaDB using **PDO (PHP Data Objects)** with prepared statements

---

## 2. Directory & File Structure

```text
campus-skill-exchange/
├── index.php                 # Home / Landing page with metrics & featured listings
├── explore.php               # Browse, search, and filter all campus skill listings
├── skill-form.php            # Combined Create & Edit skill listing form
├── dashboard.php             # Protected student dashboard with metrics & listing management
├── auth.php                  # Student login & registration page (dual tab)
├── logout.php                # Secure session termination & redirect
├── includes/
│   ├── db.php                # PDO database connection with error handling (Lab 07)
│   ├── auth-check.php        # Session authentication middleware to protect private pages
│   ├── functions.php         # Reusable helpers: clean_input(), set_flash(), display_flash()
│   ├── header.php            # Semantic header, university branding & dynamic navigation
│   └── footer.php            # Semantic footer, course metadata & script inclusions
├── actions/
│   ├── register.php          # Processes registration (duplicate check, password_hash, PDO)
│   ├── login.php             # Processes login (password_verify, session_regenerate_id)
│   ├── create-skill.php      # Processes skill insertion using PDO prepared statements
│   ├── update-skill.php      # Processes skill edit with strict ownership check
│   └── delete-skill.php      # Processes skill removal via POST with strict ownership check
├── css/
│   └── style.css             # Pure CSS3 stylesheet (no frameworks, fully responsive)
├── js/
│   ├── main.js               # UI interactivity: mobile toggle, auth tabs, details modal
│   └── validation.js         # Client-side validation & localStorage draft saving (Lab 04)
├── database/
│   └── setup.sql             # Database creation, tables, foreign keys & 8 seed listings
└── README.md                 # Setup guide, test cases & project documentation
```

---

## 3. Database Architecture (`cse472_skill_exchange`)

Normalized relational database linking students (`users`) with skill postings (`skills`) via a `1-to-Many` Foreign Key constraint.

### `users` Table
| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `id` | `INT` | `AUTO_INCREMENT PRIMARY KEY` | Internal user ID |
| `student_id` | `VARCHAR(30)` | `NOT NULL UNIQUE` | Official University Student ID |
| `full_name` | `VARCHAR(100)` | `NOT NULL` | Student full name |
| `email` | `VARCHAR(120)` | `NOT NULL UNIQUE` | Campus email address |
| `department` | `VARCHAR(60)` | `NOT NULL` | Academic department (CSE, EEE, etc.) |
| `password_hash` | `VARCHAR(255)` | `NOT NULL` | Securely hashed password (`password_hash()`) |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Account creation timestamp |

### `skills` Table
| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `id` | `INT` | `AUTO_INCREMENT PRIMARY KEY` | Unique listing ID |
| `user_id` | `INT` | `NOT NULL, FK -> users(id)` | Author student reference (ON DELETE CASCADE) |
| `title` | `VARCHAR(120)` | `NOT NULL` | Headline of skill |
| `type` | `ENUM('teach', 'learn')` | `NOT NULL DEFAULT 'teach'` | Offer vs. Request |
| `category` | `VARCHAR(60)` | `NOT NULL` | Subject (Programming, Design, Math, etc.) |
| `description` | `TEXT` | `NOT NULL` | Full details / syllabus / request |
| `availability` | `VARCHAR(120)` | `NOT NULL` | Available days and time slots |
| `contact_info` | `VARCHAR(120)` | `NULL` | Preferred communication channel |
| `status` | `ENUM('active', 'closed')` | `DEFAULT 'active'` | Active or Closed |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Created timestamp |
| `updated_at` | `TIMESTAMP` | `ON UPDATE CURRENT_TIMESTAMP` | Last updated timestamp |

---

## 4. XAMPP Localhost Setup Instructions

Follow these steps to run the application on your Windows machine with XAMPP:

### Step 1: Copy Files to `htdocs`
1. Open File Explorer and navigate to:  
   `C:\xampp\htdocs\`
2. Place or copy the project folder so that its path becomes:  
   `C:\xampp\htdocs\campus-skill-exchange\`

### Step 2: Start Apache and MySQL
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Ensure both services show green status indicators.

### Step 3: Import the Database
1. Open your browser and go to:  
   `http://localhost/phpmyadmin/`
2. Click on the **SQL** tab in the top menu.
3. Open `database/setup.sql` in VS Code or Notepad, copy all contents, paste into the phpMyAdmin SQL text box, and click **Go**.
4. The database `cse472_skill_exchange` will be created with both tables (`users`, `skills`) and populated with 16 sample listings and 6 demo student accounts.

### Step 4: Open the Application
In your browser, visit:  
`http://localhost/campus-skill-exchange/`

---

## 5. Demo Student Accounts

For quick grading and presentation testing, two student accounts are pre-seeded:

| Student Account | Student ID | Email | Password | Department |
|---|---|---|---|---|
| **Ayesha Rahman** | `202312345` | `ayesha.rahman.cse@gmail.com` | `password` | Computer Science & Engineering |
| **Tanvir Hasan** | `202312346` | `tanvir.hasan.eee@gmail.com` | `password` | Electrical & Electronic Engineering |
| **Nabila Sultana** | `202312347` | `nabila.sultana.cse@gmail.com` | `password` | Computer Science & Engineering |
| **Rakib Ahmed** | `202312348` | `rakib.ahmed.bba@gmail.com` | `password` | Business Administration (BBA) |
| **Sadia Karim** | `202312349` | `sadia.karim.english@gmail.com` | `password` | English |
| **Fahim Rahman** | `202312350` | `fahim.rahman.civil@gmail.com` | `password` | Civil Engineering |

*(Note: On the `auth.php` page, you can also click the **"Student 1 (CSE)"** or **"Student 2 (EEE)"** buttons to auto-fill the login form instantly).*

---

## 6. Security & Best Practices Implemented

1. **SQL Injection Prevention:**
   - Exclusively uses **PDO Prepared Statements** with named parameter bindings (`:title`, `:user_id`, etc.) across all SELECT, INSERT, UPDATE, and DELETE operations.
2. **Password Security:**
   - User passwords are encrypted using PHP's native `password_hash($password, PASSWORD_DEFAULT)` using bcrypt.
   - Authentication is verified using `password_verify($password, $user['password_hash'])`.
3. **Session Security & Fixation Prevention:**
   - `session_regenerate_id(true)` is executed upon successful login and registration.
   - `logout.php` completely flushes `$_SESSION` and removes the session cookie.
4. **Cross-Site Scripting (XSS) Prevention:**
   - All user-generated content rendered into HTML attributes or text nodes is passed through `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
5. **Strict Authorization Protection:**
   - Both `actions/update-skill.php` and `actions/delete-skill.php` strictly verify that the record's `user_id` matches the session's `current_user_id()`.
   - Modifying a hidden `id` or URL parameter to target another student's listing is rejected by the server.
6. **Destructive Actions Guarded by POST:**
   - Listing deletion is executed exclusively via POST requests with JavaScript confirmation dialogs, preventing accidental deletion or CSRF via simple link clicks.

---

## 7. Testing & Verification Checklist

| # | Test Scenario | Steps | Expected Result | Verified |
|---|---|---|---|---|
| **1** | **User Registration** | Go to `auth.php?tab=register`, enter new details, submit. | Account is created in MySQL, session starts, redirects to `dashboard.php` with welcome alert. | Yes |
| **2** | **Duplicate Check** | Try registering again with the same Student ID or Email. | Blocked with clear error message; duplicate row prevented. | Yes |
| **3** | **Search & Category Filtering** | Go to `explore.php`, search `"Python"` and filter by `"Programming"`. | Only matching listings appear; count updates; reset button works. | Yes |
| **4** | **Create & Edit Listing** | Log in, go to `skill-form.php`, post a skill, then click Edit from dashboard. | Data pre-fills correctly; changes update database upon saving. | Yes |
| **5** | **Authorization Enforcement** | Attempt to delete or edit a listing while not logged in, or attempt to edit someone else's ID. | Redirected to `auth.php` or `dashboard.php` with an authorization error banner. | Yes |
| **6** | **Empty Field Validation** | Submit blank fields on `auth.php` or `skill-form.php`. | Client-side validation halts submission and highlights error; server-side backup checks ensure data integrity. | Yes |
| **7** | **Draft Autosave (Lab 04)** | Type text into `skill-form.php` and refresh the page before submitting. | Text is restored from `localStorage` with a notice badge. | Yes |

## Additional Features
- Multiple students can offer or request overlapping skills, giving learners more than one peer option.
- Teaching listings can display student ratings and written reviews.
- Logged-in students can submit one review per teaching listing after a learning session.
- Direct email contact is available from listing details.
