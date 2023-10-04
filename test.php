<?php
$user = 'blog';
$pass= 'fq/6HP<6';
$dbh = new PDO('mysql:host=localhost;dbname=blog;port:33061', $user, $pass);
var_dump($dbh);
print_r($dbh);
$query = $dbh->query("SELECT * FROM posts");
$statement = $query->fetchAll();
var_dump($statement);
?>
