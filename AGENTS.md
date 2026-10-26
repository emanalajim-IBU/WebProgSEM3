# AGENTS.md

## Project
Building Management System: A web application for residents and managers to handle community chat rooms, maintenance requests, and building announcements.
Roles: admin, resident. Entities: users, channels, messages, maintenance_requests, announcements.

## Technology stack
- Frontend: HTML, CSS, JavaScript, Bootstrap 5 (SB Admin 2 template), single-page application (SPA)
- Backend: PHP with FlightPHP (REST API), PDO, MySQL
- Local server: XAMPP

## Folder structure
- frontend/: css/, js/, views/ (one HTML file per view), static/
- backend/: routes/, services/, dao/, middleware/, config/, logs/
- docs/: AI_LOG.md and other documentation

## Frontend rules
- Each view is in its own file in frontend/views/.
- Use Bootstrap 5 classes; avoid inline styles.

## Backend rules
- Requests go: route -> service -> DAO -> database.
- Only services call DAO classes. Routes never call DAO classes directly.
- All SQL uses PDO prepared statements.
- Routes return JSON with correct HTTP status codes.

## Conventions
- PHP classes: PascalCase (UserService, UserDao). Files are named after the class.
- JavaScript and PHP variables: camelCase. Database tables and columns: snake_case.

## Rules for AI assistants
- Do not add new libraries or frameworks without asking me first.
- Do not change the database schema without asking me first.
- Never put passwords, keys or secrets in code; they belong in backend/config/config.php, which is not committed. Update config.example.php instead.
- Do not edit files in backend/logs/.
- Make small changes and explain what you changed and why.
- **Existing File & Folder Protection:** If a file or folder already exists in the project workspace, keep it as-is. Do not overwrite, delete, or recreate existing files or directories unless explicitly instructed.
```[cite: 1, 2]

---
