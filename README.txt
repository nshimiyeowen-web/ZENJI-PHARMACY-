ZENJI PHARMACY – Integrated Website & Management System
========================================================
SETUP (XAMPP)
1. Copy the folder "zenji_pharmacy" into C:\xampp\htdocs\
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open http://localhost/phpmyadmin -> Import -> choose database/zenji_pharmacy.sql -> Go.
4. Visit http://localhost/zenji_pharmacy/
5. Admin login: admin@zenji.com / Admin@123  (new registrations become "staff").

STRUCTURE
 index.html, about.html, contact.html  - public website (Component A)
 contact_submit.php                    - saves contact form messages
 register.php, login.php, logout.php   - authentication portal (hashed passwords, sessions)
 dashboard.php                         - READ: list, search, filter medicines
 add.php / edit.php / delete.php       - CREATE / UPDATE / DELETE (delete = admin only, with confirmation)
 messages.php                          - admin view of website enquiries
 reports.php                           - stock, low stock, expiry and category reports; CSV export & print
 includes/                             - db connection, session/security helpers, shared layout
 css/style.css, js/validate.js         - styling and client-side validation
 database/zenji_pharmacy.sql           - full database dump script

SECURITY FEATURES
 - PDO prepared statements on every query (SQL injection protection)
 - password_hash / password_verify (bcrypt)
 - session_regenerate_id on login, 30-minute inactivity timeout, HttpOnly cookies
 - CSRF tokens on all forms, htmlspecialchars output escaping (XSS protection)
 - Role-based access (admin vs staff)
