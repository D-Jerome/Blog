CREATE TABLE post (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content TEXT(650000) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE category (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE role (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    role VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
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

CREATE TABLE user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(60) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    role_id INT UNSIGNED DEFAULT '3',
    PRIMARY KEY (id),
    CONSTRAINT fk_role
        FOREIGN KEY (role_id)
        REFERENCES  role (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ;   

CREATE TABLE comment (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    content TEXT(650000) NOT NULL,
    created_at DATETIME NOT NULL,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_user
        FOREIGN KEY (user_id)
        REFERENCES  user (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_comment_post
        FOREIGN KEY (post_id)
        REFERENCES  post (id)
        ON DELETE CASCADE
        ON UPDATE NO ACTION
);


