DROP TABLE IF EXISTS app_users;
DROP TABLE IF EXISTS files;

CREATE TABLE app_users (
    user_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email VARCHAR(540),
    password VARCHAR(160),
    ip_address VARCHAR(32),
    joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmed BOOL
);

CREATE TABLE files (
    file_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(256),
    location VARCHAR(256),
    mime_type VARCHAR(128),
    size INT,
    _user_id INT NOT NULL,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);