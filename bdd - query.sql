-- Enable client program to communicate with the server using utf8 character set
SET NAMES 'utf8';

DROP DATABASE IF EXISTS `blog`;
-- Set the default charset to utf8 for internationalization, use case-insensitive (ci) collation
CREATE DATABASE IF NOT EXISTS `blog` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `blog`;


CREATE TABLE category (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE role (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(60) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    role_id INT UNSIGNED DEFAULT '3',
    active BOOL DEFAULT TRUE
    PRIMARY KEY (id),
    CONSTRAINT fk_role
        FOREIGN KEY (role_id)
        REFERENCES  role (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);

CREATE TABLE post (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content TEXT(650000) NOT NULL,
    created_at DATETIME NOT NULL,
    modified_at DATETIME,
    user_id INT UNSIGNED NOT NULL,
    publish_state BOOL NOT NULL DEFAULT FALSE,
    publish_at DATETIME,
    PRIMARY KEY (id),
    CONSTRAINT fk_user_post
        FOREIGN KEY (user_id)
        REFERENCES  user (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_user_publish_post
        FOREIGN KEY (publish_user_id)
        REFERENCES  user (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);

CREATE TABLE post_category (
    post_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id,category_id),
    CONSTRAINT fk_post
        FOREIGN KEY (post_id)
        REFERENCES  post (id)
        ON DELETE CASCADE
        ON UPDATE RESTRICT,
    CONSTRAINT fk_category
        FOREIGN KEY (category_id)
        REFERENCES  category (id)
        ON DELETE CASCADE
        ON UPDATE RESTRICT
) ;


CREATE TABLE comment (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    content TEXT(650000) NOT NULL,
    created_at DATETIME NOT NULL,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    modified_at DATETIME,
    publish_state BOOL NOT NULL DEFAULT FALSE,
    publish_at DATETIME,
    PRIMARY KEY (id),
    CONSTRAINT fk_user_comment
        FOREIGN KEY (user_id)
        REFERENCES  user (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_post_comment
        FOREIGN KEY (post_id)
        REFERENCES  post (id)
        ON DELETE CASCADE
        ON UPDATE NO ACTION,
    CONSTRAINT fk_user_publish_comment
        FOREIGN KEY (publish_user_id)
        REFERENCES  user (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);
