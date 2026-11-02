# Database design (Milestone 2)

Database name: `building_management` (MySQL / MariaDB, InnoDB, utf8mb4).
Schema and sample data: [`building_management.sql`](building_management.sql). Diagram: [`ERD.png`](ERD.png).

## Tables

| Table | Columns | Keys |
| --- | --- | --- |
| `users` | `user_id` INT, `full_name` VARCHAR(100), `email` VARCHAR(150), `password_hash` VARCHAR(255), `role` VARCHAR(20) | PK `user_id`; `email` unique; `role` is `admin` or `resident` |
| `channels` | `channel_id` INT, `name` VARCHAR(100), `type` VARCHAR(20) | PK `channel_id`; `name` unique; `type` is `public` or `private` |
| `messages` | `message_id` INT, `channel_id` INT, `user_id` INT, `message_text` TEXT | PK `message_id`; FK `channel_id` -> `channels`; FK `user_id` -> `users` |
| `maintenance_requests` | `request_id` INT, `user_id` INT, `title` VARCHAR(200), `status` VARCHAR(20) | PK `request_id`; FK `user_id` -> `users`; `status` is `open`, `in_progress` or `resolved` |
| `announcements` | `announcement_id` INT, `created_by` INT, `title` VARCHAR(200), `content` TEXT | PK `announcement_id`; FK `created_by` -> `users` |

All primary keys are `AUTO_INCREMENT`. All foreign keys use `ON DELETE CASCADE`, so deleting a user or channel also deletes the rows that belong to it.

## Relationships

- one user writes many messages; one channel contains many messages
- one user submits many maintenance requests
- one user creates many announcements

## DAO layer

`backend/dao/`: `Database.php` (one shared PDO connection, credentials from `backend/config/config.php`), `BaseDao.php` (reusable `getAll`, `getById`, `insert`, `update`, `delete`) and one class per entity: `UserDao`, `ChannelDao`, `MessageDao`, `MaintenanceRequestDao`, `AnnouncementDao`.
Each DAO lists its writable columns, so a new column only needs to be added to that list. All SQL uses prepared statements.
The `passowrd_hash` field in the `users` table only stores the hash of the password that is entered.

## Setup

1. Start Apache and MySQL in XAMPP.
2. In phpMyAdmin, open the **Import** tab and import `docs/building_management.sql`.
3. Copy `backend/config/config.example.php` to `backend/config/config.php` and set `host`, `name` (`building_management`), `user` and `pass` for your machine.
