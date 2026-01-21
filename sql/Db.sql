DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `game_genre`;
DROP TABLE IF EXISTS `game_platform`;
DROP TABLE IF EXISTS `genre`;
DROP TABLE IF EXISTS `platform`;
DROP TABLE IF EXISTS `game`;
DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

                        `email` VARCHAR(255) NOT NULL,
                        `name` VARCHAR(255) NOT NULL,
                        `password_hash` VARCHAR(255) NOT NULL,
                        `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `uq_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `game` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `name` VARCHAR(255) NOT NULL,
                        `publisher` VARCHAR(255) DEFAULT NULL,
                        `is_dlc` TINYINT(1) NOT NULL DEFAULT 0,
                        `is_early_access` TINYINT(1) NOT NULL DEFAULT 0,
                        `base_price_eur` DECIMAL(8,2) DEFAULT NULL,
                        `global_release_date` DATE DEFAULT NULL,
                        `description` TEXT DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `idx_game_name` (`name`),
                        KEY `idx_game_global_release_date` (`global_release_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Slovníková tabuľka platforiem
CREATE TABLE `platform` (
                            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            `name` VARCHAR(100) NOT NULL,
                            `description` TEXT DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `uq_platform_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Slovníková tabuľka žánrov
CREATE TABLE `genre` (
                         `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                         `name` VARCHAR(100) NOT NULL,
                         `description` TEXT DEFAULT NULL,
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `uq_genre_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Prepojovacia tabuľka hra : platforma (N:M) s per-platform dátumom a cenou
CREATE TABLE `game_platform` (
                                 `game_id` INT UNSIGNED NOT NULL,
                                 `platform_id` INT UNSIGNED NOT NULL,
                                 `release_date` DATE,
                                 `price_eur` DECIMAL(8,2) DEFAULT NULL,
                                 PRIMARY KEY (`game_id`, `platform_id`),

                                 KEY `idx_game_platform_game_id` (`game_id`),
                                 KEY `idx_game_platform_platform_id` (`platform_id`),
                                 KEY `idx_game_platform_platform_date` (`platform_id`, `release_date`),
                                 CONSTRAINT `fk_game_platform_game`
                                     FOREIGN KEY (`game_id`) REFERENCES `game`(`id`)
                                         ON DELETE CASCADE
                                         ON UPDATE CASCADE,
                                 CONSTRAINT `fk_game_platform_platform`
                                     FOREIGN KEY (`platform_id`) REFERENCES `platform`(`id`)
                                         ON DELETE RESTRICT
                                         ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Prepojovacia tabuľka hra : žáner (N:M)
CREATE TABLE `game_genre` (
                              `game_id` INT UNSIGNED NOT NULL,
                              `genre_id` INT UNSIGNED NOT NULL,
                              PRIMARY KEY (`game_id`, `genre_id`),
                              KEY `idx_game_genre_game_id` (`game_id`),
                              KEY `idx_game_genre_genre_id` (`genre_id`),
                              CONSTRAINT `fk_game_genre_game`
                                  FOREIGN KEY (`game_id`) REFERENCES `game`(`id`)
                                      ON DELETE CASCADE
                                      ON UPDATE CASCADE,
                              CONSTRAINT `fk_game_genre_genre`
                                  FOREIGN KEY (`genre_id`) REFERENCES `genre`(`id`)
                                      ON DELETE RESTRICT
                                      ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wishlist` (
                            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            `user_id` INT UNSIGNED NOT NULL,
                            `game_id` INT UNSIGNED NOT NULL,
                            `added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `uq_wishlist_user_game` (`user_id`, `game_id`),
                            KEY `idx_wishlist_user_id` (`user_id`),
                            KEY `idx_wishlist_game_id` (`game_id`),
                            CONSTRAINT `fk_wishlist_user`
                                FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE,
                            CONSTRAINT `fk_wishlist_game`
                                FOREIGN KEY (`game_id`) REFERENCES `game`(`id`)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE game
    ADD COLUMN image_url VARCHAR(512);