CREATE TABLE IF NOT EXISTS `#__webmasterskaya_oauthserver_clients`
(
    `id`           int unsigned NOT NULL AUTO_INCREMENT,
    `client_name`  varchar(150) NOT NULL,
    `client_token` varchar(255) NOT NULL,
    `client_id`    varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `client_token` (`client_token`),
    UNIQUE KEY `client_id` (`client_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;