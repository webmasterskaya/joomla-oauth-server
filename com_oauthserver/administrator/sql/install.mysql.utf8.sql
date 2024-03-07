create table if not exists `#__webmasterskaya_oauthserver_access_tokens`
(
    id         int auto_increment
        primary key,
    identifier varchar(80)          not null,
    expiry     datetime             not null,
    user_id    int                  null,
    scopes     text                 null,
    client_id  int                  not null,
    revoked    tinyint(1) default 0 not null,
    constraint oauthserver_access_tokens_uk_1
        unique (identifier)
);

create table if not exists `#__webmasterskaya_oauthserver_authorization_codes`
(
    id         int auto_increment
        primary key,
    identifier varchar(80)          not null,
    expiry     datetime             not null,
    user_id    int                  null,
    scopes     text                 null,
    revoked    tinyint(1) default 0 not null,
    client_id  int                  not null,
    constraint oauthserver_authorization_codes_uk_1
        unique (identifier)
);

create table if not exists `#__webmasterskaya_oauthserver_clients`
(
    id                    int auto_increment
        primary key,
    identifier            varchar(32)       not null,
    name                  varchar(128)      not null,
    secret                varchar(128)      null,
    redirect_uris         longtext          null,
    grants                longtext          null,
    scopes                longtext          null,
    active                tinyint default 1 not null,
    public                tinyint default 0 not null,
    allow_plain_text_pkce tinyint default 1 not null,
    constraint oauthserver_clients_uk_1
        unique (identifier)
);

create table if not exists `#__webmasterskaya_oauthserver_refresh_tokens`
(
    id              int auto_increment
        primary key,
    identifier      varchar(80)          not null,
    expiry          datetime             not null,
    revoked         tinyint(1) default 0 not null,
    access_token_id int                  null,
    constraint oauthserver_refresh_tokens_uk_2
        unique (identifier)
);

