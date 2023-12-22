<?php

declare(strict_types=1);

require \dirname(__DIR__).'/vendor/autoload.php';

use App\Model\PDOConnection;
use Faker\Factory;
use Framework\Config;

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create('fr_FR');

$pdo = PDOConnection::getInstance(Config::getDatasource());

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE post_category');
$pdo->exec('TRUNCATE TABLE post');
$pdo->exec('TRUNCATE TABLE category');
$pdo->exec('TRUNCATE TABLE user');
$pdo->exec('TRUNCATE TABLE comment');
$pdo->exec('TRUNCATE TABLE role');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$posts = [];
$categories = [];
$comments = [];

$pdo->exec("INSERT INTO role (name) VALUES ('admin') ");
$pdo->exec("INSERT INTO role (name) VALUES ('editor') ");
$pdo->exec("INSERT INTO role (name) VALUES ('visitor') ");

$passadmin = password_hash('admin', \PASSWORD_BCRYPT);
$passeditor = password_hash('editor', \PASSWORD_BCRYPT);
$passvisitor = password_hash('visitor', \PASSWORD_BCRYPT);
$passTestInactive = password_hash('testinactive', \PASSWORD_BCRYPT);

$pdo->exec("INSERT INTO user SET firstname = '{$faker->firstName()}', lastname='{$faker->lastName()}', username='admin', password='{$passadmin}' , email='{$faker->email()}' , created_at='{$faker->date()} {$faker->time()}', role_id = '1'");

$pdo->exec("INSERT INTO user SET firstname = '{$faker->firstName()}', lastname='{$faker->lastName()}', username='editor', password='{$passeditor}' , email='{$faker->email()}', created_at='{$faker->date()} {$faker->time()}', role_id = '2'");

$pdo->exec("INSERT INTO user SET firstname = '{$faker->firstName()}', lastname='{$faker->lastName()}', username='visitor', password='{$passvisitor}' , email='{$faker->email()}', created_at='{$faker->date()} {$faker->time()}'");

$pdo->exec("INSERT INTO user SET firstname = '{$faker->firstName()}', lastname='{$faker->lastName()}', username='testinactive', password='{$passTestInactive}' , email='{$faker->email()}', created_at='{$faker->date()} {$faker->time()}', active = false ");

for ($i = 0; $i < 25; ++$i) {
    $pdo->exec(
        "
        INSERT INTO post
        SET
            name='{$faker->sentence()}',
            slug='{$faker->slug()}',
            created_at ='{$faker->date()} {$faker->time()}',
            content = '{$faker->paragraph($faker->numberBetween(50, 100))}',
            user_id = {$faker->numberBetween(1, 4)}
    "
    );
    $posts[] = $pdo->lastInsertId();
}
for ($i = 0; $i < 25; ++$i) {
    $pdo->exec(
        "
        INSERT INTO post
        SET
            name= '{$faker->sentence()}',
            slug= '{$faker->slug()}',
            created_at= '{$faker->date()} {$faker->time()}',
            content= '{$faker->paragraph($faker->numberBetween(30, 80))}',
            user_id= {$faker->numberBetween(1, 4)},
            modified_at= '{$faker->date()} {$faker->time()}',
            publish_at= '{$faker->date()} {$faker->time()}',
            publish_state= 1
    "
    );
    $posts[] = $pdo->lastInsertId();
}

for ($i = 0; $i < 5; ++$i) {
    $pdo->exec(
        "
        INSERT INTO category
        SET
            name='{$faker->word()}',
            slug='{$faker->slug()}'
    "
    );
    $categories[] = $pdo->lastInsertId();
}

foreach ($posts as $post) {
    $randomCategories = $faker->randomElements($categories, random_int(0, \count($categories)));
    foreach ($randomCategories as $category) {
        $pdo->exec(
            "
            INSERT INTO post_category
            SET
                post_id={$post} ,
                category_id={$category}
        "
        );
    }
}

for ($i = 0; $i < 25; ++$i) {
    $pdo->exec(
        "
        INSERT INTO comment
        SET
            created_at='{$faker->date()} {$faker->time()}',
            content= '{$faker->paragraph($faker->numberBetween(10, 20))}',
            post_id= {$faker->numberBetween(1, 50)},
            user_id = {$faker->numberBetween(1, 4)};"
    );
    $comments[] = $pdo->lastInsertId();

    for ($i = 0; $i < 25; ++$i) {
        $pdo->exec(
            "
            INSERT INTO comment
            SET
                created_at='{$faker->date()} {$faker->time()}',
                content= '{$faker->paragraph($faker->numberBetween(10, 20))}',
                post_id= {$faker->numberBetween(1, 50)},
                user_id = {$faker->numberBetween(1, 4)},
                publish_at='{$faker->date()} {$faker->time()}',
                publish_state = 1
            "
        );
        $comments[] = $pdo->lastInsertId();
    }
}
