<?php

return [
    "CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(16) UNIQUE NOT NULL,
        password VARCHAR(32) NOT NULL,
        status TINYINT DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );",
    "CREATE TABLE IF NOT EXISTS players (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        user_id INT UNSIGNED NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        name VARCHAR(32) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );",
    "CREATE TABLE IF NOT EXISTS stats (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(32) NOT NULL
    );",
    "CREATE TABLE IF NOT EXISTS players_stats (
        user_id INT UNSIGNED NOT NULL,
        player_id INT UNSIGNED NOT NULL,
        stats VARCHAR(255) NOT NULL,
        PRIMARY KEY (user_id, player_id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (player_id) REFERENCES players(id)
    );",
];