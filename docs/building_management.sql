-- Building Management System: database schema and sample data (Milestone 2)
-- Import with phpMyAdmin (Import tab) or: mysql -u root < docs/building_management.sql

CREATE DATABASE IF NOT EXISTS building_management
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE building_management;

DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS maintenance_requests;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS channels;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  user_id   INT          NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(100) NOT NULL,
  email     VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role      VARCHAR(20)  NOT NULL DEFAULT 'resident',
  PRIMARY KEY (user_id),
  UNIQUE KEY uq_users_email (email),
  CONSTRAINT chk_users_role CHECK (role IN ('admin', 'resident'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE channels (
  channel_id INT         NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  type       VARCHAR(20)  NOT NULL DEFAULT 'public',
  PRIMARY KEY (channel_id),
  UNIQUE KEY uq_channels_name (name),
  CONSTRAINT chk_channels_type CHECK (type IN ('public', 'private'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages (
  message_id   INT  NOT NULL AUTO_INCREMENT,
  channel_id   INT  NOT NULL,
  user_id      INT  NOT NULL,
  message_text TEXT NOT NULL,
  PRIMARY KEY (message_id),
  KEY idx_messages_channel (channel_id),
  KEY idx_messages_user (user_id),
  CONSTRAINT fk_messages_channel FOREIGN KEY (channel_id) REFERENCES channels (channel_id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_user    FOREIGN KEY (user_id)    REFERENCES users (user_id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE maintenance_requests (
  request_id INT          NOT NULL AUTO_INCREMENT,
  user_id    INT          NOT NULL,
  title      VARCHAR(200) NOT NULL,
  status     VARCHAR(20)  NOT NULL DEFAULT 'open',
  PRIMARY KEY (request_id),
  KEY idx_requests_user (user_id),
  CONSTRAINT fk_requests_user FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
  CONSTRAINT chk_requests_status CHECK (status IN ('open', 'in_progress', 'resolved'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE announcements (
  announcement_id INT          NOT NULL AUTO_INCREMENT,
  created_by      INT          NOT NULL,
  title           VARCHAR(200) NOT NULL,
  content         TEXT         NOT NULL,
  PRIMARY KEY (announcement_id),
  KEY idx_announcements_author (created_by),
  CONSTRAINT fk_announcements_user FOREIGN KEY (created_by) REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data (same data as the static frontend) the hash is just "Demo1234!" for this stage
INSERT INTO users (user_id, full_name, email, password_hash, role) VALUES
  (1, 'Eman Alajim', 'eman.alajim@stu.ibu.edu.ba', '$2y$10$mgvccyAGVtONiARxYtADPOzyXSewh9L9zIRwqDbnO8RRkDilikWtu', 'admin'), 
  (2, 'Liam Novak',  'liam.novak@example.com', '$2y$10$mgvccyAGVtONiARxYtADPOzyXSewh9L9zIRwqDbnO8RRkDilikWtu', 'resident'),
  (3, 'Sofia Marin', 'sofia.marin@example.com', '$2y$10$mgvccyAGVtONiARxYtADPOzyXSewh9L9zIRwqDbnO8RRkDilikWtu',  'resident'),
  (4, 'Noah Berg',   'noah.berg@example.com', '$2y$10$mgvccyAGVtONiARxYtADPOzyXSewh9L9zIRwqDbnO8RRkDilikWtu', 'resident'),
  (5, 'Mia Duran',   'mia.duran@example.com', '$2y$10$mgvccyAGVtONiARxYtADPOzyXSewh9L9zIRwqDbnO8RRkDilikWtu', 'resident');

INSERT INTO channels (channel_id, name, type) VALUES
  (1, 'General',   'public'),
  (2, 'Parking',   'public'),
  (3, 'Events',    'public'),
  (4, 'Committee', 'private');

INSERT INTO messages (message_id, channel_id, user_id, message_text) VALUES
  (1, 1, 1, 'Hello everyone, welcome to the building chat.'),
  (2, 1, 2, 'Does anyone know when the elevator inspection is scheduled?'),
  (3, 1, 1, 'Next Tuesday morning. Details are in the announcements.'),
  (4, 2, 4, 'Someone is parked in spot 14 again. Can the managers check?'),
  (5, 2, 1, 'Thanks Noah, I will send a reminder to all residents today.'),
  (6, 3, 5, 'Would anyone like to organise a garden afternoon this month?'),
  (7, 3, 3, 'Count me in!'),
  (8, 4, 1, 'The roof repair budget is on the agenda for the next meeting.');

INSERT INTO maintenance_requests (request_id, user_id, title, status) VALUES
  (1, 2, 'Broken light in 3rd floor hallway',  'in_progress'),
  (2, 3, 'Water leak under kitchen sink, apt 12', 'open'),
  (3, 4, 'Elevator makes a loud noise',        'open'),
  (4, 5, 'Main entrance door does not lock',   'resolved'),
  (5, 2, 'Heating radiator not working, apt 7', 'open');

INSERT INTO announcements (announcement_id, created_by, title, content) VALUES
  (1, 1, 'Elevator inspection next Tuesday', 'The annual elevator inspection takes place next Tuesday from 8:00 to 12:00. Both elevators will be out of service during this time.'),
  (2, 1, 'Water shut-off on Friday',         'Water will be shut off on Friday between 10:00 and 13:00 for pipe maintenance.'),
  (3, 1, 'Garage cleaning on Saturday',      'The underground garage will be cleaned on Saturday. Please move your vehicles by 8:00.'),
  (4, 1, 'New recycling bins',               'New recycling bins have been placed next to the main entrance.');
