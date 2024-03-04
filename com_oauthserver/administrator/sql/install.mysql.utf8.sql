CREATE TABLE IF NOT EXISTS `#__webmasterskaya_oauthserver_clients`
(
    id                    int unsigned auto_increment,
    name                  varchar(150)      not null,
    identifier            varchar(255)      not null,
    secret                varchar(255)      null,
    public                tinyint default 0 not null,
    redirect_uri          varchar(255)      null,
    allow_plain_text_pkce tinyint default 1 not null,
    PRIMARY KEY (`id`),
    UNIQUE KEY `secret` (`identifier`, `secret`),
    UNIQUE KEY `identifier` (`identifier`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;