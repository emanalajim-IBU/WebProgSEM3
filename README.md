# Building Management System

A web application for residents and building managers to handle community chat rooms, maintenance requests and building announcements. Built for the IT 2001 Web Programming course.

> **Status: Milestone 1** – project structure, static single-page frontend and draft ERD. The backend, database and API are added in the next milestones.
> **Status: Milestone 2** - MySQL database with sample data and the DAO layer.
> **Status: Milestone 3** – database, DAO layer, services, REST API with Swagger and an external weather API.

## Entities and relationships

![ERD](docs/ERD.png)

| Entity | Main fields | Relationships |
| --- | --- | --- |
| `users` | `user_id`, `full_name`, `email`, `password_hash`, `role` | one user has many `maintenance_requests`, `announcements` (as `created_by`) and `messages` |
| `channels` | `channel_id`, `name`, `type` | one channel has many `messages` |
| `messages` | `message_id`, `channel_id`, `user_id`, `message_text` | belongs to one channel and one user |
| `maintenance_requests` | `request_id`, `user_id`, `title`, `status` | belongs to one user |
| `announcements` | `announcement_id`, `created_by`, `title`, `content` | belongs to one user (author) |

## Roles and test accounts

| Role | Purpose | Test account |
| --- | --- | --- |
| `admin` | Manages users, channels, announcements and maintenance requests | added with the sample data (Milestone 2) |
| `resident` | Chats, reads announcements, submits maintenance requests | added with the sample data (Milestone 2) |

Real login will be availabe in Milestone 4.

## External API

Planned feature: a **weather card on the dashboard** (weather at the building's location), loaded through a backend endpoint, with a friendly message when the API is unavailable. The card is already placed in the static dashboard (`frontend/views/dashboard.html`). Implemented in Milestone 3.

## Project structure

```
frontend/   index.html, css/, js/ (app.js, router.js), views/ (one HTML file per view), static/
backend/    index.php, rest/ (routes/, services/, dao/), middleware/, config/, logs/
docs/       ERD.png, AI_LOG.md, DATABASE.md, openapi.json
AGENTS.md   rules for AI assistants
```

Frontend views: login, register, dashboard, chat channels, announcements, maintenance, users, profile, 404. Navigation is hash-based (`#/dashboard`), so pages change without a reload.

## Running locally (Milestone 1 & 2)

1. Put the project folder in `htdocs` and start **Apache** in XAMPP.
2. In phpMyAdmin, import docs/building_management.sql.
3. Copy backend/config/config.example.php to backend/config/config.php and fill in database values (and an OpenWeatherMap key for the weather card).
4. In the `..\WebProgSEM3\backend` folder run: composer install
5. Frontend: `http://localhost/WebProgSEM3/frontend/`
6. API: `http://localhost/WebProgSEM3/backend/`  Swagger: `http://localhost/WebProgSEM3/backend/swagger/`

---

4. With **Apache** start **MySQL** and import the `building_managment.sql` file into phpMyAdmin.
5. Also configure the `backend/config/config.php` to correct user, password.

## Links

- Swagger documentation: [Swagger](http://localhost/WebProgSEM3/backend/swagger/)
- Deployed application: _added in Milestone 5_
