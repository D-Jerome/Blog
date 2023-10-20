<?php
require(dirname(__DIR__) . '/vendor/autoload.php');

use Faker\Factory;
use App\Model\PDOConnection;
use Framework\Application;

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create('fr_FR');

$pdo = PDOConnection::getInstance(Application::getDatasource());

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

$pdo->exec("INSERT INTO role (role) VALUES ('admin') ");
$pdo->exec("INSERT INTO role (role) VALUES ('editor') ");
$pdo->exec("INSERT INTO role (role) VALUES ('visitor') ");

$passadmin = password_hash('admin', PASSWORD_BCRYPT);
$passeditor = password_hash('editor', PASSWORD_BCRYPT);
$passvisitor = password_hash('visitor', PASSWORD_BCRYPT);

$pdo->exec("INSERT INTO user SET username='admin', password='$passadmin' , email='{$faker->email()}, ' , created_at='{$faker->date()} {$faker->time()}'");

$pdo->exec("INSERT INTO user SET username='editor', password='$passeditor' , email='{$faker->email()}, ' , created_at='{$faker->date()} {$faker->time()}'");

$pdo->exec("INSERT INTO user SET username='visitor', password='$passvisitor' , email='{$faker->email()}, ' , created_at='{$faker->date()} {$faker->time()}'");

for ($i = 0; $i < 50; $i++) {
    $pdo->exec("INSERT INTO post SET name='{$faker->sentence()}', slug='{$faker->slug()}', created_at='{$faker->date()} {$faker->time()}', content='{$faker->paragraphs(rand(3, 15), true)}', user_id='{$faker->numberBetween(1, 3)}'");
    $posts[] = $pdo->lastInsertId();
}

for ($i = 0; $i < 5; $i++) {
    $pdo->exec("INSERT INTO category SET name='{$faker->word(3)}', slug='{$faker->slug()}'");
    $categories[] = $pdo->lastInsertId();
}

foreach ($posts as $post) {
    $randomCategories = $faker->randomElements($categories, rand(0, count($categories)));
    foreach ($randomCategories as $category) {
        $pdo->exec("INSERT INTO post_category SET post_id=$post , category_id=$category");
    }
}

for ($i = 0; $i < 50; $i++) {
    $pdo->exec("INSERT INTO comment SET created_at='{$faker->date()} {$faker->time()}', content='{$faker->paragraphs(rand(1, 4), true)}', post_id='{$faker->numberBetween(1, 50)}', user_id = '{$faker->numberBetween(1, 3)}';");
    $comments[] = $pdo->lastInsertId();
}
