CREATE DATABASE php_mvc_dev_db;

CREATE DATABASE php_mvc_dev_db_test;

CREATE TABLE users(
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE sessions(
    id VARCHAR(255) PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL
);

ALTER TABLE sessions
ADD CONSTRAINT fk_session_user
FOREIGN KEY (user_id) REFERENCES users(id);