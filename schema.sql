DROP DATABASE IF EXISTS doingsdone;

CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL UNIQUE,
    name VARCHAR(128) NOT NULL,
    password VARCHAR(255) NOT NULL
);


CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,

    UNIQUE (user_id, name),

    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT(1) NOT NULL DEFAULT 0,
    name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255),
    deadline DATE,
    user_id INT NOT NULL,
    project_id INT NOT NULL,

    FULLTEXT (name),
    INDEX (deadline),

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
);