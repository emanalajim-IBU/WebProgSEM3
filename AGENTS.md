# AGENTS.md

## Project
Building Management System: A web application for residents and managers to handle community chat rooms, maintenance requests, and building announcements.
Roles: admin, resident. Entities: users, channels, messages, maintenance_requests, announcements.

## Project status
- Milestone 1 (done): project structure, static SPAP frontend, draft ERD.
- Milestone 2 (WIP): MySQL database with sample data and the DAO layer.

## Technology stack
- Frontend: HTML, CSS, JavaScript, Bootstrap 5 (SB Admin 2 template), single-page application (SPA)
- Backend: PHP with FlightPHP (REST API), PDO, MySQL
- Local server: XAMPP

## Folder structure
- frontend/: css/, js/, views/ (one HTML file per view), static/
- backend/: routes/, services/, dao/, middleware/, config/, logs/
- docs/: AI_LOG.md and other documentation

## Database 
- Database name: building_management (MySQL, InnoDB, utf8mb4). Schema and sample data: docs/building_management.sql. Documentation: docs/DATABASE.md. Diagram: docs/ERD.png.
- Tables and primary keys: users (user_id), channels (channel_id), messages (message_id), maintenance_requests (request_id), announcements (announcement_id).
- Foreign keys: messages.channel_id -> channels, messages.user_id -> users, maintenance_requests.user_id -> users, announcements.created_by -> users. All use ON DELETE CASCADE.
- Allowed values: users.role is admin or resident; channels.type is public or private; maintenance_requests.status is open, in_progress or resolved.
- If the schema is changed (only after asking the user), update docs/building_management.sql, docs/DATABASE.md and the column list of the affected DAO in the same change.

## Frontend rules
- Each view is in its own file in frontend/views/.
- Use Bootstrap 5 classes; avoid inline styles.

## Backend rules
- Requests go: route -> service -> DAO -> database.
- Only services call DAO classes. Routes never call DAO classes directly.
- All SQL uses PDO prepared statements.
- Routes return JSON with correct HTTP status codes.
- DAO layer lives in backend/dao/. Database.php creates the shared PDO connection from backend/config/config.php. BaseDao.php holds the reusable CRUD methods (getAll, getById, insert, update, delete).
- Every entity has its own DAO that extends BaseDao (UserDao, ChannelDao, MessageDao, MaintenanceRequestDao, AnnouncementDao). A DAO passes its table name, primary key and list of writable columns to BaseDao.
- Entity-specific queries (for example getByEmail) go into that entity's DAO, never into a route or a service. No SQL outside backend/dao/.
- DAO methods never decide HTTP status codes or business rules; that is the job of services and routes.

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

---
